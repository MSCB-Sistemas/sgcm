<?php
class AuthController extends Control 
{
    private PermisoModel $permisoModel;

    public function __construct()
    {
        $this->permisoModel = $this->loadModel('PermisoModel');
    }


    public function login() 
    {
        $datos = ['title' => 'Login'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') 
        {
            $user     = trim($_POST['user']);
            $password = trim($_POST['password']);

            if (empty($user) || empty($password)) {
                $datos['error'] = 'Debe ingresar usuario y contraseña';
                $this->loadView('LoginView', $datos, 'login');
                exit;
            }

            $usuarioModel = $this->loadModel('UsuarioModel');
            $usuario = $usuarioModel->getUsuarioByNombreUsuario($user);

            if ($usuario && password_verify($password, $usuario['contrasenia'])) 
            {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION['usuario_id']         = (int)$usuario['id_usuario'];
                $_SESSION['usuario_nombre']     = $usuario['nombre'];
                $_SESSION['usuario_apellido']   = $usuario['apellido'];
                $_SESSION['usuario_tipo']       = $usuario['id_tipo_usuario'];

                $permisos = $this->permisoModel->getPermisosPorRol($usuario['id_tipo_usuario']);

                if ($permisos !== null) {
                    $_SESSION['usuario_permisos'] = $permisos;
                } else {
                    $_SESSION['usuario_permisos'] = [];
                }

                header('Location: ' . URL . 'home');
                exit;
            } else {
                $datos['error'] = 'Credenciales incorrectas';
                $this->loadView('LoginView', $datos, 'login');
            }
        } else {
            $this->loadView('LoginView', $datos, 'login');
        }
        $this->loadView('LoginView', $datos, 'login');
        
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        header('Location: ' . URL . 'login');
        exit;
    }
}
?>