<?php
class EstadisticasController extends Control {
    private EstadisticasModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("EstadisticasModel");
    }

    public function index()
    {
        $configIntegral = [
            'tabId' => 'reporte_integral',
            'configKey' => 'integral',
            'ajaxUrl' => URL . '/estadisticas/ajaxReporteIntegral',
            'columnHeaders' => ['ID Pago', 'Difunto', 'Parcela (ID)', 'Ubicación Detallada', 'Deudo', 'Archivo'],
        ];

        $configTraslados = [
            'tabId' => 'traslados',
            'configKey' => 'traslados',
            'ajaxUrl' => URL . '/estadisticas/ajaxTraslados', 
            'columnHeaders' => ['Nombre', 'Apellido', 'DNI', 'Fecha Fall.', 'Fecha Traslado', 'Movimiento'],
            'isActive' => true,
        ];

        $configMorosos = [
            'tabId' => 'morosos',
            'configKey' => 'morosos',
            'ajaxUrl' => URL . '/estadisticas/ajaxDeudosMorosos',
            'columnHeaders' => ['Parcela', 'DNI', 'Nombre', 'Apellido', 'Vencimiento', 'Monto', 'Días de mora', 'Acciones'],
        ];

        $datos = [
            'title' => 'Estadísticas y Reportes',
            'configIntegral' => $configIntegral,
            'configMorosos' => $configMorosos, 
            'configTraslados' => $configTraslados,
            'total_morosos' => $this->model->getTotalDeudosMorosos(),
            'total_difuntos' => $this->model->getTotalDifuntos(),
            'total_parcelas' => $this->model->getTotalParcelasOcupadas(),
            'total_parcelas_generales' => $this->model->getTotalParcelas(),
            'deuda_estimada' => $this->model->getDeudaTotalEstimada(),
            'ingresos_mes' => $this->model->getIngresosMesActual(),
            'total_traslados' => $this->model->getTotalTraslados(),
        ];

        $this->loadView("estadisticas", $datos);
    }

    public function ajaxTraslados()
    {
        $params = $_POST;

        $datos = $this->model->getTrasladosAjax($params);

        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    // public function ajaxParcelasVendidas()
    // {
    //     $params = $_POST;
    //     $datos = $this->model->getParcelasVendidasAjax($params);
        
    //     header('Content-Type: application/json');
    //     echo json_encode($datos);
    //     exit;
    // }

    public function ajaxDeudosMorosos()
    {
        $params = $_POST;
        $datos = $this->model->getDeudosMorososAjax($params);
        
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    // public function ajaxPagosPorDifunto()
    // {
    //     $params = $_POST;
    //     $datos = $this->model->getPagosPorDifuntoAjax($params);
        
    //     header('Content-Type: application/json');
    //     echo json_encode($datos);
    //     exit;
    // }

    public function ajaxReporteIntegral() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getReporteIntegralAjax($_POST));
        exit;
    }
}