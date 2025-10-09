<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/AuditoriaHelper.php';
require_once 'Database.php';

class DifuntoModel
{
    private PDO $db;

    /**
     * Constructor
     * Establece la conexión a la base de datos al crear una instancia del modelo.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los registros de difuntos.
     * @return array Lista de difuntos como arrays asociativos.
     */
    public function getAllDifuntos(): array
    {
        $stmt = $this->db->prepare("SELECT 
                    d.id_difunto,
                    de.nombre AS nombre_deudo,
                    d.nombre,
                    d.apellido,
                    d.dni,
                    d.edad,
                    d.fecha_fallecimiento,
                    COALESCE(s.descripcion, ' ') AS sexo,
                    COALESCE(n.nacionalidad, ' ') AS nacionalidad,
                    COALESCE(ec.descripcion, ' ') AS estado_civil,
                    d.domicilio,
                    d.localidad,
                    d.codigo_postal
                FROM difunto d
                LEFT JOIN deudo de ON d.id_deudo = de.id_deudo
                LEFT JOIN sexo s ON d.id_sexo = s.id_sexo
                LEFT JOIN estado_civil ec ON d.id_estado_civil = ec.id_estado_civil
                LEFT JOIN nacionalidades n ON d.id_nacionalidad = n.id_nacionalidad");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un difunto específico por su ID.
     * @param int $id_difunto ID del difunto.
     * @return array|false Datos del difunto como array asociativo, o false si no se encuentra.
     */
    public function getDifunto(int $id_difunto): array
    {
        $stmt = $this->db->prepare("SELECT * FROM difunto WHERE id_difunto = :id_difunto");
        $stmt->execute(['id_difunto' => $id_difunto]);
        return $stmt->fetch();
    }

    /**
     * Summary of insertDifunto
     * @param mixed $id_deudo
     * @param mixed $nombre
     * @param mixed $apellido
     * @param mixed $dni
     * @param mixed $edad
     * @param mixed $fecha_fallecimiento
     * @param mixed $id_sexo
     * @param mixed $id_nacionalidad
     * @param mixed $id_estado_civil
     * @param mixed $domicilio
     * @param mixed $localidad
     * @param mixed $codigo_postal
     * @return bool|string
     */
    public function insertDifunto($id_deudo, $nombre, $apellido, $dni, $edad, $fecha_fallecimiento, $id_sexo, $id_nacionalidad, $id_estado_civil, $domicilio, $localidad, $codigo_postal): int
    {
        $sql = "INSERT INTO difunto (
                id_deudo, nombre, apellido, dni, edad, fecha_fallecimiento,
                id_sexo, id_nacionalidad, id_estado_civil, domicilio, localidad, codigo_postal
            ) 
            VALUES (
                :id_deudo, :nombre, :apellido, :dni, :edad, :fecha_fallecimiento,
                :id_sexo, :id_nacionalidad, :id_estado_civil, :domicilio, :localidad, :codigo_postal
            )";
        $stmt = $this->db->prepare($sql);

        $parametros = [
            'id_deudo'              => $id_deudo,
            'nombre'                => $nombre,
            'apellido'              => $apellido,
            'dni'                   => $dni,
            'edad'                  => $edad,
            'fecha_fallecimiento'   => $fecha_fallecimiento,
            'id_sexo'               => $id_sexo,
            'id_nacionalidad'       => $id_nacionalidad,
            'id_estado_civil'       => $id_estado_civil,
            'domicilio'             => $domicilio,
            'localidad'             => $localidad,
            'codigo_postal'         => $codigo_postal
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,
            $parametros,
            "Difunto Model",
            "Insert"
        );
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza los datos de un difunto existente.
     * @param int $id_difunto ID del difunto a actualizar.
     * @param array $data Datos nuevos para el difunto.
     * @return bool True si se actualizó al menos un campo, False si no hubo cambios.
     */
    public function updateDifunto(int $id_difunto, $id_deudo, $nombre, $apellido, $dni, $edad, $fecha_fallecimiento, $id_sexo, $id_nacionalidad, $id_estado_civil, $domicilio, $localidad, $codigo_postal): bool
    {
        $sql = "UPDATE difunto SET 
                id_deudo            = :id_deudo,
                nombre              = :nombre,
                apellido            = :apellido,
                dni                 = :dni,
                edad                = :edad,
                fecha_fallecimiento = :fecha_fallecimiento,
                id_sexo             = :id_sexo,
                id_nacionalidad     = :id_nacionalidad,
                id_estado_civil     = :id_estado_civil,
                domicilio           = :domicilio,
                localidad           = :localidad,
                codigo_postal       = :codigo_postal
                WHERE id_difunto    = :id_difunto
                ";
        $stmt = $this->db->prepare($sql);

        $parametros = [
            "id_difunto"            => $id_difunto,
            "id_deudo"              => $id_deudo,
            "nombre"                => $nombre,
            "apellido"              => $apellido,
            "dni"                   => $dni,
            "edad"                  => $edad,
            "fecha_fallecimiento"   => $fecha_fallecimiento,
            "id_sexo"               => $id_sexo,
            "id_nacionalidad"       => $id_nacionalidad,
            "id_estado_civil"       => $id_estado_civil,
            "domicilio"             => $domicilio,
            "localidad"             => $localidad,
            "codigo_postal"         => $codigo_postal
        ];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,
            $parametros,
            "Difunto Model",
            "Update"
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina un difunto de la base de datos.
     * @param int $id_difunto ID del difunto a eliminar.
     * @return bool True si se eliminó, False si no se encontró o no se eliminó.
     */
    public function deleteDifunto(int $id_difunto): bool
    {
        $sql        = "DELETE FROM difunto WHERE id_difunto = :id_difunto";
        $stmt       = $this->db->prepare($sql);
        $parametros = ['id_difunto' => $id_difunto];
        $stmt->execute($parametros);

        AuditoriaHelper::log(
            $_SESSION['usuario_id'],
            $sql,
            $parametros,
            "Difunto Model",
            "Update"
        );
        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        $stmt   = $this->db->prepare("SELECT COUNT(*) as total FROM difunto");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function countFiltered($search): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM difunto d
                LEFT JOIN deudo de ON d.id_deudo = de.id_deudo
                LEFT JOIN sexo s ON d.id_sexo = s.id_sexo
                LEFT JOIN estado_civil ec ON d.id_estado_civil = ec.id_estado_civil
                LEFT JOIN nacionalidades n ON d.id_nacionalidad = n.id_nacionalidad
                WHERE d.nombre LIKE :search 
                   OR d.apellido LIKE :search 
                   OR d.dni LIKE :search 
                   OR de.nombre LIKE :search 
                   OR s.descripcion LIKE :search 
                   OR n.nacionalidad LIKE :search 
                   OR ec.descripcion LIKE :search 
                   OR d.domicilio LIKE :search 
                   OR d.localidad LIKE :search";

        $stmt       = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        $result     = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getPage($orderCol, $orderDir, $start, $length): array
    {
        $allowedColumns = [
            'id_difunto',
            'nombre_deudo',
            'nombre',
            'apellido',
            'dni',
            'edad',
            'fecha_fallecimiento',
            'sexo',
            'nacionalidad',
            'estado_civil',
            'domicilio',
            'localidad',
            'codigo_postal'
        ];
        if (!in_array($orderCol, $allowedColumns)) {
            $orderCol = 'id_difunto';
        }

        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT 
                    d.id_difunto,
                    de.nombre AS nombre_deudo,
                    d.nombre,
                    d.apellido,
                    d.dni,
                    d.edad,
                    d.fecha_fallecimiento,
                    COALESCE(s.descripcion, ' ') AS sexo,
                    COALESCE(n.nacionalidad, ' ') AS nacionalidad,
                    COALESCE(ec.descripcion, ' ') AS estado_civil,
                    d.domicilio,
                    d.localidad,
                    d.codigo_postal
                FROM difunto d
                LEFT JOIN deudo de ON d.id_deudo = de.id_deudo
                LEFT JOIN sexo s ON d.id_sexo = s.id_sexo
                LEFT JOIN estado_civil ec ON d.id_estado_civil = ec.id_estado_civil
                LEFT JOIN nacionalidades n ON d.id_nacionalidad = n.id_nacionalidad
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
            'id_difunto',
            'nombre_deudo',
            'nombre',
            'apellido',
            'dni',
            'edad',
            'fecha_fallecimiento',
            'sexo',
            'nacionalidad',
            'estado_civil',
            'domicilio',
            'localidad',
            'codigo_postal'
        ];
        if (!in_array($orderCol, $allowedColumns)) {
            $orderCol = 'id_difunto';
        }

        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT 
                    d.id_difunto,
                    de.nombre AS nombre_deudo,
                    d.nombre,
                    d.apellido,
                    d.dni,
                    d.edad,
                    d.fecha_fallecimiento,
                    COALESCE(s.descripcion, 'No especificado') AS sexo,
                    COALESCE(n.nacionalidad, 'No especificado') AS nacionalidad,
                    COALESCE(ec.descripcion, 'No especificado') AS estado_civil,
                    d.domicilio,
                    d.localidad,
                    d.codigo_postal
                FROM difunto d
                LEFT JOIN deudo de ON d.id_deudo = de.id_deudo
                LEFT JOIN sexo s ON d.id_sexo = s.id_sexo
                LEFT JOIN estado_civil ec ON d.id_estado_civil = ec.id_estado_civil
                LEFT JOIN nacionalidades n ON d.id_nacionalidad = n.id_nacionalidad
                WHERE d.nombre LIKE :search 
                OR d.apellido LIKE :search 
                OR d.dni LIKE :search 
                OR de.nombre LIKE :search 
                OR s.descripcion LIKE :search 
                OR n.nacionalidad LIKE :search 
                OR ec.descripcion LIKE :search 
                OR d.domicilio LIKE :search 
                OR d.localidad LIKE :search
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

    public function yaAlocado(int $id_difunto): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM parcela_difunto 
                WHERE id_difunto = :id_difunto";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_difunto' => $id_difunto]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
