<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

class EstadisticasModel extends Control
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    private function establecerFechasPorDefecto(&$fecha_inicio, &$fecha_fin)
    {
        if (!$fecha_fin) {
            $fecha_fin = date('Y-m-d');
        }

        if (!$fecha_inicio) {
            $fecha_inicio = date('Y-m-d', strtotime('-30 days', strtotime($fecha_fin)));
        }
    }

    public function getTotalDifuntos()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM difunto");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($resultado['total'])) {
                return (int) $resultado['total'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            error_log("Error en getTotalDifuntos: " . $e->getMessage());
            return 0;
        }
    }

    public function getDeudosMorosos()
    {
        $fecha_actual = date('Y-m-d');

        $stmt = $this->db->prepare("SELECT p.*, d.dni, d.nombre, d.apellido FROM pago p
                                        INNER JOIN deudo d ON p.id_deudo = d.id_deudo
                                        WHERE p.fecha_vencimiento < :fecha_actual
                                        ORDER BY p.id_parcela ASC");
        $stmt->bindParam(":fecha_actual", $fecha_actual, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalParcelasOcupadas()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM parcela");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($resultado['total'])) {
                return (int) $resultado['total'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            error_log("Error en getTotalParcelasOcupadas: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalTraslados()
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM ubicacion_difunto WHERE fecha_retiro != '0000-00-00'");
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($resultado['total'])) {
                return (int) $resultado['total'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            error_log("Error en getTotalTraslados: " . $e->getMessage());
            return 0;
        }
    }

    public function getTrasladosAjax($params) {
        $columnas_ordenables = [
            'd.nombre', 'd.apellido', 'd.dni', 'd.fecha_fallecimiento', 
            'origen_final.fecha_retiro', 
            'origen_final.id_parcela',
            'destino_final.id_parcela'
        ];

        $sql_base = "
            -- 1. Empezamos con la lista de los últimos traslados (garantiza una fila por difunto)
            FROM (
                SELECT u_origen.id_difunto, u_origen.id_parcela, u_origen.fecha_retiro
                FROM ubicacion_difunto u_origen
                INNER JOIN (
                    SELECT id_difunto, MAX(fecha_retiro) as max_fecha
                    FROM ubicacion_difunto
                    WHERE fecha_retiro IS NOT NULL AND fecha_retiro != '0000-00-00'
                    GROUP BY id_difunto
                ) AS ultimos ON u_origen.id_difunto = ultimos.id_difunto AND u_origen.fecha_retiro = ultimos.max_fecha
            ) AS origen_final

            -- 2. Unimos para obtener los datos del difunto
            INNER JOIN difunto d ON origen_final.id_difunto = d.id_difunto

            -- 3. Unimos para encontrar la ubicación actual (la que no tiene fecha de retiro)
            LEFT JOIN ubicacion_difunto AS destino_final 
                ON origen_final.id_difunto = destino_final.id_difunto 
                AND (destino_final.fecha_retiro IS NULL OR destino_final.fecha_retiro = '0000-00-00')
        ";

        $sql_where = "";
        $where_conditions = [];
        $pdo_params = [];

        if (!empty($params['search']['value'])) {
            $searchValue = '%' . $params['search']['value'] . '%';
            $where_conditions[] = "(d.nombre LIKE ? OR d.apellido LIKE ? OR d.dni LIKE ?)";
            array_push($pdo_params, $searchValue, $searchValue, $searchValue);
        }
        
        if (!empty($params['fecha_inicio']) && !empty($params['fecha_fin'])) {
            $where_conditions[] = "DATE(origen_final.fecha_retiro) BETWEEN ? AND ?";
            array_push($pdo_params, $params['fecha_inicio'], $params['fecha_fin']);
        }

        if (!empty($where_conditions)) {
            $sql_where = " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql_order = " ORDER BY " . $columnas_ordenables[$params['order'][0]['column']] . " " . $params['order'][0]['dir'];
        $sql_limit = " LIMIT " . intval($params['start']) . ", " . intval($params['length']);

        $stmt_total_filtrado = $this->db->prepare("SELECT COUNT(DISTINCT d.id_difunto) as total " . $sql_base . $sql_where);
        $stmt_total_filtrado->execute($pdo_params);
        $total_filtrado = $stmt_total_filtrado->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "SELECT d.nombre, d.apellido, d.dni, d.fecha_fallecimiento, 
                        origen_final.fecha_retiro, 
                        origen_final.id_parcela AS parcela_origen, 
                        destino_final.id_parcela AS parcela_destino "
                . $sql_base . $sql_where . $sql_order . $sql_limit;
        
        $stmt_datos = $this->db->prepare($query);
        $stmt_datos->execute($pdo_params);
        $datos = $stmt_datos->fetchAll(PDO::FETCH_ASSOC);

        $stmt_total_general = $this->db->query("SELECT COUNT(DISTINCT id_difunto) as total FROM ubicacion_difunto WHERE fecha_retiro IS NOT NULL AND fecha_retiro != '0000-00-00'");
        $total_general = $stmt_total_general->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($total_general),
            "recordsFiltered" => intval($total_filtrado),
            "data"            => $datos
        ];
    }

    public function getParcelasVendidasAjax($params) {
        $columnas = [
            'pgo.id_parcela', 'tp.descripcion', 'd.nombre', 'd.apellido', 'd.dni', 
            'pgo.total', 'pgo.fecha_pago', 'pgo.fecha_vencimiento'
        ];

        $sql_base = "FROM pago pgo
                    INNER JOIN parcela p ON pgo.id_parcela = p.id_parcela
                    INNER JOIN deudo d ON pgo.id_deudo = d.id_deudo
                    LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                    WHERE pgo.fecha_pago IS NOT NULL AND pgo.fecha_pago != '0000-00-00'";

        $sql_where = "";
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $sql_where .= " AND (d.nombre LIKE '%$searchValue%' OR d.apellido LIKE '%$searchValue%' OR d.dni LIKE '%$searchValue%' OR p.id_parcela LIKE '%$searchValue%')";
        }

        if (!empty($params['fecha_inicio']) && !empty($params['fecha_fin'])) {
            $sql_where .= " AND DATE(pgo.fecha_pago) BETWEEN '" . $params['fecha_inicio'] . "' AND '" . $params['fecha_fin'] . "'";
        }

        $sql_order = " ORDER BY " . $columnas[$params['order'][0]['column']] . " " . $params['order'][0]['dir'];
        $sql_limit = " LIMIT " . intval($params['start']) . ", " . intval($params['length']);

        $stmt_total_filtrado = $this->db->query("SELECT COUNT(pgo.id_pago) as total " . $sql_base . $sql_where);
        $total_filtrado = $stmt_total_filtrado->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "SELECT p.id_parcela, tp.descripcion as tipo_parcela, d.nombre as nombre_titular, d.apellido as apellido_titular, d.dni, 
                        pgo.total as monto, pgo.fecha_pago as fecha_venta, pgo.fecha_vencimiento "
                . $sql_base . $sql_where . $sql_order . $sql_limit;
        
        $stmt_datos = $this->db->query($query);
        $datos = $stmt_datos->fetchAll(PDO::FETCH_ASSOC);

        $stmt_total_general = $this->db->query("SELECT COUNT(pgo.id_pago) as total " . $sql_base);
        $total_general = $stmt_total_general->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($total_general),
            "recordsFiltered" => intval($total_filtrado),
            "data"            => $datos
        ];
    }
}
?>