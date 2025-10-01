<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

class DeudoModel {
    /**
     * @var PDO $db
     * Conexión a la base de datos
     */
    private PDO $db;

    /**
     * Constructor
     * Inicializa la conexión a la base de datos
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los deudos registrados
     * @return array Lista de deudos
     */
    public function getAllDeudos(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM deudo");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un deudo por su ID
     * @param int $id_deudo ID del deudo
     * @return array|false Datos del deudo o false si no se encuentra
     */
    public function getDeudo($id_deudo): array
    {
        $stmt = $this->db->prepare("SELECT * FROM deudo WHERE id_deudo = :id_deudo");
        $stmt->execute(['id_deudo' => $id_deudo]);
        return $stmt->fetch();
    }

    /**
     * Inserta un nuevo deudo
     * @param $dni DNI del deudo
     * @param $nombre Nombre del deudo
     * @param $apellido Apellido del deudo
     * @param $telefono Teléfono del deudo
     * @param $email Correo electrónico del deudo
     * @param $domicilio Domicilio del deudo
     * @param $localidad Localidad del deudo
     * @param $codigo_postal Código postal del deudo
     * @return int ID del nuevo deudo insertado o false si falla
     */
    public function insertDeudo($dni, $nombre, $apellido, $telefono, $email, $domicilio, $localidad, $codigo_postal){
        $sql = "INSERT INTO deudo (dni, nombre, apellido, telefono, email, domicilio, localidad, codigo_postal)
                VALUES (:dni, :nombre, :apellido, :telefono, :email, :domicilio, :localidad, :codigo_postal)";
        $stmt = $this->db->prepare($sql);

        $parametros = [
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'email' => $email,
            'domicilio' => $domicilio,
            'localidad' => $localidad,
            'codigo_postal' => $codigo_postal
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                      
            $parametros,               
            "Deudo Model",             
            "Insert"                  
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualiza los detalles de un deudo en la base de datos.
     *
     * @param $id_deudo El identificador único del deudo.
     * @param mixed $dni El DNI (Documento Nacional de Identidad) del deudo.
     * @param mixed $nombre El nombre del deudo.
     * @param mixed $apellido El apellido del deudo.
     * @param mixed $telefono El número de teléfono del deudo.
     * @param mixed $email La dirección de correo electrónico del deudo.
     * @param mixed $domicilio El domicilio del deudo.
     * @param mixed $localidad La localidad o ciudad del deudo.
     * @param mixed $codigo_postal El código postal del domicilio del deudo.
     * @return bool Devuelve true si la actualización fue exitosa, false en caso contrario.
     */
    public function updateDeudo($id_deudo, $dni, $nombre, $apellido, $telefono, $email, $domicilio, $localidad, $codigo_postal): bool
    {
        $sql = "UPDATE deudo SET dni = :dni, nombre = :nombre, apellido = :apellido, telefono = :telefono, email = :email, domicilio = :domicilio, localidad = :localidad, codigo_postal = :codigo_postal
                WHERE id_deudo = :id_deudo";
        $stmt = $this->db->prepare($sql);
        $parametros = [
            'id_deudo' => $id_deudo,
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'email' => $email,
            'domicilio' => $domicilio,
            'localidad' => $localidad,
            'codigo_postal' => $codigo_postal
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],  
            $sql,                      
            $parametros,      
            "Deudo Model",        
            "Update"                
        );
        return $stmt->rowCount() > 0;
    }

    public function deleteDeudo(int $id_deudo): bool
    {
        $sql = "DELETE FROM deudo WHERE id_deudo = :id_deudo";
        $stmt = $this->db->prepare($sql);
        $parametros = ['id_deudo' => $id_deudo];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,                      
            $parametros,       
            "Deudo Model",    
            "Delete"
        );
        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM deudo")->fetchColumn();
    }

    public function countFiltered(string $search): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM deudo 
            WHERE nombre LIKE :search OR apellido LIKE :search OR dni LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
        return (int)$stmt->fetchColumn();
    }

    public function getPage(string $orderCol, string $orderDir, int $start, int $length): array
    {
        $sql = "SELECT * FROM deudo ORDER BY $orderCol $orderDir LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFiltered(string $search, string $orderCol, string $orderDir, int $start, int $length): array
    {
        $sql = "SELECT * FROM deudo 
                WHERE nombre LIKE :search OR apellido LIKE :search OR dni LIKE :search
                ORDER BY $orderCol $orderDir
                LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', "%$search%");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
