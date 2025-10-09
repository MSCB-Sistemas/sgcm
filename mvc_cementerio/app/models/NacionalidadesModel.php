<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo Nacionalidades
 * 
 * Esta clase gestiona todas las operaciones relacionadas con la tabla `nacionalidades`
 * en la base de datos. Permite obtener, insertar, actualizar y eliminar registros.
 */
class NacionalidadesModel {
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
     * Obtiene todas las nacionalidades registradas.
     * 
     * @return array Lista de nacionalidades como arrays asociativos.
     */
    public function getAllNacionalidades(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM nacionalidades");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una nacionalidad específica por su ID.
     * 
     * @param $id_nacionalidad ID de la nacionalidad a obtener.
     * @return array Array asociativo con los datos o false si no se encuentra.
     */
    public function getNacionalidad($id_nacionalidad): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM nacionalidades WHERE id_nacionalidad = :id_nacionalidad");
        $stmt->execute(['id_nacionalidad' => $id_nacionalidad]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva nacionalidad en la base de datos.
     * 
     * @param $nacionalidad Nombre de la nacionalidad.
     * @return int ID de la nueva nacionalidad o false si falla la operación.
     */
    public function insertNacionalidad($nacionalidad): int
    {
        $sql        = "INSERT INTO nacionalidades (nacionalidad) VALUES (:nacionalidad)";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['nacionalidad' => $nacionalidad];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Nacionalidades Model",             // Modelo
            "Insert"                    // Accion
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualiza una nacionalidad existente.
     * 
     * @param $id_nacionalidad ID de la nacionalidad a actualizar.
     * @param $nacionalidad Nuevo valor de la nacionalidad.
     * @return bool True si se actualizó al menos una fila, false en caso contrario.
     */
    public function updateNacionalidad($id_nacionalidad, $nacionalidad): bool
    {
        $sql = "UPDATE nacionalidades SET nacionalidad = :nacionalidad WHERE id_nacionalidad = :id_nacionalidad";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            'id_nacionalidad' => $id_nacionalidad,
            'nacionalidad' => $nacionalidad
        ];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Nacionalidades Model",             // Modelo
            "Update"                    // Accion
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina una nacionalidad de la base de datos.
     * 
     * @param $id_nacionalidad ID de la nacionalidad a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function deleteNacionalidad($id_nacionalidad): bool
    {
        $sql        = "DELETE FROM nacionalidades WHERE id_nacionalidad = :id_nacionalidad";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_nacionalidad' => $id_nacionalidad];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    // usuario actual
            $sql,                       // Query SQL ejecutada
            $parametros,                // Parámetros
            "Nacionalidades Model",             // Modelo
            "Delete"                    // Accion
        );
        return $stmt->rowCount() > 0;
    }
}
?>
