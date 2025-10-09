<?php
class PagoController extends Control {
    private PagoModel $model;
    private DeudoModel $deudoModel;
    private ParcelaModel $parcelaModel;
    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->requireLogin();
        $this->model        = $this->loadModel("PagoModel");
        $this->deudoModel   = $this->loadModel("DeudoModel");
        $this->parcelaModel = $this->loadModel("ParcelaModel");
        $this->usuarioModel = $this->loadModel("UsuarioModel");
    }

    public function index() {
        $puedeCrear         = $this->can('crear_pago');
        $puedeEditar        = $this->can('editar_pago');
        $puedeEliminar      = $this->can('eliminar_pago');

        $datos = [
            'title'         => 'Lista de pagos',
            'urlCrear'      => URL . 'pago/create',
            'ajaxUrl'       => URL . 'pago/ajax',
            'baseUrl'       => URL . 'pago/',
            'columnas'      => ['ID', 'Deudo', 'Parcela', 'Fecha de pago', 'Fecha de vencimiento', 'Importe', 'Recargo', 'Total', 'Usuario'],
            'columnsConfig' => [
                ['data' => 'id_pago'],
                ['data' => 'nombre_deudo'],
                ['data' => 'parcela'],
                ['data' => 'fecha_pago', 'render' => function($data) {
                    if ($data){
                        return date('d/m/Y', strtotime($data));
                    }
                    else{
                        return '-';
                    }
                }],
                ['data' => 'fecha_vencimiento', 'render' => function($data) {
                    if ($data){
                        return date('d/m/Y', strtotime($data));
                    }
                    else{
                        return '-';
                    }
                }],
                ['data' => 'importe', 'render' => function($data) {
                    return '$ ' . number_format($data, 2);
                }],
                ['data' => 'recargo', 'render' => function($data) {
                    return '$ ' . number_format($data, 2);
                }],
                ['data' => 'total', 'render' => function($data) {
                    return '$ ' . number_format($data, 2);
                }],
                ['data' => 'usuario'],
                ['data' => 'acciones', 'orderable' => false, 'searchable' => false]
            ],
            'puedeCrear' => $puedeCrear,
            'errores' => [],
        ];

        $this->loadView('partials/tablaAbmAjax', $datos);
    }

    public function create() {
        $deudos                 = $this->deudoModel->getAllDeudos();
        $parcelas               = $this->parcelaModel->getAllParcelas();
        $usuarios               = $this->usuarioModel->getAllUsuarios();
        $values['id_usuario']   = $_SESSION['usuario_id'];

        $datos = [
            'title'     => 'Crear pago',
            'action'    => URL . 'pago/save',
            'values'    => $values,
            'errores'   => [],
            'deudos'    => $deudos,
            'parcelas'  => $parcelas,
            'usuarios'  => $usuarios
        ];

        $this->loadView('pagos/PagosForm', $datos);
    }


    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['deudo'])) {
                $deudo = $_POST['deudo'];
            } else {
                $deudo = '';
            }
            if (isset($_POST['parcela'])) {
                $parcela = $_POST['parcela'];
            } else {
                $parcela = '';
            }
            if (isset($_POST['fecha_pago'])) {
                $fecha_pago = trim($_POST['fecha_pago']);
            } else {
                $fecha_pago = '';
            }
            if (isset($_POST['fecha_vencimiento'])) {
                $fecha_vencimiento = trim($_POST['fecha_vencimiento']);
            } else {
                $fecha_vencimiento = '';
            }
            if (isset($_POST['importe'])) {
                $importe = trim($_POST['importe']);
            } else {
                $importe = '';
            }
            if (isset($_POST['recargo'])) {
                $recargo = trim($_POST['recargo']);
            } else {
                $recargo = '';
            }
            if (isset($_POST['total'])) {
                $total = trim($_POST['total']);
            } else {
                $total = '';
            }
            $usuario = $_SESSION['usuario_id'];
            $errores = [];

            if (empty($deudo))              $errores[] = 'El deudo es obligatorio';
            if (empty($parcela))            $errores[] = 'La parcela es obligatoria';
            if (empty($fecha_pago))         $errores[] = 'La fecha es obligatoria';
            if (empty($fecha_vencimiento))  $errores[] = 'La fecha de vencimiento es obligatoria';
            if (empty($importe))            $errores[] = 'El importe es obligatorio';
            if (empty($recargo))            $errores[] = 'El recargo es obligatorio';
            if (empty($total))              $errores[] = 'El total es obligatorio';
            if (empty($usuario))            $errores[] = 'El usuario es obligatorio';

            if (!empty($errores)) {
                $deudos     = $this->deudoModel->getAllDeudos();
                $parcelas   = $this->parcelaModel->getAllParcelas();
                $usuarios   = $this->usuarioModel->getAllUsuarios();

                $this->loadView('pagos/PagosForm', [
                    'title'     => 'Crear pago',
                    'action'    => URL . 'parcela/save',
                    'values'    => $_POST,
                    'errores'   => $errores,
                    'deudos'    => $deudos,
                    'parcelas'  => $parcelas,
                    'usuarios'  => $usuarios
                ]);
                return;
            }

            if ($this->model->insertPago($deudo, $parcela, $fecha_pago, $fecha_vencimiento, $importe, $recargo, $total, $usuario)) {
                header("Location: " . URL . "pago");
                exit;
            } else {
                die("Error al guardar el pago");
            }
        }
    }

    public function edit($id) {
        $pago       = $this->model->getPago($id);
        $deudos     = $this->deudoModel->getAllDeudos();
        $parcelas   = $this->parcelaModel->getAllParcelas();

        if (!$pago) {
            die("Pago no encontrado");
        }

        $this->loadView("pagos/PagosForm", [
            'title'     => 'Editar pago',
            'action'    => URL . 'pago/update/' . $id,
            'values'    => [
                'deudo'             => $pago['id_deudo'],
                'parcela'           => $pago['id_parcela'],
                'fecha_pago'        => $pago['fecha_pago'],
                'fecha_vencimiento' => $pago['fecha_vencimiento'],
                'importe'           => $pago['importe'],
                'recargo'           => $pago['recargo'],
                'total'             => $pago['total'],
            ],
            'errores'   => [],
            'deudos'    => $deudos,
            'parcelas'  => $parcelas
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['deudo'])) {
                $deudo = $_POST['deudo'];
            } else {
                $deudo = '';
            }
            if (isset($_POST['parcela'])) {
                $parcela = $_POST['parcela'];
            } else {
                $parcela = '';
            }
            if (isset($_POST['fecha_pago'])) {
                $fecha_pago = trim($_POST['fecha_pago']);
            } else {
                $fecha_pago = '';
            }
            if (isset($_POST['fecha_vencimiento'])) {
                $fecha_vencimiento = trim($_POST['fecha_vencimiento']);
            } else {
                $fecha_vencimiento = '';
            }
            if (isset($_POST['importe'])) {
                $importe = trim($_POST['importe']);
            } else {
                $importe = '';
            }
            if (isset($_POST['recargo'])) {
                $recargo = trim($_POST['recargo']);
            } else {
                $recargo = '';
            }
            if (isset($_POST['total'])) {
                $total = trim($_POST['total']);
            } else {
                $total = '';
            }
            $usuario = $_SESSION['usuario_id'];
            $errores = [];

            if (empty($deudo))              $errores[] = 'El deudo es obligatorio';
            if (empty($parcela))            $errores[] = 'La parcela es obligatoria.';
            if (empty($fecha_pago))         $errores[] = 'La fecha es obligatoria';
            if (empty($fecha_vencimiento))  $errores[] = 'La fecha de vencimiento es obligatoria';
            if (empty($importe))            $errores[] = 'El importe es obligatorio';
            if (empty($recargo))            $errores[] = 'El recargo es obligatorio';
            if (empty($total))              $errores[] = 'El total es obligatorio';

            if (!empty($errores)) {
                $pago = [
                    'id_pago'           => $id,
                    'id_deudo'          => $deudo,
                    'id_parcela'        => $parcela,
                    'fecha_pago'        => $fecha_pago,
                    'fecha_vencimiento' => $fecha_vencimiento,
                    'importe'           => $importe,
                    'recargo'           => $recargo,
                    'total'             => $total,
                ];

                $deudos = $this->deudoModel->getAllDeudos();
                $parcelas = $this->parcelaModel->getAllParcelas();

                $this->loadView('pagos/PagosForm', [
                    'title'     => 'Editar pago',
                    'action'    => URL . 'pago/update/' . $id,
                    'values'    => $pago,
                    'errores'   => $errores,
                    'deudos'    => $deudos,
                    'parcelas'  => $parcelas,
                ]);
                return;
            }

            if ($this->model->updatePago($id, $deudo, $parcela, $fecha_pago, $fecha_vencimiento, $importe, $recargo, $total, $usuario)) {
                header("Location: " . URL . "pago");
                exit;
            } else {
                die("Error al actualizar el pago.");
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deletePago($id)) {
            header("Location: " . URL . "pago");
            exit;
        } else {
            die("No se pudo eliminar el pago");
        }
    }

    public function ajax() {
        header('Content-Type: application/json; charset=utf-8');
        
        function getPost($key, $default = '') {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            } else {
                return $default;
            }
        }

        function getNestedPost($keys, $default = '') {
            $value = $_POST;
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    return $default;
                }
                $value = $value[$key];
            }
            return $value;
        }

        $draw               = getPost('draw', 1);
        $start              = intval(getPost('start', 0));
        $length             = intval(getPost('length', 10));
        $search             = getNestedPost(['search', 'value'], '');
        $orderColumnIndex   = getNestedPost(['order', 0, 'column'], 0);
        $orderDir           = getNestedPost(['order', 0, 'dir'], 'asc');

        $columns = [
            'id_pago', 'nombre_deudo', 'parcela', 'fecha_pago', 
            'fecha_vencimiento', 'importe', 'recargo', 'total', 'usuario'
        ];

        if (isset($columns[$orderColumnIndex])) {
            $orderCol = $columns[$orderColumnIndex];
        } else {
            $orderCol = 'id_pago';
        }

        $totalRecords = $this->model->countAll();


        if ($search) {
            $data = $this->model->getFiltered($search, $orderCol, $orderDir, $start, $length);
            $filteredRecords = $this->model->countFiltered($search);
        } else {
            $data = $this->model->getPage($orderCol, $orderDir, $start, $length);
            $filteredRecords = $totalRecords;
        }

        foreach ($data as &$fila) {
            $id  = $fila['id_pago'];
            $url = rtrim(URL,'/') . '/pago';
    
            $acciones = '';
    
            if ($this->can('editar_pago')) {
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> '
                           . '</form> ';
            }
    
            if ($this->can('eliminar_pago')) {
                $acciones .= '<form action="'.$url.'/delete/'.$id.'" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este pago?\');">'
                           . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                           . '</form>';
            }
    
            $fila['acciones'] = $acciones;
        }  

        echo json_encode([
            "draw"              => intval($draw),
            "recordsTotal"      => $totalRecords,
            "recordsFiltered"   => $filteredRecords,
            "data"              => $data
        ]);
        exit;
    }

    public function registrarPagoMantenimiento()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . '/estadisticas');
            exit;
        }

        $deudo_id          = $_POST['deudo_id'] ?? null;
        $parcela_id        = $_POST['parcela_id'] ?? null;
        $monto             = $_POST['monto'] ?? 0;
        $fecha_pago        = $_POST['fecha_pago'] ?? date('Y-m-d');
        
        $fecha_vencimiento_nueva = $_POST['fecha_vencimiento'] ?? null;
        
        $usuario_id        = $_SESSION['usuario_id'];
        $tipo_operacion_id = 1;

        if (!$deudo_id || !$parcela_id || !$fecha_vencimiento_nueva || !is_numeric($monto)) {
            die("Error: Faltan datos esenciales o el monto no es válido.");
        }

        $pagoExitoso = $this->model->insertarPagoMantenimiento(
            $deudo_id,
            $parcela_id,
            $tipo_operacion_id,
            $fecha_pago,
            $fecha_vencimiento_nueva,
            $monto,
            $usuario_id
        );

        if ($pagoExitoso) {
            header('Location: ' . URL . '/estadisticas#morosos');
            exit;
        } else {
            die("Error al guardar el pago en la base de datos.");
        }
    }
}
?>