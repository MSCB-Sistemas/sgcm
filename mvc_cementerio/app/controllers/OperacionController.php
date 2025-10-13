<?php
class OperacionController extends Control
{
    private OperacionModel $model;
    private ParcelaModel $parcelaModel;
    private DeudoModel $deudoModel;
    private DifuntoModel $difuntoModel;
    private TipoOperacionModel $tipoOperacionModel;
    private SexoModel $sexoModel;
    private NacionalidadesModel $nacionalidadesModel;
    private EstadoCivilModel $estadoCivilModel;
    private TipoParcelaModel $tipoParcelaModel;
    private OrientacionModel $orientacionModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->model = $this->loadModel('OperacionModel');
        $this->parcelaModel = $this->loadModel('ParcelaModel');
        $this->deudoModel = $this->loadModel('DeudoModel');
        $this->difuntoModel = $this->loadModel('DifuntoModel');
        $this->tipoOperacionModel = $this->loadModel('TipoOperacionModel');
        $this->sexoModel          = $this->loadModel("SexoModel");
        $this->nacionalidadesModel= $this->loadModel("NacionalidadesModel");
        $this->estadoCivilModel   = $this->loadModel("EstadoCivilModel");
        $this->tipoParcelaModel   = $this->loadModel("TipoParcelaModel");
        $this->orientacionModel   = $this->loadModel("OrientacionModel");
    }

    public function index($errores = [], $values = [])
    {
        $datos = [
            'title' => 'Registrar operacion',
            'action' => URL . 'operacion/save',
            'tipo_operaciones' => $this->tipoOperacionModel->getAllTipoOperaciones(),
            'parcelas' => $this->parcelaModel->getAllParcelas(),
            'deudos' => $this->deudoModel->getAllDeudos(),
            'difuntos' => $this->difuntoModel->getAllDifuntos(),
            'sexos'            => $this->sexoModel->getAllSexos(),
            'nacionalidades'   => $this->nacionalidadesModel->getAllNacionalidades(),
            'estados_civiles'  => $this->estadoCivilModel->getAllEstadosCiviles(),
            'tipos_parcelas'   => $this->tipoParcelaModel->getAllTiposParcelas(),
            'orientaciones'    => $this->orientacionModel->getAllOrientaciones(),
            'values' => $values,
            'errores' => $errores
        ];

        $this->loadView('operacion/OperacionForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->index();
        }

        $tipo_operacion_id = $_POST['tipo_operacion_id'] ?? 0;

        switch ($tipo_operacion_id) {
            case 1:
                return $this->procesarTrasladoInterno($_POST);
            case 2:
                return $this->procesarTrasladoExterno($_POST);
            case 3:
                return $this->procesarIngresoBajosRecursos($_POST);
            case 4:
                return $this->procesarLibreDeuda($_POST);
            default:
                return $this->index(['Tenes que seleccionar un tipo de operacion.'], $_POST);
        }
    }

    private function procesarTrasladoInterno($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto_ti'] ?? null;
        $id_parcela_nueva = $data['id_parcela_destino'] ?? null;
        $fecha_traslado = $data['fecha_traslado'] ?? null;
        $id_deudo = $data['id_deudo'] ?? null;
        $importe = $data['importe'] ?? null;
        $fecha_vencimiento = $data['fecha_vencimiento'] ?? null;

        if (!$id_difunto) $errores[] = "Debe seleccionar un difunto a trasladar.";
        if (!$id_parcela_nueva) $errores[] = "Debe seleccionar una parcela de destino.";
        if (!$id_deudo) $errores[] = "Debe seleccionar un deudo responsable del pago.";
        if (!is_numeric($importe)) $errores[] = "El importe del pago es obligatorio y debe ser un número.";
        if (!$fecha_vencimiento) $errores[] = "Debe especificar una fecha de vencimiento para el pago.";

        // ... (otras validaciones de parcela ocupada, etc.) ...
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        try {
            $ubicacion_actual = $this->model->obtenerUbicacionActual($id_difunto);
            $this->model->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_traslado);
            $this->model->crearNuevaUbicacion($id_difunto, $id_parcela_nueva, $fecha_traslado);
            
            $total = floatval($importe) + (floatval($importe) * (floatval($data['recargo'] ?? 0) / 100));
            $this->model->crearNuevoPago(
                $id_deudo, $id_parcela_nueva, 1,
                $fecha_traslado, $fecha_vencimiento, $importe, 
                $data['recargo'] ?? 0, $total, $_SESSION['usuario_id']
            );
            
            header('Location: ' . URL . 'operacion?exito=1'); 
            exit;
        } catch (Exception $e) {
            return $this->index(['Error al procesar la operación: ' . $e->getMessage()], $data);
        }
    }

    private function procesarTrasladoExterno($data)
    {
        $id_difunto = $data['id_difunto'] ?? null;
        $fecha_operacion = $data['fecha_traslado'] ?? date('Y-m-d');
        $errores = [];

        if (!$id_difunto) $errores[] = "Para un traslado externo, debe seleccionar un difunto.";
        
        $ubicacion_actual = $id_difunto ? $this->model->obtenerUbicacionActual($id_difunto) : null;
        if (!$ubicacion_actual) $errores[] = "El difunto seleccionado no tiene una ubicación activa para exhumar.";
        
        if (!empty($errores)) return $this->index($errores, $data);

        $this->model->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_operacion);

        header('Location: ' . URL . 'operacion?exito=2'); 
        exit;
    }

    private function procesarIngresoBajosRecursos($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto_br'] ?? null;
        $id_parcela = $data['id_parcela_br'] ?? null;
        $id_deudo = $data['id_deudo'] ?? null;
        $fecha_vencimiento = $data['fecha_vencimiento'] ?? null;

        if (!$id_difunto || !$id_parcela || !$id_deudo) $errores[] = "Debe seleccionar difunto, deudo y parcela.";
        if (empty($fecha_vencimiento)) $errores[] = "Debe especificar una fecha de vencimiento.";
        if ($this->model->verificarParcelaOcupada($id_parcela)) $errores[] = "La parcela ya está ocupada.";
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        try {
            $this->model->crearNuevaUbicacion($id_difunto, $id_parcela, date('Y-m-d'));

            $this->model->crearNuevoPago(
                $id_deudo, $id_parcela, 3,
                date('Y-m-d'), $fecha_vencimiento, 0, 0, 0, $_SESSION['usuario_id']
            );

            header('Location: ' . URL . 'operacion?exito=3');
            exit;
        } catch (Exception $e) {
            return $this->index(['Error al procesar el ingreso: ' . $e->getMessage()], $data);
        }
    }

    private function procesarLibreDeuda($data)
    {
        $id_parcela = $data['id_parcela'] ?? null;
        $errores = [];

        if (!$id_parcela) $errores[] = "Debe seleccionar una parcela para verificar su estado de deuda.";
        
        if (!empty($errores)) return $this->index($errores, $data);
        
        $deuda = $this->model->obtenerUltimoPagoVencido($id_parcela);

        if ($deuda) {
            $vencimiento = date('d/m/Y', strtotime($deuda['fecha_vencimiento']));
            return $this->index(["La parcela presenta una deuda pendiente con vencimiento el $vencimiento."], $data);
        }

        echo "Generando PDF de Libre Deuda...";
        exit;
    }
}
?>