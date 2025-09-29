<?php
class EstadisticasController extends Control {
    private EstadisticasModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("EstadisticasModel");
    }

    public function index(){
       
        // Filtros de Defunciones
        if (isset($_GET['fecha_inicio_defuncion'])) {
            $fecha_inicio_defuncion = $_GET['fecha_inicio_defuncion'];
        } else {
            $fecha_inicio_defuncion = '1900-01-01';
        }
        if (isset($_GET['fecha_fin_defuncion'])) {
            $fecha_fin_defuncion = $_GET['fecha_fin_defuncion'];
        } else {
            $fecha_fin_defuncion = date('Y-m-d');
        }
        if (isset($_GET['letra_apellido_difunto'])) {
            $letra_apellido_difunto = $_GET['letra_apellido_difunto'];
        } else {
            $letra_apellido_difunto = '';
        }

        // Filtros de Traslados
        if (isset($_GET['fecha_inicio_defuncion_traslado'])) {
            $fecha_inicio_defuncion_traslado = $_GET['fecha_inicio_defuncion_traslado'];
        } else {
            $fecha_inicio_defuncion_traslado = '1900-01-01';
        }
        if (isset($_GET['fecha_fin_defuncion_traslado'])) {
            $fecha_fin_defuncion_traslado = $_GET['fecha_fin_defuncion_traslado'];
        } else {
            $fecha_fin_defuncion_traslado = date('Y-m-d');
        }
        if (isset($_GET['fecha_inicio_traslado'])) {
            $fecha_inicio_traslado = $_GET['fecha_inicio_traslado'];
        } else {
            $fecha_inicio_traslado = '1900-01-01';
        }
        if (isset($_GET['fecha_fin_traslado'])) {
            $fecha_fin_traslado = $_GET['fecha_fin_traslado'];
        } else {
            $fecha_fin_traslado = date('Y-m-d');
        }
        if (isset($_GET['letra_apellido_traslado'])) {
            $letra_apellido_traslado = $_GET['letra_apellido_traslado'];
        } else {
            $letra_apellido_traslado = '';
        }

        // Filtros de Parcelas Vendidas
        if (isset($_GET['fecha_inicio_parcela'])) {
            $fecha_inicio_parcela = $_GET['fecha_inicio_parcela'];
        } elseif (isset($_GET['fecha_parcela_inicio'])) {
            $fecha_inicio_parcela = $_GET['fecha_parcela_inicio'];
        } else {
            $fecha_inicio_parcela = '1900-01-01';
        }
        if (isset($_GET['fecha_fin_parcela'])) {
            $fecha_fin_parcela = $_GET['fecha_fin_parcela'];
        } elseif (isset($_GET['fecha_parcela_fin'])) {
            $fecha_fin_parcela = $_GET['fecha_parcela_fin'];
        } else {
            $fecha_fin_parcela = date('Y-m-d');
        }
        if (isset($_GET['letra_apellido_deudo'])) {
            $letra_apellido_deudo = $_GET['letra_apellido_deudo'];
        } else {
            $letra_apellido_deudo = '';
        }

        // Filtros para defunciones mensuales
        if (isset($_GET['fecha_inicio_mensual'])) {
            $fecha_inicio_mensual = $_GET['fecha_inicio_mensual'];
        } else {
            $fecha_inicio_mensual = '1900-01-01';
        }
        if (isset($_GET['fecha_fin_mensual'])) {
            $fecha_fin_mensual = $_GET['fecha_fin_mensual'];
        } else {
            $fecha_fin_mensual = date('Y-m-d');
        }

        if (isset($_GET['sort_col'])) {
            $sort_col = $_GET['sort_col'];
        } else {
            $sort_col = 'fecha_fallecimiento';
        }
        if (isset($_GET['sort_dir'])) {
            $sort_dir = strtoupper($_GET['sort_dir']);
        } else {
            $sort_dir = 'ASC';
        }
        if (!in_array($sort_dir, ['ASC', 'DESC'])) {
            $sort_dir = 'ASC';
        }

        $validarFecha = function($d) {
            if (empty($d)) return false;
            $dt = DateTime::createFromFormat('Y-m-d', $d);
            return $dt && $dt->format('Y-m-d') === $d;
        };

        if (!$validarFecha($fecha_inicio_defuncion_traslado)) $fecha_inicio_defuncion_traslado = '1900-01-01';
        if (!$validarFecha($fecha_fin_defuncion_traslado))   $fecha_fin_defuncion_traslado = date('Y-m-d');
        if (!$validarFecha($fecha_inicio_traslado)) $fecha_inicio_traslado = '1900-01-01';
        if (!$validarFecha($fecha_fin_traslado))   $fecha_fin_traslado = date('Y-m-d');
        if (!$validarFecha($fecha_inicio_mensual)) $fecha_inicio_mensual = '1900-01-01';
        if (!$validarFecha($fecha_fin_mensual))   $fecha_fin_mensual = date('Y-m-d');

       
        $pagina = !empty($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $limite = 14;
        $offset = ($pagina - 1) * $limite;

        
        $defunciones = $this->model->getDefuncionesEntreFechas(
            $fecha_inicio_defuncion,
            $fecha_fin_defuncion,
            $letra_apellido_difunto,
            $sort_col,
            $sort_dir,
            $limite,
            $offset
        );

        $deudores_morosos = $this->model->getDeudosMorosos();
 
        $numero_ubicacion = isset($_GET['numero_ubicacion']) ? $_GET['numero_ubicacion'] : '';
        $id_tipo_parcela = isset($_GET['id_tipo_parcela']) ? $_GET['id_tipo_parcela'] : '';
        $seccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
        $fraccion = isset($_GET['fraccion']) ? $_GET['fraccion'] : '';
        $nivel = isset($_GET['nivel']) ? $_GET['nivel'] : '';
        $id_orientacion = isset($_GET['id_orientacion']) ? $_GET['id_orientacion'] : '';
        $hilera = isset($_GET['hilera']) ? $_GET['hilera'] : '';

        $filtros_parcela = [
            'numero_ubicacion' => $numero_ubicacion,
            'id_tipo_parcela' => $id_tipo_parcela,
            'seccion' => $seccion,
            'fraccion' => $fraccion,
            'nivel' => $nivel,
            'id_orientacion' => $id_orientacion,
            'hilera' => $hilera        
        ];

        $uso_filtro_parcela = array_filter($filtros_parcela, function($v) { return $v !== ''; });

        if ($uso_filtro_parcela) {
            $parcelas_vendidas = $this->model->getParcelasVendidasPorDatosParcela($filtros_parcela);
            $total_parcelas_vendidas = count($parcelas_vendidas);
        } else {
            $parcelas_vendidas = $this->model->getParcelasVendidas($fecha_inicio_parcela, $fecha_fin_parcela, $letra_apellido_deudo);
            $total_parcelas_vendidas = count($parcelas_vendidas);
        }
        
       
        $difuntos_trasladados = $this->model->getDifuntosTrasladados(
            $fecha_inicio_defuncion_traslado,
            $fecha_fin_defuncion_traslado,
            $fecha_inicio_traslado,
            $fecha_fin_traslado,
            $letra_apellido_traslado,
            $sort_col,
            $sort_dir,
            $limite,
            $offset
        );

        $total_difuntos = $this->model->getTotalDifuntos();
        $total_defunciones = $this->model->getTotalDefuncionesEntreFechas(
            $fecha_inicio_defuncion, 
            $fecha_fin_defuncion, 
            $letra_apellido_difunto
        );
        $total_parcelas = $this->model->getTotalParcelasOcupadas();
        $total_traslados = $this->model->getTotalTraslados();
        $total_paginas = max(1, ceil($total_defunciones / $limite));

        $total_defunciones_mensuales = null;
        if (isset($_GET['filtrar_defunciones'])) {
            $total_defunciones_mensuales = $this->model->getTotalDefuncionesMensuales(
                $fecha_inicio_mensual,
                $fecha_fin_mensual
            );
        }

        $datos = [
            'title' => 'Estadisticas',
            'movimientos' => $defunciones,
            'deudores_morosos' => $deudores_morosos,
            'difuntos_trasladados' => $difuntos_trasladados,
            'pagina_actual' => $pagina,            
            'parcelas_vendidas' => $parcelas_vendidas,
            'fecha_inicio_defuncion' => $fecha_inicio_defuncion,
            'fecha_fin_defuncion' => $fecha_fin_defuncion,
            'fecha_inicio_defuncion_traslado' => $fecha_inicio_defuncion_traslado,
            'fecha_fin_defuncion_traslado' => $fecha_fin_defuncion_traslado,
            'fecha_inicio_traslado' => $fecha_inicio_traslado,
            'fecha_fin_traslado' => $fecha_fin_traslado, 
            'fecha_inicio_mensual' => $fecha_inicio_mensual,
            'fecha_fin_mensual' => $fecha_fin_mensual,  
            'total_defunciones_mensuales' => $total_defunciones_mensuales,          
            'total_parcelas_vendidas' => $total_parcelas_vendidas,
            'letra_apellido_difunto' => $letra_apellido_difunto,
            'letra_apellido_deudo' => $letra_apellido_deudo,
            'letra_apellido_traslado' => $letra_apellido_traslado,                       
            'total_difuntos' => $total_difuntos,            
            'total_parcelas' => $total_parcelas,
            'total_traslados' => $total_traslados,
            'total_defunciones' => $total_defunciones,
            'total_paginas' => $total_paginas,
            'total_resultados' => $total_defunciones,
            'total_morosos' => count($deudores_morosos),
            'sort_col' => $sort_col,
            'sort_dir' => $sort_dir,         
            
        ];

        $this->loadView("estadisticas", $datos);
    }

}
?>