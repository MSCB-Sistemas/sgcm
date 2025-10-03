<?php
class OperacionController extends Control
{
    private OperacionController $model;

    public function __construct()
    {
        $this->requireLogin();
    }

    public function index($errores = [])
    {
        $traslados = $this->loadModel('OperacionModel')->getAllTraslados();
        $parcelas = $this->loadModel('ParcelaModel')->getAllParcelas();
        $deudos = $this->loadModel('DeudoModel')->getAllDeudos();
        $difuntos = $this->loadModel('DifuntoModel')->getAllDifuntos();

        $datos = [
            'title' => 'Operacion',
            'data' => $traslados,
            'action' => URL . 'operacion/save',
            'parcelas' => $parcelas,
            'deudos' => $deudos,
            'difuntos' => $difuntos,
            'errores' => $errores
        ];

        $this->loadView('operacion/OperacionForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];
            $id_difunto = $_POST['id_difunto'] ?? '';
            $id_parcela = $_POST['id_parcela'] ?? '';
            $id_deudo = $_POST['id_deudo'] ?? '';
            $fecha_traslado = $_POST['fecha_traslado'] ?? '';
            $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';

            if (empty($id_difunto))
                $errores[] = 'Seleccione un difunto';
            if (empty($id_parcela))
                $errores[] = 'Seleccione una parcela';
            if (empty($id_deudo))
                $errores[] = 'Seleccione un deudo';
            if (empty($fecha_vencimiento))
                $errores[] = 'Ingrese la fecha de vencimiento';

            if (empty($errores)) {
                $model = $this->loadModel('TrasladoModel');

                if ($model->verificarParcelaOcupada($id_parcela)) {
                    $errores[] = 'La parcela seleccionada ya está ocupada';
                } else {
                    $ubicacion_actual = $model->obtenerUbicacionActual($id_difunto);

                    if ($ubicacion_actual && isset($ubicacion_actual['id_pago'])) {
                        $model->actualizarVencimientoPago($ubicacion_actual['id_pago'], $fecha_traslado);
                    }

                    $total = 1000; // Monto fijo para el ejemplo, deberiamos poder seleccionarlo desde el form, tarea para galo del futuro.

                    $nuevo_pago_id = $model->crearNuevoPago(
                        $id_deudo,
                        $id_parcela,
                        $fecha_traslado,
                        $fecha_vencimiento,
                        $total
                    );

                    if ($nuevo_pago_id) {
                        if ($model->realizarTraslado($id_difunto, $id_parcela, $fecha_traslado)) {
                            header('Location: ' . URL . '/operacion');
                            exit;
                        }
                    }

                    $errores[] = 'Error al realizar el traslado. Intente nuevamente.';
                }
            }
        }

        $this->index($errores);
    }
}
?>