<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo ParcelaModel
 * Maneja las operaciones CRUD para la tabla 'parcelas'
 */
class ParcelaModel
{
    /**
     * @var PDO $db
     * Conexión a la base de datos
     */
    private PDO $db;

    /*
     * Constructor
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todas las parcelas
     */
    public function getAllParcelas(): array
    {
        $sql = "SELECT p.*,
                        tp.nombre_parcela AS tipo_parcela,
                        de.nombre AS nombre_deudo,
                        o.descripcion AS orientacion
                FROM parcela p
                LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN orientacion o ON p.id_orientacion = o.id_orientacion
                ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las parcelas
     */
    public function getParcelasDisponibles(): array
    {
        $sql = "SELECT 
                    p.*,
                    tp.nombre_parcela AS tipo_parcela,
                    de.nombre AS nombre_deudo,
                    o.descripcion AS orientacion
                FROM parcela p
                LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN orientacion o ON p.id_orientacion = o.id_orientacion
                
                LEFT JOIN (
                    SELECT pg1.id_parcela, pg1.fecha_vencimiento
                    FROM pago pg1
                    INNER JOIN (
                        SELECT id_parcela, MAX(id_pago) as max_id
                        FROM pago
                        GROUP BY id_parcela
                    ) pg2 ON pg1.id_pago = pg2.max_id
                ) ultimo_pago ON p.id_parcela = ultimo_pago.id_parcela

                WHERE 
                    NOT EXISTS (
                        SELECT 1
                        FROM ubicacion_difunto ud
                        WHERE ud.id_parcela = p.id_parcela
                        AND (ud.fecha_retiro IS NULL OR ud.fecha_retiro = '0000-00-00')
                    )
                    AND (
                        ultimo_pago.fecha_vencimiento IS NULL 
                        OR ultimo_pago.fecha_vencimiento < CURDATE()
                    )
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las parcelas
     */
    public function getParcelasOcupadas(): array
    {
        $sql = "SELECT 
                    p.*,
                    tp.nombre_parcela AS tipo_parcela,
                    de.nombre AS nombre_deudo,
                    o.descripcion AS orientacion
                FROM parcela p
                LEFT JOIN tipo_parcela tp ON p.id_tipo_parcela = tp.id_tipo_parcela
                LEFT JOIN deudo de ON p.id_deudo = de.id_deudo
                LEFT JOIN orientacion o ON p.id_orientacion = o.id_orientacion
                WHERE 
                    EXISTS (
                        SELECT 1 
                        FROM pago pg
                        WHERE pg.id_parcela = p.id_parcela
                        AND pg.fecha_vencimiento >= CURDATE()
                    )
                    OR (
                        EXISTS (
                            SELECT 1 
                            FROM pago pg
                            WHERE pg.id_parcela = p.id_parcela
                            AND pg.fecha_vencimiento < CURDATE()
                        )
                        AND EXISTS (
                            SELECT 1
                            FROM ubicacion_difunto ud
                            WHERE ud.id_parcela = p.id_parcela
                        )
                    )
                ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una parcela por su ID
     * @param int $id_parcela ID de la parcela
     * @return array Detalles de la parcela
     */
    public function getParcela($id_parcela): array
    {
        $stmt = $this->db->prepare("SELECT * FROM parcela WHERE id_parcela = :id_parcela");
        $stmt->execute(['id_parcela' => $id_parcela]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva parcela
     * @param int $id_tipo ID del tipo de parcela
     * @param int $id_deudo ID del deudor
     * @param string $numero_ubicacion Número de ubicación
     * @param string $hilera Hilera de la parcela
     * @param string $seccion Sección de la parcela
     * @param string $fraccion Fracción de la parcela
     * @param int $nivel Nivel de la parcela
     * @param int $id_orientacion ID de la orientación
     * @return int Resultado de la operación
     */
    public function insertParcela($id_tipo_parcela, $id_deudo, $numero_ubicacion, $hilera, $seccion, $fraccion, $nivel, $id_orientacion): int|false
    {
        $sql = "INSERT INTO parcela 
                    (id_tipo_parcela, id_deudo, numero_ubicacion, hilera, seccion, fraccion, nivel, id_orientacion) 
                VALUES 
                    (:id_tipo_parcela, :id_deudo, :numero_ubicacion, :hilera, :seccion, :fraccion, :nivel, :id_orientacion)";
        
        try {
            $stmt = $this->db->prepare($sql);

            $parametros = [
                'id_tipo_parcela'  => $id_tipo_parcela,
                'id_deudo'         => !empty($id_deudo) ? $id_deudo : null,
                'numero_ubicacion' => $numero_ubicacion,
                'hilera'           => $hilera,
                'seccion'          => $seccion,
                'fraccion'         => $fraccion,
                'nivel'            => $nivel,
                'id_orientacion'   => !empty($id_orientacion) ? $id_orientacion : null
            ];
            
            $stmt->execute($parametros);
            $nuevo_id = $this->db->lastInsertId();

            AuditoriaHelper::log($_SESSION['usuario_id'], $sql, $parametros, "Parcela Model", "Insert");

            return (int) $nuevo_id;

        } catch (PDOException $e) {
            error_log("Error en insertParcela: " . $e->getMessage());
            return false;
        }
    }

    /** 
     * Actualiza una parcela existente
     * @param int $id_parcela ID de la parcela a actualizar
     * @param int $id_tipo ID del tipo de parcela
     * @param int $id_deudo ID del deudor
     * @param string $numero_ubicacion Número de ubicación
     * @param string $hilera Hilera de la parcela
     * @param string $seccion Sección de la parcela
     * @param string $fraccion Fracción de la parcela
     * @param int $nivel Nivel de la parcela
     * @param int $id_orientacion ID de la orientación
     * @return bool Resultado de la operación
     */
    public function updateParcela($id_parcela, $id_tipo_parcela, $id_deudo, $numero_ubicacion, $hilera, $seccion, $fraccion, $nivel, $id_orientacion): bool
    {
        $sql = "UPDATE parcela 
                SET id_tipo_parcela = :id_tipo_parcela, id_deudo = :id_deudo, numero_ubicacion = :numero_ubicacion, hilera = :hilera, seccion = :seccion, fraccion = :fraccion, nivel = :nivel, id_orientacion = :id_orientacion 
                WHERE id_parcela = :id_parcela";
        $stmt = $this->db->prepare($sql);

        $parametros = [
            'id_parcela'        => $id_parcela,
            'id_tipo_parcela'   => $id_tipo_parcela,
            'id_deudo'          => $id_deudo,
            'numero_ubicacion'  => $numero_ubicacion,
            'hilera'            => $hilera,
            'seccion'           => $seccion,
            'fraccion'          => $fraccion,
            'nivel'             => $nivel,
            'id_orientacion'    => !empty($id_orientacion) ? $id_orientacion : null
        ];        
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                     
            $parametros,            
            "Parcela Model",             
            "Update"              
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina una parcela por su ID
     * @param $id_parcela ID de la parcela a eliminar
     * @return bool Resultado de la operación
     */
    public function deleteParcela($id_parcela): bool
    {
        $sql        = "DELETE FROM parcela WHERE id_parcela = :id_parcela";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_parcela' => $id_parcela];

        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,                      
            $parametros,               
            "Parcela Model",            
            "Delete"                    
        );

        return $stmt->rowCount() > 0;
    }

    public function obtenerPagosPorParcela($id_parcela)
    {
        $sql = "SELECT pg.id_pago, pg.fecha_pago, pg.fecha_vencimiento, pg.total,
                CONCAT(de.nombre, ' ', de.apellido) AS Deudo
                FROM pago pg
                JOIN deudo de ON pg.id_deudo = de.id_deudo
                WHERE pg.id_parcela = :id_parcela";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_parcela', $id_parcela, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDifuntosPorParcela($id_parcela)
    {
        $sql = "SELECT di.id_difunto, di.dni, di.nombre, di.apellido, ubi.fecha_ingreso
                FROM ubicacion_difunto ubi
                JOIN difunto di ON ubi.id_difunto = di.id_difunto
                WHERE ubi.id_parcela = :id_parcela";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_parcela', $id_parcela, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>