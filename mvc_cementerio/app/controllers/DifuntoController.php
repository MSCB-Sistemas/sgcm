<?php
class DifuntoController extends Control
{
    private DifuntoModel $model;
    private DeudoModel $deudoModel;
    private NacionalidadesModel $nacionalidadesModel;
    private SexoModel $sexoModel;
    private EstadoCivilModel $estadoCivilModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("DifuntoModel");
        $this->deudoModel = $this->loadModel("DeudoModel");
        $this->nacionalidadesModel = $this->loadModel("NacionalidadesModel");
        $this->sexoModel = $this->loadModel("SexoModel");
        $this->estadoCivilModel = $this->loadModel("EstadoCivilModel");
    }

    public function index()
    {
        $puedeCrear    = $this->can('crear_difunto');
        $puedeEditar   = $this->can('editar_difunto');
        $puedeEliminar = $this->can('eliminar_difunto');

        $datos = [
            'title'             => 'Lista de difuntos',
            'urlCrear'          => URL . 'difunto/create',
            'ajaxUrl'           => URL . 'difunto/ajax',
            'baseUrl'           => URL . 'difunto',
            'columnas'          => ['ID', 'Deudo', 'Nombre', 'Apellido', 'DNI', 'Edad', 'Fecha defuncion', 'Genero', 'Nacionalidad', 'Estado civil', 'Domicilio', 'Localidad', 'Codigo postal'],
            'columnsConfig'     => [
                ['data' => 'id_difunto'],
                ['data' => 'nombre_deudo'],
                ['data' => 'nombre'],
                ['data' => 'apellido'],
                ['data' => 'dni'],
                ['data' => 'edad'],
                ['data' => 'fecha_fallecimiento'],
                ['data' => 'sexo'],
                ['data' => 'nacionalidad'],
                ['data' => 'estado_civil'],
                ['data' => 'domicilio'],
                ['data' => 'localidad'],
                ['data' => 'codigo_postal'],
                ['data' => 'acciones', 'orderable' => false, 'searchable' => false]
            ],
            'puedeCrear'      => $puedeCrear,
            'errores'         => [],
            'csrfToken'       => $this->generateCsrfToken()
        ];

        $this->loadView('partials/tablaAbmAjax', $datos);
    }

    public function create()
    {
        $deudos = $this->deudoModel->getAllDeudos();
        $nacionalidades = $this->nacionalidadesModel->getAllNacionalidades();
        $sexos = $this->sexoModel->getAllSexos();
        $estadosCiviles = $this->estadoCivilModel->getAllestadosCiviles();

        $datos = [
            'title' => 'Crear difunto',
            'action' => URL . 'difunto/save',
            'values' => [],
            'errores' => [],
            'deudos' => $deudos,
            'nacionalidades' => $nacionalidades,
            'sexos' => $sexos,
            'estados_civiles' => $estadosCiviles
        ];

        $this->loadView('difuntos/DifuntoForm', $datos);
    }

    public function save()
    {
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST["deudo"])) {
                $deudo = $_POST["deudo"];
            } else {
                $deudo = '';
            }
            if (isset($_POST["nombre"])) {
                $nombre = trim($_POST["nombre"]);
            } else {
                $nombre = '';
            }
            if (isset($_POST["apellido"])) {
                $apellido = trim($_POST["apellido"]);
            } else {
                $apellido = '';
            }
            if (isset($_POST["dni"])) {
                $dni = trim($_POST["dni"]);
            } else {
                $dni = '';
            }
            if (isset($_POST["edad"])) {
                $edad = trim($_POST["edad"]);
            } else {
                $edad = '';
            }
            if (isset($_POST["fecha_fallecimiento"])) {
                $fechaFallecimiento = trim($_POST["fecha_fallecimiento"]);
            } else {
                $fechaFallecimiento = '';
            }
            if (isset($_POST["sexo"])) {
                $sexo = $_POST["sexo"];
            } else {
                $sexo = '';
            }
            if (isset($_POST["nacionalidad"])) {
                $nacionalidad = $_POST["nacionalidad"];
            } else {
                $nacionalidad = '';
            }
            if (isset($_POST["estado_civil"])) {
                $estadoCivil = $_POST["estado_civil"];
            } else {
                $estadoCivil = '';
            }
            if (isset($_POST["domicilio"])) {
                $domicilio = trim($_POST["domicilio"]);
            } else {
                $domicilio = '';
            }
            if (isset($_POST["localidad"])) {
                $localidad = trim($_POST["localidad"]);
            } else {
                $localidad = '';
            }
            if (isset($_POST["codigo_postal"])) {
                $codigoPostal = trim($_POST["codigo_postal"]);
            } else {
                $codigoPostal = '';
            }
            $errores = [];


            if (empty($deudo))
                $errores[] = "El deudo es obligatorio";
            if (empty($dni))
                $errores[] = "El dni es obligatorio";
            if (empty($fechaFallecimiento))
                $errores[] = "La fecha de fallecimiento es obligatoria";
            if (empty($domicilio))
                $errores[] = "El domicilio es obligatorio";
            if (empty($localidad))
                $errores[] = "La localidad es obligatorio";

            if (!empty($errores)) {
                $deudos = $this->deudoModel->getAllDeudos();
                $nacionalidades = $this->nacionalidadesModel->getAllNacionalidades();
                $sexos = $this->sexoModel->getAllSexos();
                $estadosCiviles = $this->estadoCivilModel->getAllestadosCiviles();

                $this->loadView('difuntos/DifuntoForm', [
                    'title' => 'Crear difunto',
                    'action' => URL . 'difunto/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'deudos' => $deudos,
                    'nacionalidades' => $nacionalidades,
                    'sexos' => $sexos,
                    'estados_civiles' => $estadosCiviles,
                ]);
                return;
            }

            if ($this->model->insertDifunto($deudo, $nombre, $apellido, $dni, $edad, $fechaFallecimiento, $sexo, $nacionalidad, $estadoCivil, $domicilio, $localidad, $codigoPostal)) {
                header("Location: " . URL . "difunto");
                exit;
            } else {
                die("Error al guardar el difunto");
            }
        }
    }

    public function edit($id)
    {
        $difunto = $this->model->getDifunto($id);
        $deudos = $this->deudoModel->getAllDeudos();
        $nacionalidades = $this->nacionalidadesModel->getAllNacionalidades();
        $sexos = $this->sexoModel->getAllSexos();
        $estadosCiviles = $this->estadoCivilModel->getAllestadosCiviles();

        if (!$difunto) {
            die("Difunto no encontrado");
        }

        $this->loadView('difuntos/DifuntoForm', [
            'title' => 'Editar difunto',
            'action' => URL . 'difunto/update/' . $id,
            'values' => [
                'deudo' => $difunto['id_deudo'],
                'nombre' => $difunto['nombre'],
                'apellido' => $difunto['apellido'],
                'dni' => $difunto['dni'],
                'edad' => $difunto['edad'],
                'fecha_fallecimiento' => $difunto['fecha_fallecimiento'],
                'sexo' => $difunto['id_sexo'],
                'nacionalidad' => $difunto['id_nacionalidad'],
                'estado_civil' => $difunto['id_estado_civil'],
                'domicilio' => $difunto['domicilio'],
                'localidad' => $difunto['localidad'],
                'codigo_postal' => $difunto['codigo_postal'],
            ],
            'errores' => [],
            'deudos' => $deudos,
            'nacionalidades' => $nacionalidades,
            'sexos' => $sexos,
            'estados_civiles' => $estadosCiviles,
        ]);
    }

    public function update($id) {    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST["deudo"])) {
                $deudo = $_POST["deudo"];
            } else {
                $deudo = '';
            }
            if (isset($_POST["nombre"])) {
                $nombre = trim($_POST["nombre"]);
            } else {
                $nombre = '';
            }
            if (isset($_POST["apellido"])) {
                $apellido = trim($_POST["apellido"]);
            } else {
                $apellido = '';
            }
            if (isset($_POST["dni"])) {
                $dni = trim($_POST["dni"]);
            } else {
                $dni = '';
            }
            if (isset($_POST["edad"])) {
                $edad = trim($_POST["edad"]);
            } else {
                $edad = '';
            }
            if (isset($_POST["fecha_fallecimiento"])) {
                $fechaFallecimiento = trim($_POST["fecha_fallecimiento"]);
            } else {
                $fechaFallecimiento = '';
            }
            if (isset($_POST["sexo"])) {
                $sexo = $_POST["sexo"];
            } else {
                $sexo = '';
            }
            if (isset($_POST["nacionalidad"])) {
                $nacionalidad = $_POST["nacionalidad"];
            } else {
                $nacionalidad = '';
            }
            if (isset($_POST["estado_civil"])) {
                $estadoCivil = $_POST["estado_civil"];
            } else {
                $estadoCivil = '';
            }
            if (isset($_POST["domicilio"])) {
                $domicilio = trim($_POST["domicilio"]);
            } else {
                $domicilio = '';
            }
            if (isset($_POST["localidad"])) {
                $localidad = trim($_POST["localidad"]);
            } else {
                $localidad = '';
            }
            if (isset($_POST["codigo_postal"])) {
                $codigoPostal = trim($_POST["codigo_postal"]);
            } else {
                $codigoPostal = '';
            }


            if (empty($deudo))
                $errores[] = "El deudo es obligatorio";
            if (empty($dni))
                $errores[] = "El dni es obligatorio";
            if (empty($fechaFallecimiento))
                $errores[] = "La fecha de fallecimiento es obligatoria";
            if (empty($domicilio))
                $errores[] = "El domicilio es obligatorio";
            if (empty($localidad))
                $errores[] = "La localidad es obligatorio";

            if (!empty($errores)) {
                $difunto = [
                    'id_difunto' => $id,
                    'id_deudo' => $deudo,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'dni' => $dni,
                    'edad' => $edad,
                    'fecha_fallecimiento' => $fechaFallecimiento,
                    'id_sexo' => $sexo,
                    'id_nacionalidad' => $nacionalidad,
                    'id_estado_civil' => $estadoCivil,
                    'domicilio' => $domicilio,
                    'localidad' => $localidad,
                    'codigo_postal' => $codigoPostal
                ];

                $deudos = $this->deudoModel->getAllDeudos();
                $nacionalidades = $this->nacionalidadesModel->getAllNacionalidades();
                $sexos = $this->sexoModel->getAllSexos();
                $estadosCiviles = $this->estadoCivilModel->getAllestadosCiviles();

                $this->loadView('difuntos/DifuntoForm', [
                    'title' => 'Editar difunto',
                    'action' => URL . 'difunto/update/' . $id,
                    'values' => $difunto,
                    'errores' => $errores,
                    'deudos' => $deudos,
                    'nacionalidades' => $nacionalidades,
                    'sexos' => $sexos,
                    'estados_civiles' => $estadosCiviles,
                ]);
                return;
            }

            if ($this->model->updateDifunto($id, $deudo, $nombre, $apellido, $dni, $edad, $fechaFallecimiento, $sexo, $nacionalidad, $estadoCivil, $domicilio, $localidad, $codigoPostal)) {
                header("Location: " . URL . "difunto");
                exit;
            } else {
                die("Error al actualizar el difunto");
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->deleteDifunto($id)) {
            header("Location: " . URL . "difunto");
            exit;
        } else {
            die("No se pudo eliminar al difunto");
        }
    }

    public function ajax()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        // Función auxiliar para obtener valores POST de forma segura
        function getPost($key, $default = '') {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            } else {
                return $default;
            }
        }


        // Función auxiliar para obtener valores anidados en arrays (como search[value])
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

        // Obtener parámetros
        $draw   = getPost('draw', 1);
        $start  = intval(getPost('start', 0));
        $length = intval(getPost('length', 10));
        $search = getNestedPost(['search', 'value'], '');
        $orderColumnIndex = getNestedPost(['order', 0, 'column'], 0);
        $orderDir         = getNestedPost(['order', 0, 'dir'], 'asc');

        // Definir columnas permitidas para ordenamiento
        $columns = [
            'id_difunto', 'nombre_deudo', 'nombre', 'apellido', 'dni', 'edad', 
            'fecha_fallecimiento', 'sexo', 'nacionalidad', 'estado_civil', 
            'domicilio', 'localidad', 'codigo_postal'
        ];

        // Validar si el índice de columna existe
        if (isset($columns[$orderColumnIndex])) {
            $orderCol = $columns[$orderColumnIndex];
        } else {
            $orderCol = 'id_difunto';
        }

        // Obtener el total de registros desde el modelo
        $totalRecords = $this->model->countAll();


        if ($search) {
            $data = $this->model->getFiltered($search, $orderCol, $orderDir, $start, $length);
            $filteredRecords = $this->model->countFiltered($search);
        } else {
            $data = $this->model->getPage($orderCol, $orderDir, $start, $length);
            $filteredRecords = $totalRecords;
        }

        foreach ($data as &$row) {
            if (!empty($row['fecha_fallecimiento'])) {
                $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_fallecimiento']);
                if ($fecha) {
                    $row['fecha_fallecimiento'] = $fecha->format('Y-m-d');
                } else {
                    $fecha = DateTime::createFromFormat('Y-m-d', $row['fecha_fallecimiento']);
                    if ($fecha) {
                        $row['fecha_fallecimiento'] = $fecha->format('Y-m-d');
                    }
                }
            }
        }

        foreach ($data as &$fila) {
            $id  = $fila['id_difunto'];
            $url = rtrim(URL,'/') . '/difunto';
    
            $acciones = '';
    
            if ($this->can('editar_difunto')) {
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> '
                           . '</form> ';
            }
    
            if ($this->can('eliminar_difunto')) {
                $acciones .= '<form action="'.$url.'/delete/'.$id.'" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este difunto?\');">'
                           . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                           . '</form>';
            }
    
            $fila['acciones'] = $acciones;
        }  

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
        exit;
    }

    private function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

}
?>