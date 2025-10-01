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

    public function getAllTraslados()
    {
        $sql = "SELECT ubi.id_ubicacion_difunto,
                CONCAT(de.dni,' - ', de.nombre, de.apellido) AS Deudo,
                CONCAT(di.dni,' - ', di.nombre, di.apellido) AS Difunto,
                CONCAT(pa.id_parcela,' - ', pa.id_tipo_parcela,' - ', pa.numero_ubicacion,' - ', pa.hilera,'/', pa.seccion,'/', pa.fraccion,'/', pa.nivel) AS Parcela,
                CONCAT(pg.id_deudo, pg.id_parcela, pg.fecha_pago, pg.total, pg.fecha_vencimiento) AS Pago
                FROM ubicacion_difunto ubi
                JOIN difunto di ON ubi.id_difunto = di.id_difunto
                JOIN deudo de ON di.id_deudo = de.id_deudo
                JOIN parcela pa ON ubi.id_parcela = pa.id_parcela
                JOIN pago pg ON pa.id_parcela = pg.id_parcela";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function verificarParcelaOcupada($id_parcela)
    {
        $sql = "SELECT COUNT(*) as ocupada FROM ubicacion_difunto WHERE id_parcela = :id_parcela";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_parcela' => $_POST['id_parcela']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['ocupada'] > 0;
    }

    public function obtenerUbicacionActual($id_difunto)
    {
        $sql = "SELECT ubi.*, pg.id_pago
                FROM ubicaion_difunto ubi
                JOIN pago pg ON ubi.id_parcela = pg.id_parcela
                WHERE ubi.id_difunto = :id_difunto
                ORDER BY ubi.fecha_ubicacion DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_difunto', $id_difunto, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarVencimientoPago($id_pago, $fecha_vencimiento)
    {
        $sql = "UPDATE pago SET fecha_vencimiento = :fecha_vencimiento WHERE id_pago = :id_pago";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento);
        $stmt->bindValue(':id_pago', $id_pago, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function crearNuevoPago($id_deudo, $id_parcela, $fecha_inicio, $fecha_vencimiento, $total)
    {
        $sql = "INSERT INTO pago (id_deudo, id_parcela, fecha_pago, fecha_vencimiento, total)
                VALUES (:id_deudo, :id_parcela, :fecha_pago, :fecha_vencimiento, :total)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_deudo', $id_deudo, PDO::PARAM_INT);
        $stmt->bindValue(':id_parcela', $id_parcela, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_pago', $fecha_inicio);
        $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento);
        $stmt->bindValue(':total', $total);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function realizarTraslado($id_difunto, $id_parcela_nueva, $fecha_traslado)
    {
        $sql = "INSERT INTO ubicacion_difunto (id_difunto, id_parcela, fecha_ubicacion)
                VALUES (:id_difunto, :id_parcela, :fecha_ubicacion)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_difunto', $id_difunto, PDO::PARAM_INT);
        $stmt->bindValue(':id_parcela', $id_parcela_nueva, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ubicacion', $fecha_traslado);

        return $stmt->execute();
    }
}
?>