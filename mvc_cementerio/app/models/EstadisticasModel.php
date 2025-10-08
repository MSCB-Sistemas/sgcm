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

    public function getDefuncionesEntreFechas($fecha_inicio_defuncion, $fecha_fin_defuncion)
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $sql = "SELECT d.*, 
               s.descripcion AS sexo, 
               n.nacionalidad AS nacionalidad,
               ec.descripcion AS estado_civil,
               CONCAT(deu.nombre, ' ', deu.apellido) AS deudo
        FROM difunto d
        INNER JOIN sexo s ON d.id_sexo = s.id_sexo
        LEFT JOIN nacionalidades n ON d.id_nacionalidad = n.id_nacionalidad
        LEFT JOIN estado_civil ec ON d.id_estado_civil = ec.id_estado_civil
        LEFT JOIN deudo deu ON d.id_deudo = deu.id_deudo
        WHERE DATE(d.fecha_fallecimiento) BETWEEN :inicio AND :fin";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':inicio', $fecha_inicio_defuncion);
        $stmt->bindValue(':fin', $fecha_fin_defuncion);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalDefuncionesEntreFechas($fecha_inicio_defuncion, $fecha_fin_defuncion)
    {
        try {
            $this->establecerFechasPorDefecto($fecha_inicio_defuncion, $fecha_fin_defuncion);

            $sql = "SELECT COUNT(*) as total FROM difunto 
                    WHERE DATE(fecha_fallecimiento) BETWEEN :inicio AND :fin";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':inicio', $fecha_inicio_defuncion);
            $stmt->bindValue(':fin', $fecha_fin_defuncion);

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($resultado['total'])) {
                return (int) $resultado['total'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            error_log("Error en getTotalDefuncionesEntreFechas: " . $e->getMessage());
            return 0;
        }
    }

    public function getParcelasVendidas($fecha_inicio_parcela, $fecha_fin_parcela)
    {
        $this->establecerFechasPorDefecto($fecha_inicio_parcela, $fecha_fin_parcela);

        try {
            $sql = "
                SELECT p.id_parcela, p.id_tipo_parcela, d.nombre, d.apellido, d.dni, pgo.total as monto, pgo.fecha_pago as fecha_venta, pgo.fecha_vencimiento
                FROM pago pgo
                INNER JOIN parcela p ON pgo.id_parcela = p.id_parcela
                INNER JOIN deudo d ON pgo.id_deudo = d.id_deudo
                WHERE DATE(pgo.fecha_pago) BETWEEN :inicio AND :fin
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':inicio', $fecha_inicio_parcela);
            $stmt->bindValue(':fin', $fecha_fin_parcela);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en getParcelasVendidas: " . $e->getMessage());
            return [];
        }
    }

    public function getParcelasVendidasPorDatosParcela($filtros = [])
    {
        try {
            $sql = "
                SELECT p.id_parcela, p.id_tipo_parcela, d.nombre, d.apellido, d.dni, pgo.total as monto, pgo.fecha_pago as fecha_venta, pgo.fecha_vencimiento
                FROM pago pgo
                INNER JOIN parcela p ON pgo.id_parcela = p.id_parcela
                INNER JOIN deudo d ON pgo.id_deudo = d.id_deudo
                WHERE 1=1
            ";

            $params = [];

            if (!empty($filtros['numero_ubicacion'])) {
                $sql .= " AND p.numero_ubicacion = :numero_ubicacion";
                $params[':numero_ubicacion'] = $filtros['numero_ubicacion'];
            }

            if (!empty($filtros['id_tipo_parcela'])) {
                $sql .= " AND p.id_tipo_parcela = :id_tipo_parcela";
                $params[':id_tipo_parcela'] = $filtros['id_tipo_parcela'];
            }

            if (!empty($filtros['seccion'])) {
                $sql .= " AND p.seccion = :seccion";
                $params[':seccion'] = $filtros['seccion'];
            }

            if (!empty($filtros['fraccion'])) {
                $sql .= " AND p.fraccion = :fraccion";
                $params[':fraccion'] = $filtros['fraccion'];
            }

            if (!empty($filtros['nivel'])) {
                $sql .= " AND p.nivel = :nivel";
                $params[':nivel'] = $filtros['nivel'];
            }

            if (!empty($filtros['id_orientacion'])) {
                $sql .= " AND p.id_orientacion = :id_orientacion";
                $params[':id_orientacion'] = $filtros['id_orientacion'];
            }

            if (!empty($filtros['hilera'])) {
                $sql .= " AND p.hilera = :hilera";
                $params[':hilera'] = $filtros['hilera'];
            }

            $sql .= " ORDER BY pgo.fecha_pago DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getParcelasVendidasPorDatosParcela: " . $e->getMessage());
            return [];
        }
    }

    public function getDifuntosTrasladados($fecha_inicio_traslado, $fecha_fin_traslado,)
    {
        $this->establecerFechasPorDefecto($fecha_inicio_traslado, $fecha_fin_traslado);

        $sql = "SELECT 
                d.nombre, d.apellido, d.dni, d.fecha_fallecimiento, 
                u.fecha_retiro,
                u.id_parcela AS parcela_origen, 
                u2.id_parcela AS parcela_destino, 
                u2.fecha_ingreso AS fecha_ingreso_destino
            FROM difunto d
            INNER JOIN ubicacion_difunto u ON d.id_difunto = u.id_difunto
            INNER JOIN ubicacion_difunto u2 ON u2.id_difunto = d.id_difunto
                AND u2.fecha_ingreso = (
                    SELECT MIN(u3.fecha_ingreso)
                    FROM ubicacion_difunto u3
                    WHERE u3.id_difunto = d.id_difunto
                    AND u3.fecha_ingreso > u.fecha_retiro
                    AND u3.fecha_ingreso != '0000-00-00'
                )
            WHERE u.fecha_retiro != '0000-00-00'
            AND DATE(u.fecha_retiro) BETWEEN :inicio_traslado AND :fin_traslado";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':inicio_traslado', $fecha_inicio_traslado);
        $stmt->bindValue(':fin_traslado', $fecha_fin_traslado);
        
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
}
?>