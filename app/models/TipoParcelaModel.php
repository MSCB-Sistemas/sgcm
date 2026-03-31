<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/AuditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo SexoModel
 * Maneja las operaciones CRUD para la tabla 'sexo'
 */
class TipoParcelaModel {
    /**
     * @var PDO $db
     * Conexión a la base de datos
     */
    private PDO $db;

    /*
     * Constructor
     * Inicializa la conexión a la base de datos
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los tipos de parcelas
     * @return array Lista de tipos de parcelas
     */
    public function getAllTiposParcelas(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tipo_parcela");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un tipo de parcela por su ID
     * @param int $id_tipo ID del tipo de parcela
     * @return array Detalles del tipo de parcela
     */
    public function getTipoParcela($id_tipo_parcela): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tipo_parcela WHERE id_tipo_parcela = :id_tipo_parcela");
        $stmt->execute(['id_tipo_parcela' => $id_tipo_parcela]);
        return $stmt->fetch();
    }

    /**
     * Inserta un nuevo tipo de parcela
     * @param int $id_tipo ID del tipo de parcela
     * @param string $nombre_parcela Nombre del tipo de parcela
     * @return bool Resultado de la operación
     */
    public function insertTipoParcela($nombre_parcela)
    {
        $sql        = "INSERT INTO tipo_parcela (nombre_parcela) VALUES (:nombre_parcela)";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['nombre_parcela' => $nombre_parcela];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                     
            $parametros,             
            "Tipo Parcela Model",           
            "Insert"                  
        );
        
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un tipo de parcela
     * @param int $id_tipo ID del tipo de parcela
     * @param string $nombre_parcela Nombre del tipo de parcela
     * @return bool Resultado de la operación
     */
    public function updateTipoParcela($id_tipo_parcela, $nombre_parcela): bool
    {
        $sql    = "UPDATE tipo_parcela SET nombre_parcela = :nombre_parcela WHERE id_tipo_parcela = :id_tipo_parcela";
        $stmt   = $this->db->prepare($sql);
        
        $parametros = [
            'id_tipo_parcela' => $id_tipo_parcela,
            'nombre_parcela'  => $nombre_parcela
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,            
            $parametros,             
            "Tipo Parcela Model",      
            "Update"               
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina un tipo de parcela por su ID
     * @param int $id_tipo ID del tipo de parcela a eliminar
     * @return bool Resultado de la operación
     */
    public function deleteTipoParcela($id_tipo_parcela): bool
    {
        $sql        = "DELETE FROM tipo_parcela WHERE id_tipo_parcela = :id_tipo_parcela";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_tipo_parcela' => $id_tipo_parcela];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                    
            $parametros,         
            "Tipo Parcela Model",     
            "Delete"                
        );

        return $stmt->rowCount() > 0;
    }
}

?>
