<?php
class TipoParcelaController extends Control
{
    private TipoParcelaModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("TipoParcelaModel");
    }

    public function index()
    {
        $tipos_parcelas = $this->model->getAllTiposParcelas();

        $datos = [
            'title' => 'Lista de tipos de parcelas',
            'urlCrear' => URL . 'tipoParcela/create',
            'columnas' => ['ID', 'Descripcion'],
            'columnas_claves' => ['id_tipo_parcela', 'nombre_parcela'],
            'data' => $tipos_parcelas,
            'acciones' => function ($fila) {
                $id = $fila['id_tipo_parcela'];
                $url = URL . 'tipoParcela';
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
            'title' => 'Crear tipo parcela',
            'action' => URL . 'tipoParcela/save',
            'values' => [],
            'errores' => [],
        ];

        $this->loadView('tipos_parcelas/TipoParcelaForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['nombre_parcela'])) {
                $nombre_parcela = trim($_POST['nombre_parcela']);
            } else {
                $nombre_parcela = '';
            }
            $errores = [];

            if (empty($nombre_parcela))
                $errores[] = 'El nombre de la parcela es obligatoria';

            if (!empty($errores)) {
                $this->loadView('tipos_parcelas/TipoParcelaForm', [
                    'title' => 'Crear tipo de parcela',
                    'action' => URL . 'tipoParcela/save',
                    'values' => [],
                    'errores' => $errores
                ]);
                return;
            }

            $id_tipo_parcela = $this->model->insertTipoParcela($nombre_parcela);
            if ($id_tipo_parcela) {
                header('Location: ' . URL . 'tipoParcela');
                exit;
            } else {
                die('Error al guardar el tipo de parcela.');
            }
        }
    }

    public function edit($id)
    {
        $tipo_parcela = $this->model->getTipoParcela($id);

        if (!$tipo_parcela) {
            die("Tipo de parcela no encontrado");
        }

        $this->loadView("tipos_parcelas/TipoParcelaForm", [
            'title' => 'Editar tipo de parcela',
            'action' => URL . 'tipoParcela/update/' . $id,
            'values' => [
                'descripcion' => $tipo_parcela['nombre_parcela'],
            ],
            'errores' => []
        ]);
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['nombre_parcela'])) {
                $nombre_parcela = trim($_POST['nombre_parcela']);
            } else {
                $nombre_parcela = '';
            }
            $errores = [];

            if (empty($nombre_parcela))
                $errores[] = 'El nombre de la parcela no puede quedar vacia.';

            if (!empty($errores)) {
                $tipo_parcela = [
                    'nombre_parcela' => $nombre_parcela
                ];

                $this->loadView("tipos_parcelas/TipoParcelaForm", [
                    'title' => 'Editar tipo de parcela',
                    'action' => URL . 'tipoParcela/update/' . $id,
                    'values' => $tipo_parcela,
                    'errores' => $errores
                ]);
                return;
            }

            if ($this->model->updateTipoParcela($id, $nombre_parcela)) {
                header('Location: ' . URL . 'tipoParcela');
                exit;
            } else {
                die('Error al actualizar el tipo de parcela.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteTipoParcela($id)) {
            header('Location: ' . URL . 'tipoParcela');
            exit;
        } else {
            die('No se pudo eliminar el tipo de parcela.');
        }
    }
}
?>