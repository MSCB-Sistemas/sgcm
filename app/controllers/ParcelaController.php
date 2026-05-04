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
        $this->model            = $this->loadModel("ParcelaModel");
        $this->tipoParcelaModel = $this->loadModel("TipoParcelaModel");
        $this->deudoModel       = $this->loadModel("DeudoModel");
        $this->orientacionModel = $this->loadModel("OrientacionModel");
    }

    public function index()
    {
        $puedeCrear = $this->can('crear_parcela');
        $puedeEditar = $this->can('editar_parcela');
        $puedeEliminar = $this->can('eliminar_parcela');

        $parcela = $this->model->getAllParcelas();

        $datos = [
            'title'             => 'Lista de parcelas',
            'urlCrear'          => URL . 'parcela/create',
            'columnas'          => ['ID', 'Tipo', 'Deudo', 'Numero ubicacion', 'Hilera', 'Seccion', 'Fraccion', 'Nivel', 'Orientacion'],
            'columnas_claves'   => ['id_parcela', 'tipo_parcela', 'nombre_deudo', 'numero_ubicacion', 'hilera', 'seccion', 'fraccion', 'nivel', 'orientacion'],
            'data'              => $parcela,
            'acciones'          => function (array $fila) use ($puedeEditar, $puedeEliminar)
            {
                $id = $fila['id_parcela'];
                $url = rtrim(URL, '/') . '/parcela';

                $html = '';
                if ($puedeEditar) {
                    $html .= '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a> ';
                }
                if ($puedeEliminar) {
                    $html .= '<form action="' . $url . '/delete/' . $id . '" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este usuario?\');">'
                        . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                        . '</form>';
                }
                return $html;
            },
            'puedeCrear' => $puedeCrear, 
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
            'title'             => 'Crear parcela',
            'action'            => URL . 'parcela/save',
            'values'            => [],
            'errores'           => [],
            'tipos_parcelas'    => $tipos_parcelas,
            'deudos'            => $deudos,
            'orientaciones'     => $orientaciones
        ];

        $this->loadView('parcelas/ParcelaForm', $datos);
    }

    
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URL . "parcela/create");
            exit;
        }

        $tipo_parcela = $_POST['tipo_parcela'];
        if (isset($_POST['deudo'])) {
            $id_deudo = $_POST['deudo'];
        } else {
            $id_deudo = '';
        }
        $nro_ubicacion = trim($_POST['numero_ubicacion']);
        $hilera = trim($_POST['hilera']);
        $seccion = trim($_POST['seccion']);
        $fraccion = trim($_POST['fraccion']);
        $nivel = trim($_POST['nivel']);
        
        if (isset($_POST['orientacion'])) {
            $orientacion = $_POST['orientacion'];
        } else {
            $orientacion = null;
        }

        $errores = [];

        if (empty($tipo_parcela)) $errores[]    = "El tipo de parcela es obligatorio.";

        $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (!empty($errores)) {
            if ($es_ajax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errores]);
                exit;
            } else {
                $datos_error = [
                    'title' => 'Crear parcela', 'action' => URL . 'parcela/save',
                    'values' => $_POST, 'errores' => $errores,
                    'tipos_parcelas' => $this->tipoParcelaModel->getAllTiposParcelas(),
                    'deudos' => $this->deudoModel->getAllDeudos(),
                    'orientaciones' => $this->orientacionModel->getAllOrientaciones()
                ];

                $this->loadView('parcelas/ParcelaForm', $datos_error);
                return;
            }
        }

        $nuevo_ingreso = $this->model->insertParcela($tipo_parcela, $id_deudo, $nro_ubicacion, $hilera, $seccion, $fraccion, $nivel, $orientacion);

        if ($nuevo_ingreso) {
            if ($es_ajax) {
                header('Content-Type: application/json');
                $ubic = !empty($nro_ubicacion) ? $nro_ubicacion : 'S/N';
                $sec = !empty($seccion) ? $seccion : 'S/S';
                $hil = !empty($hilera) ? $hilera : 'S/H';
                $texto_parcela = "ID: $nuevo_ingreso | Ubic: $ubic | Sec: $sec | Hil: $hil";
                
                echo json_encode([
                    'success' => true,
                    'newItem' => [
                        'id'   => $nuevo_ingreso,
                        'text' => $texto_parcela
                    ]
                ]);
                exit;
            } else {
                header("Location: " . URL . "parcela");
                exit;
            }
        } else {
            $errorMsg = ['Error al guardar la parcela en la base de datos.'];
            if ($es_ajax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errorMsg]);
                exit;
            } else {
                die($errorMsg[0]);
            }
        }
    }

    public function edit($id)
    {
        $parcela        = $this->model->getParcela($id);
        $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
        $deudos         = $this->deudoModel->getAllDeudos();
        $orientaciones  = $this->orientacionModel->getAllOrientaciones();

        if (!$parcela) {
            die("Parcela no encontrada");
        }

        $this->loadView('parcelas/ParcelaForm', [
            'title'     => 'Editar parcela',
            'action'    => URL . 'parcela/update/' . $id,
            'values'    => [
                'tipo_parcela'      => $parcela['id_tipo_parcela'],
                'deudo'             => $parcela['id_deudo'],
                'numero_ubicacion'  => $parcela['numero_ubicacion'],
                'hilera'            => $parcela['hilera'],
                'seccion'           => $parcela['seccion'],
                'fraccion'          => $parcela['fraccion'],
                'nivel'             => $parcela['nivel'],
                'orientacion'       => $parcela['id_orientacion'],
            ],
            'errores' => [],
            'tipos_parcelas'    => $tipos_parcelas,
            'deudos'            => $deudos,
            'orientaciones'     => $orientaciones,
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipo_parcela = $_POST['tipo_parcela'];
            if (isset($_POST['deudo'])) {
                $deudo = $_POST['deudo'];
            } else {
                $deudo = '';
            }
            if (isset($_POST['numero_ubicacion'])) {
                $nro_ubicacion = trim($_POST['numero_ubicacion']);
            } else {
                $nro_ubicacion = '';
            }
            if (isset($_POST['hilera'])) {
                $hilera = trim($_POST['hilera']);
            } else {
                $hilera = '';
            }
            if (isset($_POST['seccion'])) {
                $seccion = trim($_POST['seccion']);
            } else {
                $seccion = '';
            }
            if (isset($_POST['fraccion'])) {
                $fraccion = trim($_POST['fraccion']);
            } else {
                $fraccion = '';
            }
            if (isset($_POST['nivel'])) {
                $nivel = trim($_POST['nivel']);
            } else {
                $nivel = '';
            }
            if (isset($_POST['orientacion'])) {
                $orientacion = $_POST['orientacion'];
            } else {
                $orientacion = null;
            }

            $errores = [];

            if (empty($tipo_parcela)) $errores[]    = "El tipo de parcela es obligatorio.";

            if (!empty($errores)) {
                $parcela = [
                    'id_parcela'        => $id,
                    'id_tipo_parcela'   => $tipo_parcela,
                    'id_deudo'          => $deudo,
                    'numero_ubicacion'  => $nro_ubicacion,
                    'hilera'            => $hilera,
                    'seccion'           => $seccion,
                    'fraccion'          => $fraccion,
                    'nivel'             => $nivel,
                    'id_orientacion'    => $orientacion
                ];

                $tipos_parcelas = $this->tipoParcelaModel->getAllTiposParcelas();
                $deudos         = $this->deudoModel->getAllDeudos();
                $orientaciones  = $this->orientacionModel->getAllOrientaciones();

                $this->loadView('parcelas/ParcelaForm', [
                    'title'          => 'Editar parcela',
                    'action'         => URL . 'parcela/update/' . $id,
                    'values'         => $parcela,
                    'errores'        => $errores,
                    'tipos_parcelas' => $tipos_parcelas,
                    'deudos'         => $deudos,
                    'orientaciones'  => $orientaciones
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

        $parcela = $model->getParcelaConDeudo($id);
        
        $deudoInfo = null;
        if ($parcela && !empty($parcela['id_deudo'])) {
            $dni = !empty($parcela['dni']) ? $parcela['dni'] : 'S/DNI';
            $nom = !empty($parcela['nombre']) ? $parcela['nombre'] : '';
            $ape = !empty($parcela['apellido']) ? $parcela['apellido'] : '';
            $deudoInfo = [
                'id' => $parcela['id_deudo'],
                'text' => strtoupper("$dni - $ape, $nom")
            ];
        }

        $data = [
            'parcela' => $parcela,
            'deudo' => $deudoInfo,
            'pagos' => $pagosLimitados,
            'difuntos' => $model->obtenerDifuntosPorParcela($id),
        ];

        echo json_encode($data);
        exit;
    }
}
?>