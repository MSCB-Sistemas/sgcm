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

    public function getTotalDeudosMorosos() {
        try {
            $sql = "SELECT COUNT(DISTINCT p.id_parcela) 
                    FROM pago p
                    INNER JOIN (
                        SELECT id_parcela, MAX(id_pago) as max_id 
                        FROM pago GROUP BY id_parcela
                    ) sub ON p.id_pago = sub.max_id
                    INNER JOIN ubicacion_difunto ud ON p.id_parcela = ud.id_parcela
                    WHERE (ud.fecha_retiro IS NULL OR ud.fecha_retiro = '0000-00-00')
                    AND p.fecha_vencimiento < CURRENT_DATE";
            
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en getTotalDeudosMorosos: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalParcelasOcupadas()
    {
        try {
            $sql = "SELECT COUNT(DISTINCT id_parcela) as total 
                    FROM (
                        SELECT id_parcela 
                        FROM ubicacion_difunto 
                        WHERE (fecha_retiro IS NULL OR fecha_retiro = '0000-00-00')
                        
                        UNION 
                        
                        SELECT pa.id_parcela 
                        FROM pago pa
                        INNER JOIN (
                            SELECT id_parcela, MAX(id_pago) as max_id 
                            FROM pago GROUP BY id_parcela
                        ) sub ON pa.id_pago = sub.max_id
                        WHERE pa.fecha_vencimiento >= CURRENT_DATE
                    ) AS todas_las_ocupadas";

            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ($resultado['total'] ?? 0);

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

    public function getTotalParcelas()
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
            error_log("Error en getTotalParcelas: " . $e->getMessage());
            return 0;
        }
    }

    public function getDeudaTotalEstimada()
    {
        try {
            $sql = "SELECT SUM(p.total) as total_deuda 
                    FROM pago p
                    INNER JOIN (
                        SELECT id_parcela, MAX(id_pago) as max_id 
                        FROM pago GROUP BY id_parcela
                    ) sub ON p.id_pago = sub.max_id
                    INNER JOIN ubicacion_difunto ud ON p.id_parcela = ud.id_parcela
                    WHERE (ud.fecha_retiro IS NULL OR ud.fecha_retiro = '0000-00-00')
                    AND p.fecha_vencimiento < CURRENT_DATE";
            
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float) ($resultado['total_deuda'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error en getDeudaTotalEstimada: " . $e->getMessage());
            return 0;
        }
    }

    public function getIngresosMesActual()
    {
        try {
            $sql = "SELECT SUM(total) as ingresos 
                    FROM pago 
                    WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE()) 
                    AND YEAR(fecha_pago) = YEAR(CURRENT_DATE())";
            
            $stmt = $this->db->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float) ($resultado['ingresos'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error en getIngresosMesActual: " . $e->getMessage());
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

    public function getDeudosMorososAjax($params)
    {
        $columnas = ['p.id_parcela', 'd.dni', 'd.nombre', 'd.apellido', 'p.fecha_vencimiento', 'p.total'];

        $sql_base_morosos = "
            FROM pago p
            INNER JOIN (
                SELECT id_parcela, MAX(id_pago) as max_id_pago
                FROM pago
                GROUP BY id_parcela
            ) sub ON p.id_pago = sub.max_id_pago
            INNER JOIN deudo d ON p.id_deudo = d.id_deudo
            INNER JOIN ubicacion_difunto ud ON p.id_parcela = ud.id_parcela
            WHERE (ud.fecha_retiro IS NULL OR ud.fecha_retiro = '0000-00-00')
            AND p.fecha_vencimiento < CURRENT_DATE
        ";

        $where_search = "";
        $pdo_params = [];
        if (!empty($params['search']['value'])) {
            $search = '%' . $params['search']['value'] . '%';
            $where_search = " AND (d.nombre LIKE ? OR d.apellido LIKE ? OR d.dni LIKE ? OR p.id_parcela LIKE ?)";
            array_push($pdo_params, $search, $search, $search, $search);
        }

        $stmt_total_real = $this->db->query("SELECT COUNT(*) " . $sql_base_morosos);
        $recordsTotal = $stmt_total_real->fetchColumn() ?? 0;

        $stmt_filtrado = $this->db->prepare("SELECT COUNT(*) " . $sql_base_morosos . $where_search);
        $stmt_filtrado->execute($pdo_params);
        $recordsFiltered = $stmt_filtrado->fetchColumn() ?? 0;

        $col_idx = intval($params['order'][0]['column']);
        $col_dir = ($params['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';
        $order_by = " ORDER BY " . ($columnas[$col_idx] ?? 'p.fecha_vencimiento') . " " . $col_dir;
        
        $start = intval($params['start']);
        $length = intval($params['length']);
        $limit = " LIMIT $start, $length";

        $sql = "SELECT p.id_pago, p.id_deudo, p.id_parcela, d.dni, d.nombre, d.apellido, p.fecha_vencimiento, p.total " 
            . $sql_base_morosos . $where_search . $order_by . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($pdo_params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $data
        ];
    }

    public function getReporteIntegralAjax($params)
    {
        $columnas = [
            'p.id_pago', 'dif.apellido', 'p.id_parcela', 'deu.apellido', 
            'p.total', 'p.fecha_pago', 'p.fecha_vencimiento'
        ];

        $sql_base = "
            FROM pago p
            INNER JOIN deudo deu ON p.id_deudo = deu.id_deudo
            INNER JOIN parcela par ON p.id_parcela = par.id_parcela
            INNER JOIN tipo_parcela tp ON par.id_tipo_parcela = tp.id_tipo_parcela
            LEFT JOIN ubicacion_difunto ud ON par.id_parcela = ud.id_parcela 
                AND (ud.fecha_retiro IS NULL OR ud.fecha_retiro = '0000-00-00')
            LEFT JOIN difunto dif ON ud.id_difunto = dif.id_difunto
        ";

        $where = " WHERE 1=1 ";
        $pdo_params = [];

        if (!empty($params['search']['value'])) {
            $search = '%' . $params['search']['value'] . '%';
            $where .= " AND (dif.nombre LIKE ? OR dif.apellido LIKE ? OR deu.apellido LIKE ? OR p.id_parcela LIKE ? OR tp.nombre_parcela LIKE ?)";
            array_push($pdo_params, $search, $search, $search, $search, $search);
        }

        $stmt_total = $this->db->prepare("SELECT COUNT(*) as total " . $sql_base . $where);
        $stmt_total->execute($pdo_params);
        $total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $col_idx = intval($params['order'][0]['column']);
        $order_by = " ORDER BY " . ($columnas[$col_idx] ?? 'p.id_pago') . " " . $params['order'][0]['dir'];
        $limit = " LIMIT " . intval($params['start']) . ", " . intval($params['length']);

        $sql = "SELECT p.id_pago, p.id_parcela, p.total, p.fecha_pago, p.fecha_vencimiento,
                    deu.nombre as deudo_nombre, deu.apellido as deudo_apellido,
                    dif.nombre as difunto_nombre, dif.apellido as difunto_apellido,
                    tp.nombre_parcela as tipo_nombre,
                    par.seccion, par.hilera, par.numero_ubicacion, par.nivel,
                    (SELECT COUNT(*) FROM ubicacion_difunto ud2 
                    WHERE ud2.id_parcela = p.id_parcela 
                    AND (ud2.fecha_retiro IS NULL OR ud2.fecha_retiro = '0000-00-00')) as tiene_difunto,
                    (SELECT MAX(id_pago) FROM pago WHERE id_parcela = p.id_parcela) as ultimo_pago_id " 
                . $sql_base . $where . $order_by . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($pdo_params);
        
        return [
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($total),
            "recordsFiltered" => intval($total),
            "data"            => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
}    
?>