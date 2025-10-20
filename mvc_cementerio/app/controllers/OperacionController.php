<?php
require_once __DIR__ . '/../helpers/PdfHelper.php';
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        if (isset($_POST['tipo_operacion'])) {
            $tipo_operacion_id = $_POST['tipo_operacion'];
        } else {
            $tipo_operacion_id = 0;
        }

        switch ($tipo_operacion_id) {
            case 1:
                return $this->procesarTrasladoInterno($_POST);
            case 2:
                return $this->procesarTrasladoExterno($_POST);
            case 3:
                return $this->procesarIngresoBajosRecursos($_POST);
            case 4:
                return $this->procesarLibreDeuda($_POST);
            case 5:
                return $this->procesarIngreso($_POST);
            default:
                return $this->index(['Tenes que seleccionar un tipo de operacion.'], $_POST);
        }
    }

    private function procesarTrasladoInterno($data)
    {
        $errores = [];
        $id_difunto       = $data['id_difunto_ti'];
        $id_parcela_nueva = $data['id_parcela_ti'];
        $id_deudo         = $data['id_deudo_ti'];
        if (isset($data['fecha_traslado_ti'])) { $fecha_operacion = $data['fecha_traslado_ti']; } else { $fecha_operacion = date('Y-m-d'); }
        $importe          = $data['importe_ti'];
        $vencimiento      = $data['fecha_vencimiento_ti'];

        if (!$id_difunto || !$id_parcela_nueva || !$id_deudo || !is_numeric($importe) || !$vencimiento) {
            $errores[] = "Para un Traslado Interno, todos los campos son obligatorios.";
        } else {
            $ubicacion_actual = $this->model->obtenerUbicacionActual($id_difunto);
            if (!$ubicacion_actual) $errores[] = "El difunto no tiene una ubicación activa para trasladar.";
            if ($this->model->verificarParcelaOcupada($id_parcela_nueva)) $errores[] = "La parcela de destino ya está ocupada.";
        }
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        $ubicacion_actual = $this->model->obtenerUbicacionActual($id_difunto);
        $this->model->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_operacion);
        $this->model->crearNuevaUbicacion($id_difunto, $id_parcela_nueva, $fecha_operacion);
        
        $recargo = 0;
        if (isset($data['recargo_ti'])) {
            $recargo = $data['recargo_ti'];
        }

        $total = floatval($importe) + (floatval($importe) * (floatval($recargo) / 100));

        $nuevo_ingreso = $this->model->crearNuevoPago($id_deudo, $id_parcela_nueva, 1, $fecha_operacion, $vencimiento, $importe, $recargo, $total, $_SESSION['usuario_id']);
        
        if ($nuevo_ingreso) {
            $datos_pdf = $this->model->getDatosParaPdfTraslado($id_difunto, $nuevo_ingreso);

            $datos_pdf['fecha_fallecimiento'] = date('d/m/Y', strtotime($datos_pdf['fecha_fallecimiento']));
            $datos_pdf['fecha_pago'] = date('d/m/Y', strtotime($datos_pdf['fecha_pago']));

            $templatePath = __DIR__ . '/../../../docs/AUTORIZACIONTRASLADOINTERNO.html';
            
            PdfHelper::generarPlantilla($templatePath, $datos_pdf, "Traslado-{$id_difunto}.pdf");
        } else {
            return $this->index(['Error fatal al crear el registro de pago.'], $data);
        }

        header('Location: ' . URL . 'operacion?exito=1'); 
        exit;
    }

    private function procesarTrasladoExterno($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto_te'];
        if ($data['fecha_exhumacion_te']) { $fecha_operacion = $data['fecha_exhumacion_te']; } else { $fecha_operacion = date('Y-m-d'); }

        if (!$id_difunto) $errores[] = "Para un traslado externo, debe seleccionar un difunto.";
        
        $ubicacion_actual = $id_difunto ? $this->model->obtenerUbicacionActual($id_difunto) : null;
        if (!$ubicacion_actual) $errores[] = "El difunto seleccionado no tiene una ubicación activa para exhumar.";
        
        if (!empty($errores)) return $this->index($errores, $data);

        $this->model->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_operacion);

        $datos_pdf = $this->model->getDatosParaPdfTrasladoExterno($id_difunto, $ubicacion_actual['id_parcela']);
        
        if ($datos_pdf) {
            $datos_pdf['fecha_fallecimiento'] = date('d/m/Y', strtotime($datos_pdf['fecha_fallecimiento']));
            $datos_pdf['fecha_operacion'] = date('d/m/Y', strtotime($fecha_operacion));
        
            $templatePath = __DIR__ . '/../../../docs/AUTORIZACIONTRASLADOEXTERNO.html';
            PdfHelper::generarPlantilla($templatePath, $datos_pdf, "TrasladoExterno-{$id_difunto}.pdf");
            exit;
        } else {
            return $this->index(['Error fatal al obtener los datos para el certificado.'], $data);
        }
    }

    private function procesarIngresoBajosRecursos($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto_br'];
        $id_parcela = $data['id_parcela_br'];
        $id_deudo   = $data['id_deudo_br'];
        $vencimiento = $data['fecha_vencimiento_br'];
        $fecha_operacion = date('Y-m-d');

        if (!$id_difunto || !$id_parcela || !$id_deudo) $errores[] = "Debe seleccionar difunto, deudo y parcela.";
        if (empty($vencimiento)) $errores[] = "Debe especificar una fecha de vencimiento.";
        if ($this->model->verificarParcelaOcupada($id_parcela)) $errores[] = "La parcela ya está ocupada.";
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        try {
            $this->model->crearNuevaUbicacion($id_difunto, $id_parcela, date('Y-m-d'));
            $this->model->crearNuevoPago(
                $id_deudo, $id_parcela, 3,
                date('Y-m-d'), $vencimiento, 0, 0, 0, $_SESSION['usuario_id']
            );

            $datos_pdf = $this->model->getDatosParaPdfIngresoBR($id_difunto, $id_deudo, $id_parcela);

            if ($datos_pdf) {
                $datos_pdf['fecha_operacion'] = date('d/m/Y', strtotime($fecha_operacion));
                $datos_pdf['fecha_vencimiento'] = date('d/m/Y', strtotime($vencimiento));
    
                $templatePath = __DIR__ . '/../../../docs/AUTORIZACIONPERSONASBAJOSRECURSOS.html';
                PdfHelper::generarPlantilla($templatePath, $datos_pdf, "IngresoBR-{$id_difunto}.pdf");
                exit;
            } else {
                return $this->index(['Error al obtener los datos para el comprobante.'], $data);
            }

        } catch (Exception $e) {
            return $this->index(['Error al procesar el ingreso: ' . $e->getMessage()], $data);
        }
    }

    private function procesarLibreDeuda($data)
    {
        $errores = [];
        $id_parcela = $data['id_parcela_ld'];   
        $id_deudo = $data['id_deudo_ld'];   

        if (!$id_parcela || !$id_deudo) $errores[] = "Debe seleccionar un deudo y parcela para verificar su estado de deuda.";
        
        if (!empty($errores)) return $this->index($errores, $data);
        
        $deuda = $this->model->obtenerDeudaPorDeudoYParcela($id_deudo, $id_parcela);

        if ($deuda) {
            $vencimiento = date('d/m/Y', strtotime($deuda['fecha_vencimiento']));
            return $this->index(["El deudo presenta una deuda en esta parcela con vencimiento el $vencimiento."], $data);
        }

        $datos_pdf = $this->model->getDatosParaPdfLibreDeuda($id_parcela, $id_deudo);

        if (!$datos_pdf) {
            return $this->index(["No se encontraron datos para la parcela seleccionada."], $data);
        }

        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
        
        $datos_pdf['fecha_vencimiento'] = $datos_pdf['fecha_vencimiento'] ? date('d \d\e F \d\e Y', strtotime($datos_pdf['fecha_vencimiento'])) : 'No registra pagos';
        $datos_pdf['fecha_pago'] = date('d \d\e F \d\e Y');

        $templatePath = __DIR__ . '/../../../docs/LIBREDEUDA.html';
        PdfHelper::generarPlantilla($templatePath, $datos_pdf, "LibreDeuda-{$id_parcela}.pdf");
        exit;
    }

    private function procesarIngreso($data)
    {
        $errores = [];
        $id_difunto       = $data['id_difunto_in'] ?? null;
        $id_parcela       = $data['id_parcela_in'] ?? null;
        $id_deudo         = $data['id_deudo_in'] ?? null;
        $fecha_operacion  = $data['fecha_ingreso_in'] ?? date('Y-m-d');
        $importe          = $data['importe_in'] ?? null;
        $vencimiento      = $data['fecha_vencimiento_in'] ?? null;

        if (!$id_difunto || !$id_parcela || !$id_deudo || !is_numeric($importe) || !$vencimiento) {
            $errores[] = "Para un Ingreso, todos los campos son obligatorios.";
        } else {
            if ($this->model->verificarParcelaOcupada($id_parcela)) {
                $errores[] = "La parcela seleccionada ya está ocupada.";
            }
        }
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        $this->model->crearNuevaUbicacion($id_difunto, $id_parcela, $fecha_operacion);
        
        $total = floatval($importe) + (floatval($importe) * (floatval($data['recargo_in'] ?? 0) / 100));
        $nuevo_pago = $this->model->crearNuevoPago($id_deudo, $id_parcela, 5, $fecha_operacion, $vencimiento, $importe, $data['recargo_in'] ?? 0, $total, $_SESSION['usuario_id']);
        
        if ($nuevo_pago) {
            $datos_pdf = $this->model->getDatosParaPdfIngresoDifunto($id_difunto, $nuevo_pago);

            $datos_pdf['fecha_fallecimiento'] = date('d/m/Y', strtotime($datos_pdf['fecha_fallecimiento']));
            $datos_pdf['fecha_pago'] = date('d/m/Y', strtotime($datos_pdf['fecha_pago']));

            $templatePath = __DIR__ . '/../../../docs/AUTORIZACIONINGRESO.html';
            
            PdfHelper::generarPlantilla($templatePath, $datos_pdf, "Comprobante-Ingreso-{$id_difunto}.pdf");
            exit;

        } else {
            return $this->index(['Error fatal al crear el registro de pago.'], $data);
        }
    }
}
?>