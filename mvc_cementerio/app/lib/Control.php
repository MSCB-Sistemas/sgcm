<?php
class Control
{
    public function __construct()
    {
        session_start();
    }

    protected function loadModel($model)
    {
        require_once APP .'/models/' . $model . '.php';
        return new $model;
    }

    protected function loadView(string $view, array $datos = [], string $layout = 'main')
    {
        $viewFile = APP . '/views/pages/' . $view . '.php';
        if (!file_exists($viewFile)) { die($viewFile); }

        // FLASH de error o confirmacion de algún middleware/controlador.
        if (isset($_SESSION['flash_error'])) {
            $datos['flash_error'] = $_SESSION['flash_error'];
        
            // los borra en la sesión así no aparece en el proximo request
            unset($_SESSION['flash_error']);
        } else {
            
            // La vista lo recibe solo en la primera carga
            $datos['flash_error'] = null;
        }
        
        if (isset($_SESSION['flash_ok'])) {
            $datos['flash_ok'] = $_SESSION['flash_ok'];
            unset($_SESSION['flash_ok']);
        } else {
            $datos['flash_ok'] = null;
        }

        // Variables disponibles en la vista
        extract($datos, EXTR_SKIP);

        if ($layout) {
            $viewPath = $viewFile;  // el layout hace require de este path
            require_once APP . "/views/layout/{$layout}.php";
        } else {
            require_once $viewFile;
        }
    }
    
    protected function isLogin(): bool  { return !empty($_SESSION['usuario_id']); }
    protected function userId(): ?int   
    { 
        if (isset($_SESSION['usuario_id'])) {
            return (int)$_SESSION['usuario_id'];
        } else {
            return null;
        }
    }
    protected function roleId(): ?int   
    { 
        if (isset($_SESSION['usuario_tipo'])) {
            return (int)$_SESSION['usuario_tipo'];
        } else {
            return null;
        }
    }

    protected function permisos(): array 
    {   
        if (isset($_SESSION['usuario_permisos'])) {
            $permisos = $_SESSION['usuario_permisos'];
        } else {
            $permisos = [];
        }
        return $permisos;
    }

    protected function can(string $permiso): bool 
    {   
        return in_array($permiso, $this->permisos(), true);
    }

    protected function requirePermissionInController(string|array $permisos, ?string $redirect = null): void 
    {
        requirePermission($permisos, $redirect); // reusa el helper global
    }

    protected function redirect(string $path, int $code = 303): void
    {
        $base = rtrim(URL, '/');
        header('Location: ' . $base . '/' . ltrim($path, '/'), true, $code);
        exit;
    }

    protected function requireLogin(string $redirect = URL . 'login'): void
    {
        // NO session_start() acá: la sesión ya se abrió en init.php
        if (!($this->isLogin())) {
            $_SESSION['flash_error'] = 'Debés iniciar sesión.';
            header('Location: ' . rtrim($redirect, '/'));
            exit;
        }
    }
}
?>