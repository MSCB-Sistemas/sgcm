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
        $configPagosPorDifunto = [
            'tabId' => 'pagos_por_difunto',
            'configKey' => 'pagos_por_difunto',
            'ajaxUrl' => URL . '/estadisticas/ajaxPagosPorDifunto',
            'columnHeaders' => ['ID Pago', 'Difunto', 'Parcela', 'Deudo Responsable', 'Monto', 'Fecha Pago'],
        ];

        $configDifuntos = [
            'tabId' => 'difuntos',
            'configKey' => 'difuntos',
            'ajaxUrl' => URL . '/difunto/ajax',
            'columnHeaders' => ['Fecha Fall.', 'Nombre', 'Apellido', 'Edad', 'DNI', 'Deudo', 'Estado Civil', 'Nacionalidad', 'Sexo', 'Domicilio', 'Localidad', 'Cód. Postal'],
        ];

        $configTraslados = [
            'tabId' => 'traslados',
            'configKey' => 'traslados',
            'ajaxUrl' => URL . '/estadisticas/ajaxTraslados', 
            'columnHeaders' => ['Nombre', 'Apellido', 'DNI', 'Fecha Fall.', 'Fecha Traslado', 'Movimiento'],
            'isActive' => true,
        ];

        $configVendidas = [
            'tabId' => 'vendidas',
            'configKey' => 'vendidas',
            'ajaxUrl' => URL . '/estadisticas/ajaxParcelasVendidas',
            'columnHeaders' => ['Parcela', 'Tipo', 'Titular', 'Apellido', 'DNI', 'Monto', 'Fecha Venta', 'Fecha Vencimiento'],
        ];

        $configMorosos = [
            'tabId' => 'morosos',
            'configKey' => 'morosos',
            'ajaxUrl' => URL . '/estadisticas/ajaxDeudosMorosos',
            'columnHeaders' => ['Parcela', 'DNI', 'Nombre', 'Apellido', 'Vencimiento', 'Monto', 'Días de mora', 'Acciones'],
        ];

        $datos = [
            'title' => 'Estadísticas',
            'configPagosPorDifunto' => $configPagosPorDifunto,       
            'configDifuntos' => $configDifuntos,
            'configTraslados' => $configTraslados,
            'configVendidas' => $configVendidas,
            'configMorosos' => $configMorosos,
            'total_morosos' => $this->model->getTotalDeudosMorosos(),
            'total_difuntos' => $this->model->getTotalDifuntos(),
            'total_parcelas' => $this->model->getTotalParcelasOcupadas(),
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

    public function ajaxParcelasVendidas()
    {
        $params = $_POST;
        $datos = $this->model->getParcelasVendidasAjax($params);
        
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    public function ajaxDeudosMorosos()
    {
        $params = $_POST;
        $datos = $this->model->getDeudosMorososAjax($params);
        
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    public function ajaxPagosPorDifunto()
    {
        $params = $_POST;
        $datos = $this->model->getPagosPorDifuntoAjax($params);
        
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }
}
?>