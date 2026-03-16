<?php
class TipoOperacionController extends Control
{
    private TipoOperacionModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("TipoOperacionModel");
    }

    public function index()
    {
        $operaciones = $this->model->getAllTipoOperaciones();

        $datos = [
            'title' => 'Lista de operaciones',
            'urlCrear' => URL . 'tipoOperacion/create',
            'columnas' => ['ID', 'Descripcion'],
            'columnas_claves' => ['id_tipo_operacion', 'descripcion'],
            'data' => $operaciones,
            'acciones' => function ($fila) {
                $id = $fila['id_tipo_operacion'];
                $url = URL . 'tipoOperacion';
                return '
                <a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a>
                <a href="' . $url . '/delete/' . $id . '" class="btn btn-sm btn-danger">Eliminar</a>
                ';
            },
            'errores' => [],
        ];

        $this->loadView('partials/tablaAbm', $datos);
    }

    public function create()
    {
        $datos = [
            'title' => 'Crear tipo de operacion',
            'action' => URL . 'tipoOperacion/save',
            'values' => [],
            'errores' => [],
        ];

        $this->loadView('tipo_operaciones/TipoOperacionForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $descripcion = trim($_POST['descripcion']);
            $errores = [];

            if (empty($descripcion))
                $errores[] = 'La descripcion es obligatoria';

            if (!empty($errores)) {
                $this->loadView('tipo_operaciones/TipoOperacionForm', [
                    'title' => 'Crear tipo operacion',
                    'action' => URL . 'tipoOperacion/save',
                    'values' => [],
                    'errores' => $errores
                ]);
                return;
            }

            $id_tipo_operacion = $this->model->insertTipoOperacion($descripcion);
            if ($id_tipo_operacion) {
                header('Location: ' . URL . 'tipoOperacion');
                exit;
            } else {
                die('Error al guardar el tipo de operacion.');
            }
        }
    }

    public function edit($id)
    {
        $tipo_operacion = $this->model->getTipoOperacion($id);

        if (!$tipo_operacion) {
            die("Tipo de operacion no encontrado");
        }

        $this->loadView("tipo_operaciones/TipoOperacionForm", [
            'title' => 'Editar tipo de operacion',
            'action' => URL . 'tipoOperacion/update/' . $id,
            'values' => [
                'descripcion' => $tipo_operacion['descripcion'],
            ],
            'errores' => []
        ]);
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $descripcion = trim($_POST['descripcion']);
            $errores = [];

            if (empty($descripcion))
                $errores[] = 'La descripcion no puede quedar vacia.';

            if (!empty($errores)) {
                $tipo_operacion = [
                    'descripcion' => $descripcion
                ];

                $this->loadView("tipo_operaciones/TipoOperacionForm", [
                    'title' => 'Editar tipo de operacion',
                    'action' => URL . 'tipoOperacion/update/' . $id,
                    'values' => $tipo_operacion,
                    'errores' => $errores
                ]);
                return;
            }

            if ($this->model->updateTipoOperacion($id, $descripcion)) {
                header('Location: ' . URL . 'tipoOperacion');
                exit;
            } else {
                die('Error al actualizar el tipo de operacion.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteTipoOperacion($id)) {
            header('Location: ' . URL . 'tipoOperacion');
            exit;
        } else {
            die('No se pudo eliminar el tipo de operacion');
        }
    }
}
?>