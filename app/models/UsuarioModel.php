<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

class UsuarioModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /** 
     * Obtener todos los usuarios
     * @return array Lista de usuarios
     */
    public function getAllUsuarios(): array {
        $stmt = $this->db->prepare("SELECT 
            u.id_usuario,
            u.usuario,
            u.nombre,
            u.apellido,
            u.cargo,
            u.sector,
            u.telefono,
            u.email,
            tu.id_tipo_usuario,
            u.activo
        FROM 
            usuarios u
        JOIN 
            tipos_usuarios tu ON u.id_tipo_usuario = tu.id_tipo_usuario;");
        
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$usuarios) {
            return [];
        }

        foreach ($usuarios as &$usuario) {
            if ((int)$usuario['activo'] === 1) {
                $usuario['activo'] = "Si";
            } else {
                $usuario['activo'] = "No";
            }
        }
        return $usuarios;
    }

    /** 
     * Obtener un usuario por su ID
     * @param int $id_usuario ID del usuario
     * @return array Detalles del usuario
     */
    public function getUsuarioId($id_usuario): array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->fetch();
    }

    /**
     * Summary of insertUsuario
     * @param mixed $usuario
     * @param mixed $nombre
     * @param mixed $apellido
     * @param mixed $cargo
     * @param mixed $sector
     * @param mixed $contrasenia
     * @param mixed $id_tipo_usuario
     * @return bool
     */
    public function insertUsuario($usuario, $nombre, $apellido, $cargo, $sector, $telefono, $email, $contrasenia, $id_tipo_usuario): int
    {
        $sql = "INSERT INTO usuarios (usuario, nombre, apellido, cargo, sector, telefono, email, contrasenia, id_tipo_usuario) 
                VALUES (:usuario, :nombre, :apellido, :cargo, :sector, :telefono, :email, :contrasenia, :id_tipo_usuario)
                ";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            "usuario"           => $usuario,
            "nombre"            => $nombre,
            "apellido"          => $apellido,
            "cargo"             => $cargo,
            "sector"            => $sector,
            'telefono'          => $telefono,
            'email'             => $email,
            "contrasenia"       => password_hash($contrasenia, PASSWORD_DEFAULT),
            "id_tipo_usuario"   => $id_tipo_usuario
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,               
            $parametros,       
            "Usuario Model",         
            "Insert"               
        );
        return (int)$this->db->lastInsertId();
    }

    /** 
     * Update usuario
     * 
     * @param int $id_usuario ID del usuario a actualizar
     * @param string $usuario Nuevo nombre de usuario
     * @param string $nombre Nuevo nombre
     * @param string $apellido Nuevo apellido
     * @param string $cargo Nuevo cargo
     * @param string $sector Nuevo sector
     * @param string $contrasenia Nueva contraseña
     * @param int $id_tipo_usuario Nuevo tipo de usuario
     * @param bool $activo Estado activo del usuario
     * @return bool Resultado de la actualización
     */
    public function updateUsuario($id_usuario, $usuario, $nombre, $apellido, $cargo, $sector, $telefono, $email, $id_tipo_usuario): bool
    {
        $sql = "UPDATE usuarios 
                SET usuario = :usuario, nombre = :nombre, apellido = :apellido, cargo = :cargo, sector = :sector, telefono = :telefono, email = :email, id_tipo_usuario = :id_tipo_usuario 
                WHERE id_usuario = :id_usuario
                ";
        $stmt = $this->db->prepare($sql);
        
        $parametros = [
            "id_usuario"      => $id_usuario,
            "usuario"         => $usuario,
            "nombre"          => $nombre,
            "apellido"        => $apellido,
            "cargo"           => $cargo,
            "sector"          => $sector,
            'telefono'        => $telefono,
            'email'           => $email,
            "id_tipo_usuario" => $id_tipo_usuario,
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                  
            $parametros,             
            "Usuario Model",        
            "Update"           
        );
        return $stmt->rowCount() > 0;
    }

    /**     
     * Elimina un usuario por su ID
     * @param $id_usuario ID del usuario a eliminar
     * @return bool Resultado de la eliminación
     */
    public function deleteUsuario($id_usuario): bool
    {
        $sql        = "UPDATE usuarios SET activo = 0 WHERE id_usuario = :id_usuario";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_usuario' => $id_usuario];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                      
            $parametros,        
            "Usuario Model",      
            "Delete"               
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Activa un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario ID del usuario a activar.
     * @return bool True si se activó el usuario, false en caso contrario.
     */
    public function activateUsuario($id_usuario) : bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Actualiza la contraseña de un usuario.
     *
     * @param  mixed $id_usuario ID del usuario cuya contraseña se actualizará.
     * @param  mixed $password  Nueva contraseña del usuario.
     * @return bool True si se actualizó la contraseña, false en caso contrario.
     */
    public function updatePassword($id_usuario, $password) : bool {
        $sql        = "UPDATE usuarios SET contrasenia = :contrasenia WHERE id_usuario = :id_usuario";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_usuario' => $id_usuario, 'contrasenia'=> $password];
        $stmt->execute($parametros);
        
        AuditoriaHelper::log(
            $_SESSION['usuario_id'],   
            $sql,                     
            $parametros,            
            "Usuario Model",        
            "Password Update"      
        );
        return $stmt->rowCount() > 0;
    }

    public function getUsuarioByNombreUsuario($nombre_usuario) : array|bool {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute(["usuario"=> $nombre_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}