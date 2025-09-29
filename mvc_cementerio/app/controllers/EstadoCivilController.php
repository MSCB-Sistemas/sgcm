<?php
class EstadoCivilController extends Control
{
    private EstadoCivilModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("EstadoCivilModel");
    }
    public function index()
    {
        $estadosCiviles = $this->model->getAllEstadosCiviles();

        $datos = [
            'title' => 'Lista de estados civiles',
            'urlCrear' => URL . 'estadoCivil/create',
            'columnas' => ['ID', 'Descripcion'],
            'columnas_claves' => ['id_estado_civil', 'descripcion'],
            'data' => $estadosCiviles,
            'acciones' => function ($fila) {
                $id = $fila['id_estado_civil'];
                $url = URL . 'estadoCivil';
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
            'title' => 'Crear estado civil',
            'action' => URL . 'estadoCivil/save',
            'values' => [],
            'errores' => [],
        ];

        $this->loadView('estados_civiles/EstadoCivilForm', $datos);
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

            if (empty($descripcion)) $errores[] = "La descripcion es obligatoria.";

            if (!empty($errores)) {
                $this->loadView('estados_civiles/EstadoCivilForm', [
                    'title' => 'Crear estado civil',
                    'action' => URL . 'estadoCivil/save',
                    'values' => [],
                    'errores' => $errores
                ]);
                return;
            }

            $id_estado_civil = $this->model->insertEstadoCivil($descripcion);
            if ($id_estado_civil) {
                header('Location: ' . URL . 'estadoCivil');
                exit;
            } else {
                die('Error al guardar el estado civil.');
            }
        }
    }

    public function edit($id)
    {
        $estadoCivil = $this->model->getEstadoCivil($id);

        if (!$estadoCivil) {
            die("Estado civil no encontrado.");
        }

        $this->loadView("estados_civiles/EstadoCivilForm", [
            'title' => 'Editar estado civil',
            'action' => URL . 'estadoCivil/update/' . $id,
            'values' => [
                'descripcion' => $estadoCivil['descripcion'],
            ],
            'errores' => [],
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
            
            if (empty($descripcion)) $errores[] = "La descripcion es obligatoria.";

            if (!empty($errores)) {
                $estadoCivil = [
                    "descripcion" => $descripcion,
                ];

                $this->loadView("estados_civiles/EstadoCivilForm", [
                    'title' => 'Editar estado civil',
                    'action' => URL . 'estadoCivil/update/' . $id,
                    'values' => $estadoCivil,
                    'errores' => $errores,
                ]);
                return;
            }

            if ($this->model->updateEstadoCivil($id, $descripcion)) {
                header("Location: " . URL . "estadoCivil");
                exit;
            } else {
                die("Error al actualizar el estado civil.");
            }
        }
    }

    public function delete($id)
    {
        $eliminado = $this->model->deleteEstadoCivil($id);

        if (!$eliminado) {
            die('Error al eliminar el estado civil.');
        }

        header("Location: " . URL . "estadoCivil");
        exit;
    }
}
?>