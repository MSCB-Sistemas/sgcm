<?php
class SexoController extends Control
{
    private SexoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("SexoModel");
    }

    public function index()
    {
        $sexos = $this->model->getAllSexos();

        $datos = [
            'title' => 'Lista de sexos',
            'urlCrear' => URL . 'sexo/create',
            'columnas' => ['ID', 'Descripcion'],
            'columnas_claves' => ['id_sexo', 'descripcion'],
            'data' => $sexos,
            'acciones' => function ($fila) {
                $id = $fila['id_sexo'];
                $url = URL . 'sexo';
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
            'title' => 'Crear sexo',
            'action' => URL . 'sexo/save',
            'values' => [],
            'errores' => [],
        ];

        $this->loadView('sexos/SexoForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['descripcion'])) {
                $descripcion = trim($_POST['descripcion']);
            } else {
                $descripcion = '';
            }
            $errores = [];

            if (empty($descripcion))
                $errores[] = 'La descripcion es obligatoria';

            if (!empty($errores)) {
                $this->loadView('sexos/SexoForm', [
                    'title' => 'Crear sexo',
                    'action' => URL . 'sexo/save',
                    'values' => [],
                    'errores' => $errores
                ]);
                return;
            }

            $id_sexo = $this->model->insertSexo($descripcion);
            if ($id_sexo) {
                header('Location: ' . URL . 'sexo');
                exit;
            } else {
                die('Error al guardar el sexo.');
            }
        }
    }

    public function edit($id)
    {
        $sexo = $this->model->getSexo($id);

        if (!$sexo) {
            die("Sexo no encontrado");
        }

        $this->loadView("sexos/SexoForm", [
            'title' => 'Editar sexo',
            'action' => URL . 'sexo/update/' . $id,
            'values' => [
                'descripcion' => $sexo['descripcion'],
            ],
            'errores' => []
        ]);
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['descripcion'])) {
                $descripcion = trim($_POST['descripcion']);
            } else {
                $descripcion = '';
            }
            $errores = [];

            if (empty($descripcion))
                $errores[] = 'La descripcion no puede quedar vacia.';

            if (!empty($errores)) {
                $sexo = [
                    'descripcion' => $descripcion
                ];

                $this->loadView("sexos/SexoForm", [
                    'title' => 'Editar sexo',
                    'action' => URL . 'sexo/update/' . $id,
                    'values' => $sexo,
                    'errores' => $errores
                ]);
                return;
            }

            if ($this->model->updateSexo($id, $descripcion)) {
                header('Location: ' . URL . 'sexo');
                exit;
            } else {
                die('Error al actualizar el sexo.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteSexo($id)) {
            header('Location: ' . URL . 'sexo');
            exit;
        } else {
            die('No se pudo eliminar el sexo');
        }
    }
}
?>