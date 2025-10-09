<?php
class OperacionController extends Control
{
    private OperacionModel $operacionModel;
    private ParcelaModel $parcelaModel;
    private DeudoModel $deudoModel;
    private DifuntoModel $difuntoModel;
    private TipoOperacionModel $tipoOperacionModel;

    public function __construct()
    {
        $this->requireLogin();

        $this->operacionModel     = $this->loadModel("OperacionModel");
        $this->parcelaModel       = $this->loadModel("ParcelaModel");
        $this->deudoModel         = $this->loadModel("DeudoModel");
        $this->difuntoModel       = $this->loadModel("DifuntoModel");
        $this->tipoOperacionModel = $this->loadModel("TipoOperacionModel");
    }

    public function index($errores = [], $values = [])
    {
        $datos = [
            'title'            => 'Registrar Operación',
            'action'           => URL . 'operacion/save',
            'tipo_operaciones' => $this->tipoOperacionModel->getAllTipoOperaciones(),
            'parcelas'         => $this->parcelaModel->getAllParcelas(),
            'deudos'           => $this->deudoModel->getAllDeudos(),
            'difuntos'         => $this->difuntoModel->getAllDifuntos(),
            'values'           => $values, 
            'errores'          => $errores,
        ];

        $this->loadView('operacion/OperacionForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        $tipo_operacion_id = $_POST['tipo_operacion_id'] ?? 0;

        switch ($tipo_operacion_id) {
            case 1:
                return $this->procesarTrasladoInterno($_POST);
            case 2: 
                return $this->procesarTrasladoExterno($_POST);
            case 3: 
                return $this->procesarBajosRecursos($_POST);
            case 4: 
                return $this->procesarLibreDeuda($_POST);

            default:
                return $this->index(['Tipo de operación no válido.'], $_POST);
        }
    }

    private function procesarTrasladoInterno($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto'] ?? null;
        $id_parcela_nueva = $data['id_parcela_destino'] ?? null;
        $fecha_traslado = $data['fecha_traslado'] ?? null;

        if (!$id_difunto || !$id_parcela_nueva) {
            $errores[] = "Debe seleccionar un difunto y una parcela de destino.";
        } else {
            if ($this->operacionModel->verificarParcelaOcupada($id_parcela_nueva)) {
                $errores[] = "La parcela de destino ya está ocupada.";
            }
        }
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        try {
            $ubicacion_actual = $this->operacionModel->obtenerUbicacionActual($id_difunto);
            if ($ubicacion_actual) {
                $this->operacionModel->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_traslado);
            }
            $this->operacionModel->crearNuevaUbicacion($id_difunto, $id_parcela_nueva, $fecha_traslado);
            
            // Aquí hay que generar un PDF y despues redirigir
            header('Location: ' . URL . 'operacion'); 
            exit;
        } catch (Exception $e) {
            return $this->index(['Error al procesar el traslado.'], $data);
        }
    }

    private function procesarTrasladoExterno($data)
    {
        $errores = [];
        $id_difunto = $data['id_difunto_exhumar'] ?? null;
        $fecha_operacion = $data['fecha_exhumacion'] ?? date('Y-m-d');

        if (!$id_difunto) {
            $errores[] = "Debe seleccionar un difunto para exhumar.";
        } else {
             $ubicacion_actual = $this->operacionModel->obtenerUbicacionActual($id_difunto);
            if (!$ubicacion_actual) {
                $errores[] = "El difunto seleccionado no tiene una ubicación activa para exhumar.";
            }
        }
        
        if (!empty($errores)) {
            return $this->index($errores, $data);
        }

        try {
            $this->operacionModel->actualizarFechaRetiro($ubicacion_actual['id_ubicacion_difunto'], $fecha_operacion);
            
            header('Location: ' . URL . 'operacion?exito=2'); 
            exit;
        } catch (Exception $e) {
            return $this->index(['Error al procesar la exhumación: ' . $e->getMessage()], $data);
        }
    }

    private function procesarLibreDeuda($data)
    {
        $id_deudo = $data['id_deudo_libre'] ?? null;
        $id_parcela = $data['id_parcela_libre'] ?? null;

        if (!$id_deudo || !$id_parcela) {
             return $this->index(['Debe seleccionar un deudo y una parcela.'], $data);
        }

        $deuda_pendiente = $this->operacionModel->verificarDeuda($id_parcela);
        
        if ($deuda_pendiente) {
            return $this->index(['La parcela presenta una deuda pendiente de $' . $deuda_pendiente['total'] . ' con vencimiento el ' . $deuda_pendiente['fecha_vencimiento']], $data);
        }

        // Lógica para generar PDF de Libre de Deuda...
        
    }

    private function procesarBajosRecursos($data) {
        // ...
    }
}
?>