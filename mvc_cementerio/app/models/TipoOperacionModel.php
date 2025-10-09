<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

class TipoOperacionModel {
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllTipoOperaciones(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tipo_operacion");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoOperacion($id_tipo_operacion): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tipo_operacion WHERE id_tipo_operacion = :id_tipo_operacion");
        $stmt->execute(['id_tipo_operacion' => $id_tipo_operacion]);
        return $stmt->fetch();
    }

    public function insertTipoOperacion($descripcion)
    {
        $sql = "INSERT INTO tipo_operacion (descripcion) VALUES (:descripcion)";
        $stmt = $this->db->prepare($sql);
        $parametros = ['descripcion' => $descripcion];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,                    
            $parametros,             
            "Tipo Operacion Model",         
            "Insert"                   
        );
        return $this->db->lastInsertId();
    }

    public function updateTipoOperacion($id_tipo_operacion, $descripcion): bool
    {
        $sql = "UPDATE tipo_operacion SET descripcion = :descripcion WHERE id_tipo_operacion = :id_tipo_operacion";
        
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            'id_tipo_operacion' => $id_tipo_operacion,
            'descripcion' => $descripcion
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],    
            $sql,                      
            $parametros,                 
            "Tipo Operacion Model",     
            "Update"          
        );

        return $stmt->rowCount() > 0;
    }

    public function deleteTipoOperacion($id_tipo_operacion): bool
    {
        $sql = "DELETE FROM tipo_operacion WHERE id_tipo_operacion = :id_tipo_operacion";
        $stmt = $this->db->prepare($sql);
        $parametros = ['id_tipo_operacion' => $id_tipo_operacion];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                      
            $parametros,              
            "Tipo Operacion Model",      
            "Delete"               
        );
        return $stmt->rowCount() > 0;
    }
}
?>