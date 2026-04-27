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
        $puedeCrear = $this->can('crear_difunto');
        $puedeEditar = $this->can('editar_difunto');
        $puedeEliminar = $this->can('eliminar_difunto');

        $datos = [
            'title' => 'Lista de difuntos',
            'urlCrear' => URL . 'difunto/create',
            'ajaxUrl' => URL . 'difunto/ajax',
            'baseUrl' => URL . 'difunto',
            'columnas' => ['ID', 'Nombre', 'Apellido', 'DNI', 'Edad', 'Fecha defuncion', 'Genero', 'Nacionalidad', 'Estado civil', 'Domicilio', 'Localidad', 'Codigo postal'],
            'columnsConfig' => [
                ['data' => 'id_difunto'],
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
            'puedeCrear' => $puedeCrear,
            'errores' => [],
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'difunto/create');
            exit;
        }

        $id_deudo = !empty($_POST["id_deudo"]) ? $_POST["id_deudo"] : null;
        $nombre = trim($_POST["nombre"]);
        $apellido = trim($_POST["apellido"]);
        $dni = trim($_POST["dni"]);
        $edad = trim($_POST["edad"]);
        $fechaFallecimiento = trim($_POST["fecha_fallecimiento"]);
        $sexo = $_POST["sexo"];
        $nacionalidad = $_POST["nacionalidad"];
        $estadoCivil = $_POST["estado_civil"];
        $domicilio = trim($_POST["domicilio"]);
        $localidad = trim($_POST["localidad"]);
        $codigoPostal = trim($_POST["codigo_postal"]);
        $errores = [];


        if (empty($dni))
            $errores[] = "El dni es obligatorio";
        if (empty($fechaFallecimiento))
            $errores[] = "La fecha de fallecimiento es obligatoria";
        if (empty($domicilio))
            $errores[] = "El domicilio es obligatorio";
        if (empty($localidad))
            $errores[] = "La localidad es obligatorio";

        if (!empty($errores)) {
            $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($es_ajax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errores' => $errores]);
                exit;
            } else {
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
        }

        $nuevo_ingreso = $this->model->insertDifunto($id_deudo, $nombre, $apellido, $dni, $edad, $fechaFallecimiento, $sexo, $nacionalidad, $estadoCivil, $domicilio, $localidad, $codigoPostal);

        if ($nuevo_ingreso) {
            $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($is_ajax) {
                $texto_completo = '';
                if (!empty($dni)) {
                    $texto_completo = "$dni - $nombre $apellido";
                } else {
                    $texto_completo = "$nombre $apellido";
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'newItem' => [
                        'id' => $nuevo_ingreso,
                        'text' => $texto_completo
                    ]
                ]);
                exit;
            } else {
                header("Location: " . URL . "difunto");
                exit;
            }
        } else {
            $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            if ($is_ajax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errores' => ["Error al guardar el difunto en la base de datos."]]);
                exit;
            } else {
                die("Error al guardar el difunto en la base de datos.");
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

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST["id_deudo"])) {
                $id_deudo_val = $_POST["id_deudo"];
            } else {
                $id_deudo_val = null;
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
                    'id_deudo' => $id_deudo_val,
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

            if ($this->model->updateDifunto($id, $id_deudo_val, $nombre, $apellido, $dni, $edad, $fechaFallecimiento, $sexo, $nacionalidad, $estadoCivil, $domicilio, $localidad, $codigoPostal)) {
                header("Location: " . URL . "difunto");
                exit;
            } else {
                $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
                if ($is_ajax) {
                    http_response_code(500);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'errores' => ["Error al actualizar el difunto."]]);
                    exit;
                } else {
                    die("Error al actualizar el difunto");
                }
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

        if ($_POST['draw']) {
            $draw = $_POST['draw'];
        } else {
            $draw = 1;
        }
        if (intval($_POST['start'])) {
            $start = intval($_POST['start']);
        } else {
            $start = 0;
        }
        if (intval($_POST['length'])) {
            $length = intval($_POST['length']);
        } else {
            $length = 10;
        }
        if ($_POST['search']['value']) {
            $search = $_POST['search']['value'];
        } else {
            $search = '';
        }

        $orderColumnIndex = 0;
        $orderDir = 'asc';

        if (isset($_POST['order'][0]['column'])) {
            $orderColumnIndex = $_POST['order'][0]['column'];
        } else {
            $orderColumnIndex = 0;
        }
        if (isset($_POST['order'][0]['dir'])) {
            $orderDir = $_POST['order'][0]['dir'];
        } else {
            $orderDir = 'asc';
        }

        $columns = [
            'id_difunto',
            'nombre',
            'apellido',
            'dni',
            'edad',
            'fecha_fallecimiento',
            'sexo',
            'nacionalidad',
            'estado_civil',
            'domicilio',
            'localidad',
            'codigo_postal'
        ];

        if (isset($columns[$orderColumnIndex])) {
            $orderCol = $columns[$orderColumnIndex];
        } else {
            $orderCol = 'id_difunto';
        }

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
            $id = $fila['id_difunto'];
            $url = rtrim(URL, '/') . '/difunto';

            $acciones = '';

            if ($this->can('editar_difunto')) {
                $acciones .= '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a> '
                    . '</form> ';
            }

            if ($this->can('eliminar_difunto')) {
                $acciones .= '<form action="' . $url . '/delete/' . $id . '" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este difunto?\');">'
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
}
