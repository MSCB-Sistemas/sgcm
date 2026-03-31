<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo UbicacionDifuntoModel
 * 
 * Encargada de gestionar las operaciones relacionadas con la ubicación de los difuntos
 * en el cementerio. Realiza tareas de inserción, actualización, eliminación y consulta
 * sobre la tabla `ubicacion_difunto`.
 */
class UbicacionDifuntoModel {
    private PDO $db;

    /**
     * Constructor.
     * Establece la conexión con la base de datos utilizando la clase Database.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todas las ubicaciones registradas en la base de datos.
     * 
     * @return array Lista de ubicaciones como arrays asociativos.
     */
    public function getAllUbicaciones(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM ubicacion_difunto");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los datos de una ubicación específica por su ID.
     * 
     * @param $id_ubicacion_difunto ID de la ubicación a consultar.
     * @return array Array asociativo con los datos de la ubicación o false si no se encuentra.
     */
    public function getUbicacionDifunto($id_ubicacion_difunto): array
    {
        $stmt = $this->db->prepare("SELECT * FROM ubicacion_difunto WHERE id_ubicacion_difunto = :id_ubicacion_difunto");
        $stmt->execute(['id_ubicacion_difunto' => $id_ubicacion_difunto]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva ubicación para un difunto en la base de datos.
     * 
     * @param $id_parcela ID de la parcela donde se ubica el difunto.
     * @param $id_difunto ID del difunto que se ubica.
     * @param $fecha_ingreso Fecha de ingreso del difunto (opcional).
     * @param $fecha_retiro Fecha de retiro del difunto (opcional).
     * @return int ID de la nueva ubicación insertada o false en caso de error.
     */
    public function insertUbicacion($id_parcela, $id_difunto, $fecha_ingreso, $fecha_retiro): int
    {
        $sql = "INSERT INTO ubicacion_difunto (id_parcela, id_difunto, fecha_ingreso, fecha_retiro)
                VALUES (:id_parcela, :id_difunto, :fecha_ingreso, :fecha_retiro)
                ";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            'id_parcela'    => $id_parcela,
            'id_difunto'    => $id_difunto,
            'fecha_ingreso' => $fecha_ingreso,
            'fecha_retiro'  => $fecha_retiro
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    
            $sql,                       
            $parametros,             
            "Ubicacion Difunto Model",  
            "Insert"                   
        );
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza los datos de una ubicación existente.
     * 
     * @param $id_ubicacion_difunto ID de la ubicación a actualizar.
     * @param $id_parcela Nueva ID de parcela.
     * @param $id_difunto Nueva ID de difunto.
     * @param $fecha_ingreso Nueva fecha de ingreso (opcional).
     * @param $fecha_retiro Nueva fecha de retiro (opcional).
     * @return bool True si se actualizó correctamente, false si no se modificó nada.
     */
    public function updateUbicacion($id_ubicacion_difunto, $id_parcela, $id_difunto, $fecha_ingreso, $fecha_retiro): bool
    {
        $sql = "UPDATE ubicacion_difunto SET id_parcela = :id_parcela, id_difunto = :id_difunto, fecha_ingreso = :fecha_ingreso, fecha_retiro = :fecha_retiro
                WHERE id_ubicacion_difunto = :id_ubicacion_difunto
                ";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            'id_ubicacion_difunto'  => $id_ubicacion_difunto,
            'id_parcela'            => $id_parcela,
            'id_difunto'            => $id_difunto,
            'fecha_ingreso'         => $fecha_ingreso,
            'fecha_retiro'          => $fecha_retiro
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                       
            $parametros,              
            "Ubicacion Difunto Model",    
            "Update"                  
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina una ubicación específica de la base de datos.
     * 
     * @param $id_ubicacion_difunto ID de la ubicación a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function deleteUbicacion($id_ubicacion_difunto): bool
    {
        $sql        = "DELETE FROM ubicacion_difunto WHERE id_ubicacion_difunto = :id_ubicacion_difunto";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_ubicacion_difunto' => $id_ubicacion_difunto];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                   
            $parametros,         
            "Ubicacion Difunto Model", 
            "Delete"                  
        );
        
        return $stmt->rowCount() > 0;
    }
}
?>
