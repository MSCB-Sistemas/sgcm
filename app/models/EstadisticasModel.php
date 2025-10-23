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

    public function getTotalDeudosMorosos()
    {
        $fecha_actual = date('Y-m-d');

        $stmt = $this->db->prepare("SELECT COUNT(*) 
                                        FROM pago p
                                        INNER JOIN deudo d ON p.id_deudo = d.id_deudo
                                        WHERE p.fecha_vencimiento < :fecha_actual");
        
        $stmt->bindParam(":fecha_actual", $fecha_actual, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
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

            INNER JOIN difunto d ON origen_final.id_difunto = d.id_difunto

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
        $columnas_ordenables = [
            'p.id_parcela', 
            'tp.nombre_parcela',
            'd.nombre', 
            'd.apellido', 
            'd.dni', 
            'pgo.total', 
            'pgo.fecha_pago', 
            'pgo.fecha_vencimiento'
        ];

        $sql_base = "
            FROM pago pgo
            INNER JOIN parcela p ON pgo.id_parcela = p.id_parcela
            INNER JOIN deudo d ON pgo.id_deudo = d.id_deudo
            LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
            WHERE pgo.fecha_pago IS NOT NULL AND pgo.fecha_pago != '0000-00-00'
        ";

        $sql_where = "";
        $where_conditions = [];
        $pdo_params = [];

        if (!empty($params['search']['value'])) {
            $searchValue = '%' . $params['search']['value'] . '%';
            $where_conditions[] = "(d.nombre LIKE ? OR d.apellido LIKE ? OR d.dni LIKE ? OR p.id_parcela LIKE ?)";
            array_push($pdo_params, $searchValue, $searchValue, $searchValue, $searchValue);
        }
        
        if (!empty($params['fecha_inicio']) && !empty($params['fecha_fin'])) {
            $where_conditions[] = "DATE(pgo.fecha_pago) BETWEEN ? AND ?";
            array_push($pdo_params, $params['fecha_inicio'], $params['fecha_fin']);
        }

        if (!empty($where_conditions)) {
            $sql_where = " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql_order = " ORDER BY " . $columnas_ordenables[intval($params['order'][0]['column'])] . " " . $params['order'][0]['dir'];
        $sql_limit = " LIMIT " . intval($params['start']) . ", " . intval($params['length']);

        $stmt_total_filtrado = $this->db->prepare("SELECT COUNT(pgo.id_pago) as total " . $sql_base . $sql_where);
        $stmt_total_filtrado->execute($pdo_params);
        $total_filtrado = $stmt_total_filtrado->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "SELECT p.id_parcela, 
                        tp.nombre_parcela as tipo_parcela,
                        d.nombre as nombre_titular, d.apellido as apellido_titular, d.dni, 
                        pgo.total as monto, pgo.fecha_pago as fecha_venta, pgo.fecha_vencimiento "
                . $sql_base . $sql_where . $sql_order . $sql_limit;
        
        $stmt_datos = $this->db->prepare($query);
        $stmt_datos->execute($pdo_params);
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

    public function getDeudosMorososAjax($params)
    {
        $fecha_actual = date('Y-m-d');
        
        $columnas = [
            'p.id_parcela',
            'd.dni',
            'd.nombre',
            'd.apellido',
            'p.fecha_vencimiento',
            'p.total'
        ];

        $sql_base = "FROM pago p INNER JOIN deudo d ON p.id_deudo = d.id_deudo";

        $where_base = " WHERE p.id_pago IN (
                            -- This subquery finds the most recent payment ID for each combination.
                            SELECT MAX(p_inner.id_pago)
                            FROM pago p_inner
                            GROUP BY p_inner.id_deudo, p_inner.id_parcela
                        ) AND p.fecha_vencimiento < :fecha_actual";

        $stmt_total = $this->db->prepare("SELECT COUNT(p.id_pago) {$sql_base}{$where_base}");
        $stmt_total->bindParam(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
        $stmt_total->execute();
        $recordsTotal = $stmt_total->fetchColumn();

        $where_busqueda = "";
        if (!empty($params['search']['value'])) {
            $valor_busqueda = '%' . $params['search']['value'] . '%';
            $where_busqueda .= " AND (";
            $where_busqueda .= " p.id_parcela LIKE :busqueda";
            $where_busqueda .= " OR d.dni LIKE :busqueda";
            $where_busqueda .= " OR d.nombre LIKE :busqueda";
            $where_busqueda .= " OR d.apellido LIKE :busqueda";
            $where_busqueda .= " )";
        }

        $stmt_filtrado = $this->db->prepare("SELECT COUNT(p.id_pago) {$sql_base}{$where_base}{$where_busqueda}");
        $stmt_filtrado->bindParam(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
        if (!empty($params['search']['value'])) {
            $stmt_filtrado->bindParam(':busqueda', $valor_busqueda, PDO::PARAM_STR);
        }
        $stmt_filtrado->execute();
        $recordsFiltered = $stmt_filtrado->fetchColumn();

        $sql_final = "SELECT p.id_parcela, p.id_deudo, p.fecha_vencimiento, p.total, d.dni, d.nombre, d.apellido {$sql_base}{$where_base}{$where_busqueda}";

        if (isset($params['order']) && count($params['order'])) {
            $columna_orden = $columnas[$params['order'][0]['column']];
            $direccion_orden = $params['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            $sql_final .= " ORDER BY {$columna_orden} {$direccion_orden}";
        }

        if (isset($params['start']) && $params['length'] != -1) {
            $sql_final .= " LIMIT :start, :length";
        }

        $stmt_final = $this->db->prepare($sql_final);
        $stmt_final->bindParam(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
        
        if (!empty($params['search']['value'])) {
            $stmt_final->bindParam(':busqueda', $valor_busqueda, PDO::PARAM_STR);
        }
        
        if (isset($params['start']) && $params['length'] != -1) {
            $stmt_final->bindValue(':start', (int) $params['start'], PDO::PARAM_INT);
            $stmt_final->bindValue(':length', (int) $params['length'], PDO::PARAM_INT);
        }
        
        $stmt_final->execute();
        $data = $stmt_final->fetchAll(PDO::FETCH_ASSOC);

        return [
            "draw"            => intval($params['draw']),
            'recordsTotal'    => intval($recordsTotal),
            'recordsFiltered' => intval($recordsFiltered),
            'data'            => $data
        ];
    }
}
?>