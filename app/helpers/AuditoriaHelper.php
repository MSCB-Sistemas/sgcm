<?php 
require_once '/../models/Database.php';

class AuditoriaHelper {
    public static function log(
        int $id_usuario, 
        string $query_sql, 
        array $parametros, 
        string $model, 
        string $accion
    ): bool {
        try {
            $db = Database::connect();

            if ($id_usuario === null && isset($_SESSION['id_usuario'])) {
                $id_usuario = $_SESSION['id_usuario'];
            }

            $sql = "INSERT INTO auditoria (id_usuario, creado_en, query_sql, parametros, model, accion) 
                    VALUES (:id_usuario, :creado_en, :query_sql, :parametros, :model, :accion)";

            if (!empty($parametros)){
                $parametrosJSON = json_encode($parametros, JSON_UNESCAPED_UNICODE);
            }else{
                $parametrosJSON = null;
            }

            $paramsInsert = [
                'id_usuario' => $id_usuario,
                'creado_en'  => date('Y-m-d H:i:s'),
                'query_sql'  => $query_sql,
                'parametros' => $parametrosJSON,
                'model'      => $model,
                'accion'     => $accion
            ];

            $stmt = $db->prepare($sql);

            if (!$stmt->execute($paramsInsert)) {
                error_log("Error al insertar auditoría: " . implode(" | ", $stmt->errorInfo()));
                return false;
            }
            return true;

        } catch (Exception $e) {
            error_log("Error en auditoría: " . $e->getMessage());
            return false;
        }
    }
}
?>