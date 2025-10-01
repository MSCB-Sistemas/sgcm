<?php
class ParcelaController extends Control
{
    private ParcelaModel $model;
    private TipoParcelaModel $tipoParcelaModel;
    private DeudoModel $deudoModel;
    private OrientacionModel $orientacionModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("ParcelaModel");
        $this->tipoParcelaModel = $this->loadModel("TipoParcelaModel");
        $this->deudoModel = $this->loadModel("DeudoModel");
        $this->orientacionModel = $this->loadModel("OrientacionModel");
    }

    public function index()
    {
        $puedeCrear = $this->can('crear_parcela');
        $puedeEditar = $this->can('editar_parcela');
        $puedeEliminar = $this->can('eliminar_parcela');

        $parcela = $this->model->getAllParcelas();

        $datos = [
            'title' => 'Lista de parcelas',
            'urlCrear' => URL . 'parcela/create',
            'columnas' => ['ID', 'Tipo', 'Deudo', 'Numero ubicacion', 'Hilera', 'Seccion', 'Fraccion', 'Nivel', 'Orientacion'],
            'columnas_claves' => ['id_parcela', 'tipo_parcela', 'nombre_deudo', 'numero_ubicacion', 'hilera', 'seccion', 'fraccion', 'nivel', 'orientacion'],
            'data' => $parcela,
            'acciones' => function (array $fila) use ($puedeEditar, $puedeEliminar) {
                $id = $fila['id_parcela'];
                $url = rtrim(URL, '/') . '/parcela';

                $html = '';
                if ($puedeEditar) {
                    $html .= '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a> ';
                    $html .= '<form action="' . $url . '/activate/' . $id . '" method="post" style="display:inline">'
                        . '<button class="btn btn-sm btn-success" onclick="return confirm(\'¿Activar este usuario?\');">Activar</button>'
                        . '</form> ';
                }
                if ($puedeEliminar) {
                    $html .= '<form action="' . $url . '/delete/' . $id . '" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este usuario?\');">'
                        . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                        . '</form>';
                }
                return $html;
            },
            'puedeCrear' => $puedeCrear,   // por si tu partial muestra el botón “Nuevo”
            'errores' => [],
        ];

        $this->loadView('partials/tablaAbm', $datos);
    }

    public function create()
    {
        $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
        $deudos = $this->deudoModel->getAllDeudos();
        $orientaciones = $this->orientacionModel->getAllOrientaciones();

        $datos = [
            'title' => 'Crear parcela',
            'action' => URL . 'parcela/save',
            'values' => [],
            'errores' => [],
            'tipos_parcelas' => $tipos_parcelas,
            'deudos' => $deudos,
            'orientaciones' => $orientaciones
        ];

        $this->loadView('parcelas/ParcelaForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipo_parcela = $_POST['tipo_parcela'] ?? '';
            $deudo = $_POST['deudo'] ?? '';
            $nro_ubicacion = trim($_POST['numero_ubicacion'] ?? '');
            $hilera = trim($_POST['hilera'] ?? '');
            $seccion = trim($_POST['seccion'] ?? '');
            $fraccion = trim($_POST['fraccion'] ?? '');
            $nivel = trim($_POST['nivel'] ?? '');
            $orientacion = $_POST['orientacion'] ?? '';
            $errores = [];

            if (empty($tipo_parcela))
                $errores[] = "El tipo de parcela es obligatorio.";
            if (empty($orientacion))
                $errores[] = "La orientacion de la parcela es obligatoria.";

            if (!empty($errores)) {
                $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
                $deudos = $this->deudoModel->getAllDeudos();
                $orientaciones = $this->orientacionModel->getAllOrientaciones();

                $this->loadView('parcelas/ParcelaForm', [
                    'title' => 'Crear parcela',
                    'action' => URL . 'parcela/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'tipos_parcelas' => $tipos_parcelas,
                    'deudos' => $deudos,
                    'orientaciones' => $orientaciones,
                ]);
                return;
            }

            if ($this->model->insertParcela($tipo_parcela, $deudo, $nro_ubicacion, $hilera, $seccion, $fraccion, $nivel, $orientacion)) {
                header("Location: " . URL . "parcela");
                exit;
            } else {
                die("Error al guardar la parcela");
            }
        }
    }

    public function edit($id)
    {
        $parcela = $this->model->getParcela($id);
        $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
        $deudos = $this->deudoModel->getAllDeudos();
        $orientaciones = $this->orientacionModel->getAllOrientaciones();

        if (!$parcela) {
            die("Parcela no encontrada");
        }

        $this->loadView('parcelas/ParcelaForm', [
            'title' => 'Editar parcela',
            'action' => URL . 'parcela/update/' . $id,
            'values' => [
                'tipo_parcela' => $parcela['id_tipo_parcela'],
                'deudo' => $parcela['id_deudo'],
                'numero_ubicacion' => $parcela['numero_ubicacion'],
                'hilera' => $parcela['hilera'],
                'seccion' => $parcela['seccion'],
                'fraccion' => $parcela['fraccion'],
                'nivel' => $parcela['nivel'],
                'orientacion' => $parcela['id_orientacion'],
            ],
            'errores' => [],
            'tipos_parcelas' => $tipos_parcelas,
            'deudos' => $deudos,
            'orientaciones' => $orientaciones,
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipo_parcela = $_POST['tipo_parcela'] ?? '';
            $deudo = $_POST['deudo'] ?? '';
            $nro_ubicacion = trim($_POST['numero_ubicacion'] ?? '');
            $hilera = trim($_POST['hilera'] ?? '');
            $seccion = trim($_POST['seccion'] ?? '');
            $fraccion = trim($_POST['fraccion'] ?? '');
            $nivel = trim($_POST['nivel'] ?? '');
            $orientacion = $_POST['orientacion'] ?? '';

            if (empty($tipo_parcela))
                $errores[] = "El tipo de parcela es obligatorio.";
            if (empty($orientacion))
                $errores[] = "La orientacion de la parcela es obligatoria.";

            if (!empty($errores)) {
                $parcela = [
                    'id_parcela' => $id,
                    'id_tipo_parcela' => $tipo_parcela,
                    'id_deudo' => $deudo,
                    'numero_ubicacion' => $nro_ubicacion,
                    'hilera' => $hilera,
                    'seccion' => $seccion,
                    'fraccion' => $fraccion,
                    'nivel' => $nivel,
                    'id_orientacion' => $orientacion
                ];

                $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
                $deudos = $this->deudoModel->getAllDeudos();
                $orientaciones = $this->orientacionModel->getAllOrientaciones();

                $this->loadView('parcelas/ParcelaForm', [
                    'title' => 'Editar parcela',
                    'action' => URL . 'parcela/update/' . $id,
                    'values' => $parcela,
                    'errores' => $errores,
                    'tipos_parcelas' => $tipos_parcelas,
                    'deudos' => $deudos,
                    'orientaciones' => $orientaciones
                ]);
                return;
            }

            if ($this->model->updateParcela($id, $tipo_parcela, $deudo, $nro_ubicacion, $hilera, $seccion, $fraccion, $nivel, $orientacion)) {
                header("Location: " . URL . "parcela");
                exit;
            } else {
                die("Error al actualizar la parcela.");
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteParcela($id)) {
            header("Location: " . URL . "parcela");
            exit;
        } else {
            die("No se pudo eliminar la parcela");
        }
    }

    public function obtenerInfoParcela($id = null)
    {
        header('Content-Type: application/json');
        $model = new ParcelaModel();

        $todosLosPagos = $model->obtenerPagosPorParcela($id);
        
        usort($todosLosPagos, function($a, $b) {
            $fechaA = strtotime($a['fecha_pago']);
            $fechaB = strtotime($b['fecha_pago']);
            return $fechaB - $fechaA;
        });
        
        $pagosLimitados = array_slice($todosLosPagos, 0, 10);

        $data = [
            'pagos' => $pagosLimitados,
            'difuntos' => $model->obtenerDifuntosPorParcela($id),
        ];

        echo json_encode($data);
        exit;
    }
}
?>