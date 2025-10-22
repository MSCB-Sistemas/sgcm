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

    public function obtenerDeudaPorDeudoYParcela($id_deudo, $id_parcela)
    {
        $fecha_actual = date('Y-m-d');
        $sql = "SELECT * FROM pago 
                WHERE id_deudo = :id_deudo 
                AND id_parcela = :id_parcela 
                AND fecha_vencimiento < :fecha_actual
                ORDER BY fecha_vencimiento DESC LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_deudo' => $id_deudo,
            'id_parcela' => $id_parcela,
            'fecha_actual' => $fecha_actual
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfTraslado($id_difunto, $id_pago)
    {
        $sql = "SELECT 
                    CONCAT(d.apellido, ', ', d.nombre) AS difunto, 
                    d.fecha_fallecimiento,
                    
                    CONCAT(de.apellido, ', ', de.nombre) AS responsable_tramite,
                    
                    p.id_pago,
                    p.fecha_pago,
                    
                    po.id_parcela AS id_parcela,
                    po.hilera AS hilera,
                    po.seccion AS seccion,
                    tpo.nombre_parcela AS tipo_parcela,
                    
                    pd.id_parcela AS id_parcela_2,
                    pd.hilera AS hilera_2,
                    pd.seccion AS seccion_2,
                    tpd.nombre_parcela AS tipo_parcela_2

                FROM pago p
                JOIN deudo de ON p.id_deudo = de.id_deudo
                JOIN difunto d ON d.id_difunto = :id_difunto
                
                JOIN ubicacion_difunto uo ON uo.id_difunto = d.id_difunto AND uo.fecha_retiro IS NOT NULL
                JOIN parcela po ON po.id_parcela = uo.id_parcela
                JOIN tipo_parcela tpo ON tpo.id_tipo_parcela = po.id_tipo_parcela
                
                JOIN ubicacion_difunto ud ON ud.id_difunto = d.id_difunto AND ud.fecha_retiro IS NULL
                JOIN parcela pd ON pd.id_parcela = ud.id_parcela
                JOIN tipo_parcela tpd ON tpd.id_tipo_parcela = pd.id_tipo_parcela

                WHERE p.id_pago = :id_pago
                ORDER BY uo.fecha_retiro DESC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto, 'id_pago' => $id_pago]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfIngresoDifunto($id_difunto, $id_pago)
    {
        $sql = "SELECT
                    CONCAT(d.apellido, ', ', d.nombre) as difunto,
                    d.fecha_fallecimiento,
                    p.id_pago,
                    p.fecha_pago,
                    CONCAT(de.apellido, ', ', de.nombre) as responsable_tramite,
                    de.dni as dni_responsable,
                    p.vinculo_familiar,
                    
                    par.id_parcela,
                    tp.nombre_parcela as tipo_parcela, 
                    par.seccion,
                    par.hilera

                FROM pago p
                JOIN deudo de ON p.id_deudo = de.id_deudo
                JOIN difunto d ON d.id_difunto = :id_difunto
                
                JOIN ubicacion_difunto ud ON ud.id_difunto = d.id_difunto AND ud.fecha_retiro IS NULL
                JOIN parcela par ON ud.id_parcela = par.id_parcela
                JOIN tipo_parcela tp ON par.id_tipo_parcela = tp.id_tipo_parcela

                WHERE p.id_pago = :id_pago";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto, 'id_pago' => $id_pago]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfTrasladoExterno($id_difunto, $id_parcela)
    {
        $sql = "SELECT 
                    d.nombre AS difunto_nombre, d.apellido AS difunto_apellido, d.dni AS difunto_dni, d.fecha_fallecimiento,
                    de.nombre AS deudo_nombre, de.apellido AS deudo_apellido, de.dni AS deudo_dni,
                    p.numero_ubicacion, p.hilera, p.seccion, p.fraccion, p.nivel,
                    tp.nombre_parcela
                FROM difunto d
                JOIN deudo de ON d.id_deudo = de.id_deudo
                JOIN parcela p ON p.id_parcela = :id_parcela
                JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                WHERE d.id_difunto = :id_difunto";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto, 'id_parcela' => $id_parcela]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfIngresoBR($id_difunto, $id_deudo, $id_parcela)
    {
        $sql = "SELECT 
                    d.nombre AS difunto_nombre, d.apellido AS difunto_apellido, d.dni AS difunto_dni,
                    de.nombre AS deudo_nombre, de.apellido AS deudo_apellido, de.dni AS deudo_dni, de.domicilio AS deudo_domicilio,
                    p.numero_ubicacion, p.hilera, p.seccion, p.fraccion,
                    tp.nombre_parcela
                FROM difunto d
                JOIN deudo de ON de.id_deudo = :id_deudo
                JOIN parcela p ON p.id_parcela = :id_parcela
                JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                WHERE d.id_difunto = :id_difunto";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto, 'id_deudo' => $id_deudo, 'id_parcela' => $id_parcela]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosParaPdfLibreDeuda($id_parcela, $id_deudo)
    {
        $sql = "SELECT
                p.id_parcela, p.hilera, p.seccion,
                tp.nombre_parcela as tipo_parcela,
                CONCAT(d.apellido, ', ', d.nombre) as deudo,
                d.dni as dni_deudo,
                
                (SELECT GROUP_CONCAT(CONCAT(dif.apellido, ', ', dif.nombre) SEPARATOR ', ') 
                 FROM ubicacion_difunto ud JOIN difunto dif ON ud.id_difunto = dif.id_difunto
                 WHERE ud.id_parcela = p.id_parcela AND ud.fecha_retiro IS NULL) as difunto,

                (SELECT MAX(pg.fecha_vencimiento) FROM pago pg WHERE pg.id_parcela = p.id_parcela) as fecha_vencimiento
                
            FROM parcela p
            LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
            JOIN deudo d ON d.id_deudo = :id_deudo
            WHERE p.id_parcela = :id_parcela";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_parcela' => $id_parcela, 'id_deudo' => $id_deudo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>