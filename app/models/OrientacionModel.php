<?php
// Carga la configuración del sistema y la clase para conectar a la base de datos
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo OrientacionModel
 * 
 * Esta clase gestiona las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con la tabla `orientacion` en la base de datos. 
 */
class OrientacionModel {
    private PDO $db;

    /**
     * Constructor de la clase.
     * Establece la conexión a la base de datos utilizando la clase Database.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todas las orientaciones registradas.
     * 
     * @return array Lista de orientaciones como arrays asociativos.
     */
    public function getAllOrientaciones(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orientacion");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una orientación específica por su ID.
     * 
     * @param $id_orientacion ID de la orientación a obtener.
     * @return array Array asociativo con los datos o false si no se encuentra.
     */
    public function getOrientacion($id_orientacion): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orientacion WHERE id_orientacion = :id_orientacion");
        $stmt->execute(['id_orientacion' => $id_orientacion]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva orientación en la base de datos.
     * 
     * @param $descripcion Descripción de la orientación.
     * @return int ID de la nueva orientación o false si falla la operación.
     */
    public function insertOrientacion($descripcion): int
    {
        $sql        = "INSERT INTO orientacion (descripcion) VALUES (:descripcion)";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['descripcion' => $descripcion];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Orientacion Model",             // Modelo
            "Insert"                    // Accion
        );
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza una orientación existente.
     * 
     * @param $id_orientacion ID de la orientación a actualizar.
     * @param $descripcion Nueva descripción de la orientación.
     * @return bool True si se actualizó al menos una fila, false en caso contrario.
     */
    public function updateOrientacion($id_orientacion, $descripcion): bool
    {
        $sql = "UPDATE orientacion 
                SET descripcion = :descripcion 
                WHERE id_orientacion = :id_orientacion
                ";
        $stmt = $this->db->prepare($sql);
        $parametros = [
            'id_orientacion' => $id_orientacion,
            'descripcion'    => $descripcion
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Orientacion Model",             // Modelo
            "Update"                    // Accion
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina una orientación de la base de datos.
     * 
     * @param $id_orientacion ID de la orientación a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function deleteOrientacion($id_orientacion): bool
    {
        $sql        = "DELETE FROM orientacion WHERE id_orientacion = :id_orientacion";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_orientacion' => $id_orientacion];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Orientacion Model",             // Modelo
            "Delete"                    // Accion
        );
        return $stmt->rowCount() > 0;
    }
}
?>
