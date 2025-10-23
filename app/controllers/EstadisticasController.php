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

        $deudores_morosos = $this->model->getDeudosMorosos();

        $datos = [
            'title' => 'Estadísticas',            
            'configDifuntos' => $configDifuntos,
            'configTraslados' => $configTraslados,
            'configVendidas' => $configVendidas,
            'deudores_morosos' => $deudores_morosos,
            'total_morosos' => count($deudores_morosos),
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
}
?>