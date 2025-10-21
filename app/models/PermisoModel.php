<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

class PermisoModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();    
    }

    public function getPermisosPorRol($id_tipo) 
    {
        $sql = "SELECT p.nombre_permiso
                  FROM permisos p
                  INNER JOIN tipos_usuarios_permisos rp ON p.id_permiso = rp.id_permiso
                WHERE rp.id_tipo_usuario = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tipo]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    }
}