<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

class OperacionModel
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function crearNuevoPago($id_deudo, $id_parcela, $id_tipo_operacion, $fecha_pago, $fecha_vencimiento, $importe, $recargo, $total, $id_usuario)
    {
        $sql = "INSERT INTO pago (id_deudo, id_parcela, id_tipo_operacion, fecha_pago, fecha_vencimiento, importe, recargo, total, id_usuario)
                VALUES (:id_deudo, :id_parcela, :id_tipo_operacion, :fecha_pago, :fecha_vencimiento, :importe, :recargo, :total, :id_usuario)";

        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':id_deudo', $id_deudo, PDO::PARAM_INT);
        $stmt->bindValue(':id_parcela', $id_parcela, PDO::PARAM_INT);
        $stmt->bindValue(':id_tipo_operacion', $id_tipo_operacion, PDO::PARAM_INT); // Se añade el nuevo parámetro
        $stmt->bindValue(':fecha_pago', $fecha_pago);
        $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento);
        $stmt->bindValue(':importe', $importe);
        $stmt->bindValue(':recargo', $recargo);
        $stmt->bindValue(':total', $total);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function verificarParcelaOcupada($id_parcela)
    {
        $sql = "SELECT COUNT(*) FROM ubicacion_difunto 
                WHERE id_parcela = :id_parcela AND (fecha_retiro IS NULL OR fecha_retiro = '0000-00-00')";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute(['id_parcela' => $id_parcela]);
        
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerUbicacionActual($id_difunto)
    {
        $sql = "SELECT * FROM ubicacion_difunto 
                WHERE id_difunto = :id_difunto AND (fecha_retiro IS NULL OR fecha_retiro = '0000-00-00') 
                ORDER BY fecha_ingreso DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarFechaRetiro($id_ubicacion_difunto, $fecha_retiro)
    {
        $sql = "UPDATE ubicacion_difunto SET fecha_retiro = :fecha_retiro WHERE id_ubicacion_difunto = :id_ubicacion";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'fecha_retiro' => $fecha_retiro, 
            'id_ubicacion' => $id_ubicacion_difunto
        ]);
    }

    public function crearNuevaUbicacion($id_difunto, $id_parcela, $fecha_ingreso)
    {
        $sql = "INSERT INTO ubicacion_difunto (id_difunto, id_parcela, fecha_ingreso)
                VALUES (:id_difunto, :id_parcela, :fecha_ingreso)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_difunto' => $id_difunto, 
            'id_parcela' => $id_parcela, 
            'fecha_ingreso' => $fecha_ingreso
        ]);
    }

    public function obtenerUltimoPagoVencido($id_parcela)
    {
        $fecha_actual = date('Y-m-d');
        $sql = "SELECT * FROM pago 
                WHERE id_parcela = :id_parcela AND fecha_vencimiento < :fecha_actual
                ORDER BY fecha_vencimiento DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_parcela' => $id_parcela, 'fecha_actual' => $fecha_actual]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfLibreDeuda($id_parcela)
    {
        $sql = "SELECT
                    p.id_parcela, p.hilera, p.seccion,
                    tp.nombre_parcela as tipo_parcela,
                    CONCAT(d.apellido, ', ', d.nombre) as deudo,
                    d.dni as dni_deudo,
                    (SELECT GROUP_CONCAT(CONCAT(dif.nombre, ' ', dif.apellido) SEPARATOR ', ') 
                    FROM ubicacion_difunto ud JOIN difunto dif ON ud.id_difunto = dif.id_difunto
                    WHERE ud.id_parcela = p.id_parcela AND ud.fecha_retiro IS NULL) as difunto,
                    (SELECT MAX(pg.fecha_vencimiento) FROM pago pg WHERE pg.id_parcela = p.id_parcela) as fecha_vencimiento
                FROM parcela p
                JOIN deudo d ON p.id_deudo = d.id_deudo
                LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                WHERE p.id_parcela = :id_parcela";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_parcela' => $id_parcela]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>