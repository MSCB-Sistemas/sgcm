<?php
class TipoUsuariosController extends Control
{
    private TiposUsuariosModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("TiposUsuariosModel");
    }

    public function index()
    {
        $tipos_usuarios = $this->model->getAllTiposUsuarios();

        $datos = [
            'title'             => 'Lista de tipos de usuarios',
            'urlCrear'          => URL . 'tipoUsuario/create',
            'columnas'          => ['ID', 'Rol'],
            'columnas_claves'   => ['id_tipo_usuario', 'rol'],
            'data'              => $tipos_usuarios,
            'acciones'          => function ($fila) {
                $id     = $fila['id_tipo_usuario'];
                $url    = URL . 'tipoUsuario';
                return '
                <a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-outline-primary">Editar</a>
                <a href="' . $url . '/delete/' . $id . '" class="btn btn-sm btn-outline-primary">Eliminar</a>
                ';
            },
            'errores' => [],
        ];

        $this->loadView('partials/tablaAbm', $datos);
    }

    public function create()
    {
        $datos = [
            'title'     => 'Crear tipo usuario',
            'action'    => URL . 'tipoUsuario/save',
            'values'    => [],
            'errores'   => [],
        ];

        $this->loadView('tipos_usuarios/TipoUsuarioForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rol = trim($_POST['rol']);
            $errores = [];

            if (empty($rol))
                $errores[] = 'El nombre del tipo de usuario es obligatorio';

            if (!empty($errores)) {
                $this->loadView('tipos_usuarios/TipoUsuarioForm', [
                    'title'     => 'Crear tipo de usuario',
                    'action'    => URL . 'tipoUsuario/save',
                    'values'    => [],
                    'errores'   => $errores
                ]);
                return;
            }

            $id_tipo_usuario = $this->model->insertTipoUsuario($rol);
            if ($id_tipo_usuario) {
                header('Location: ' . URL . 'tipoUsuario');
                exit;
            } else {
                die('Error al guardar el tipo de usuario.');
            }
        }
    }

    public function edit($id)
    {
        $tipo_usuario = $this->model->getTipoUsuario($id);

        if (!$tipo_usuario) {
            die("Tipo de usuario no encontrado");
        }

        $this->loadView("tipos_usuarios/TipoUsuarioForm", [
            'title'     => 'Editar tipo de usuario',
            'action'    => URL . 'tipoUsuario/update/' . $id,
            'values'    => [
                'rol' => $tipo_usuario['rol'],
            ],
            'errores'   => []
        ]);
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rol = trim($_POST['rol']);
            $errores = [];

            if (empty($rol))
                $errores[] = 'El nombre del usuario no puede quedar vacio.';

            if (!empty($errores)) {
                $tipo_usuario = [
                    'rol' => $rol
                ];

                $this->loadView("tipos_usuarios/TipoUsuarioForm", [
                    'title'     => 'Editar tipo de usuario',
                    'action'    => URL . 'tipoUsuario/update/' . $id,
                    'values'    => $tipo_usuario,
                    'errores'   => $errores
                ]);
                return;
            }

            if ($this->model->updateTipoUsuario($id, $rol)) {
                header('Location: ' . URL . 'tipoUsuario');
                exit;
            } else {
                die('Error al actualizar el tipo de usuario.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteTipoUsuario($id)) {
            header('Location: ' . URL . 'tipoUsuario');
            exit;
        } else {
            die('No se pudo eliminar el tipo de usuario.');
        }
    }
}
?>