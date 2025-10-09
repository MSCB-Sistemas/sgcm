<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo PagoModel
 * 
 * Esta clase se encarga de gestionar las operaciones CRUD relacionadas con los pagos.
 * Accede a la tabla `pago` de la base de datos y permite registrar, consultar, actualizar y eliminar pagos.
 */
class PagoModel {
    private PDO $db;

    /**
     * Constructor.
     * Establece la conexión con la base de datos al instanciar la clase.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los pagos registrados en la base de datos.
     * 
     * @return array Lista de pagos como arrays asociativos.
     */
    public function getAllPagos(): array
    {
        $stmt = $this->db->prepare("SELECT p.*,
                                    de.nombre as nombre_deudo,
                                    p.id_parcela AS parcela,
                                    u.usuario AS usuario
                                    FROM pago p
                                    LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                                    LEFT JOIN parcela pa ON p.id_parcela = pa.id_parcela
                                    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                                    LEFT JOIN tipo_operacion op ON p.id_tipo_operacion = op.id_tipo_operacion");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un pago específico por su ID.
     * 
     * @param $id_pago ID del pago a consultar.
     * @return array Array asociativo con los datos del pago o false si no se encuentra.
     */
    public function getPago($id_pago): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pago WHERE id_pago = :id_pago");
        $stmt->execute(['id_pago' => $id_pago]);
        return $stmt->fetch();
    }

    /**
     * Inserta un nuevo pago en la base de datos.
     * 
     * @param int $id_deudo ID del deudo asociado al pago.
     * @param int $id_parcela ID de la parcela asociada al pago.
     * @param string $fecha_pago Fecha del pago.
     * @param float $importe Importe del pago.
     * @param float $recargo Recargo aplicado al pago.
     * @param float $total Total del pago (importe + recargo).
     * @param int $id_usuario ID del usuario que realiza el pago.
     * @return int ID del nuevo pago insertado.
     */
    public function insertPago($id_deudo, $id_parcela, $id_tipo_operacion, $fecha_pago, $fecha_vencimiento, $importe, $recargo, $total, $vinculo_familiar, $responsable_tramite, $id_usuario): int
    {
        $sql = "INSERT INTO pago (id_deudo, id_parcela, id_tipo_operacion, fecha_pago, fecha_vencimiento, importe, recargo, total, vinculo_familiar, responsable_tramite, id_usuario) 
                VALUES (:id_deudo, :id_parcela, :id_tipo_operacion, :fecha_pago, :fecha_vencimiento, :importe, :recargo, :total, :vinculo_familiar, :responsable_tramite, :id_usuario)";

        $stmt = $this->db->prepare($sql);
        $parametros = [
            "id_deudo" => $id_deudo,
            "id_parcela" => $id_parcela,
            "id_tipo_operacion" => $id_tipo_operacion,
            "fecha_pago" => $fecha_pago,
            "fecha_vencimiento" => $fecha_vencimiento,
            "importe" => $importe,
            "recargo" => $recargo,
            "total" => $total,
            "vinculo_familiar" => $vinculo_familiar,
            "responsable_tramite" => $responsable_tramite,
            "id_usuario" => $id_usuario
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                  
            $parametros,          
            "Pago Model",      
            "Insert"     
        );

        return $this->db->lastInsertId();
    }

    /** 
     * Actualiza un pago existente en la base de datos.
     * 
     * @param int $id_pago ID del pago a actualizar.
     * @param int $id_deudo ID del deudo asociado al pago.
     * @param int $id_parcela ID de la parcela asociada al pago.
     * @param string $fecha_pago Fecha del pago.
     * @param float $importe Importe del pago.
     * @param float $recargo Recargo aplicado al pago.
     * @param float $total Total del pago (importe + recargo).
     * @param int $id_usuario ID del usuario que realiza el pago.
     * @return bool True si se actualizó correctamente, false en caso contrario.
     */
    public function updatePago($id_pago, $id_deudo, $id_parcela, $id_tipo_operacion, $fecha_pago, $fecha_vencimiento, $importe, $recargo, $total, $vinculo_familiar, $responsable_tramite, $id_usuario): bool
    {
        $sql = "UPDATE pago SET id_deudo = :id_deudo, id_parcela = :id_parcela, id_tipo_operacion = :id_tipo_operacion, fecha_pago = :fecha_pago, fecha_vencimiento = :fecha_vencimiento, importe = :importe, recargo = :recargo, total = :total, vinculo_familiar = :vinculo_familiar, responsable_tramite = :responsable_tramite, id_usuario = :id_usuario
                WHERE id_pago = :id_pago";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            "id_pago" => $id_pago,
            "id_deudo" => $id_deudo,
            "id_parcela" => $id_parcela,
            "id_tipo_operacion" => $id_tipo_operacion,
            "fecha_pago" => $fecha_pago,
            "fecha_vencimiento" => $fecha_vencimiento,
            "importe" => $importe,
            "recargo" => $recargo,
            "total" => $total,
            "vinculo_familiar" => $vinculo_familiar,
            "responsable_tramite" => $responsable_tramite,
            "id_usuario" => $id_usuario
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                     
            $parametros,            
            "Pago Model",         
            "Update"  
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina un pago de la base de datos.
     * 
     * @param int $id_pago ID del pago a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function deletePago($id_pago): bool
    {
        $sql        = "DELETE FROM pago WHERE id_pago = :id_pago";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_pago' => $id_pago];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                   
            $parametros,         
            "Pago Model",         
            "Delete"              
        );
        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        $stmt   = $this->db->prepare("SELECT COUNT(*) as total FROM pago");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function countFiltered($search): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM pago p
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN parcela pa ON p.id_parcela = pa.id_parcela
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN tipo_operacion op ON p.id_tipo_operacion = op.id_tipo_operacion
                WHERE de.nombre LIKE :search 
                   OR de.apellido LIKE :search 
                   OR pa.id_parcela LIKE :search 
                   OR u.usuario LIKE :search 
                   OR p.fecha_pago LIKE :search 
                   OR p.importe LIKE :search 
                   OR p.total LIKE :search";

        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getPage($orderCol, $orderDir, $start, $length): array
    {
        $allowedColumns = [
            'id_pago', 
            'nombre_deudo', 
            'parcela', 
            'tipo_operacion', 
            'fecha_pago', 
            'fecha_vencimiento', 
            'importe', 
            'recargo', 
            'total', 
            'vinculo_familiar', 
            'responsable_tramite',
            'usuario'
        ];

        if (!in_array($orderCol, $allowedColumns)) {
            $orderCol = 'id_pago';
        }
        
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT p.*,
                       CONCAT(de.nombre, ' ', de.apellido) as nombre_deudo,
                       pa.id_parcela as parcela,
                       op.descripcion as tipo_operacion,
                       u.usuario as usuario
                FROM pago p
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN parcela pa ON p.id_parcela = pa.id_parcela
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN tipo_operacion op ON p.id_tipo_operacion = op.id_tipo_operacion
                ORDER BY $orderCol $orderDir 
                LIMIT :start, :length";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFiltered($search, $orderCol, $orderDir, $start, $length): array
    {
        $allowedColumns = [
            'id_pago', 
            'nombre_deudo', 
            'parcela', 
            'tipo_operacion', 
            'fecha_pago', 
            'fecha_vencimiento', 
            'importe', 
            'recargo', 
            'total', 
            'vinculo_familiar', 
            'responsable_tramite',
            'usuario'
        ];
      
        if (!in_array($orderCol, $allowedColumns)) {
            $orderCol = 'id_pago';
        }
        
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT p.*,
                       CONCAT(de.nombre, ' ', de.apellido) as nombre_deudo,
                       pa.id_parcela as parcela,
                       op.descripcion as tipo_operacion,
                       u.usuario as usuario
                FROM pago p
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN parcela pa ON p.id_parcela = pa.id_parcela
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN tipo_operacion op ON p.id_tipo_operacion = op.id_tipo_operacion
                WHERE de.nombre LIKE :search 
                   OR de.apellido LIKE :search 
                   OR pa.id_parcela LIKE :search 
                   OR u.usuario LIKE :search 
                   OR p.fecha_pago LIKE :search 
                   OR p.fecha_vencimiento LIKE :search
                   OR p.importe LIKE :search 
                   OR p.recargo LIKE :search
                   OR p.total LIKE :search
                ORDER BY $orderCol $orderDir 
                LIMIT :start, :length";
        
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarPagoMantenimiento($deudo_id, $parcela_id, $tipo_operacion_id, $fecha_pago, $fecha_vencimiento, $importe, $usuario_id): bool
    {
        $recargo = 0;
        $total = $importe;

        try {
            $sql = "INSERT INTO pago 
                        (id_deudo, id_parcela, id_tipo_operacion, fecha_pago, fecha_vencimiento, importe, recargo, total, id_usuario) 
                    VALUES 
                        (:deudo, :parcela, :tipo_op, :fecha_pago, :fecha_venc, :importe, :recargo, :total, :usuario)";
            
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':deudo', $deudo_id, PDO::PARAM_INT);
            $stmt->bindParam(':parcela', $parcela_id, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_op', $tipo_operacion_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_pago', $fecha_pago);
            $stmt->bindParam(':fecha_venc', $fecha_vencimiento);
            $stmt->bindParam(':importe', $importe);
            $stmt->bindParam(':recargo', $recargo);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':usuario', $usuario_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en insertarPagoMantenimiento: " . $e->getMessage());
            return false;
        }
    }
}
?>
