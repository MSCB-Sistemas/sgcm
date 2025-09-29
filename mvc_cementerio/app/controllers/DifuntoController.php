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

        $difuntos = $this->model->getAllDifuntos();

        $datos = [
            'title'             => 'Lista de difuntos',
            'urlCrear'          => URL . 'difunto/create',
            'columnas'          => ['ID', 'Deudo', 'Nombre', 'Apellido', 'DNI', 'Edad', 'Fecha fallecimiento', 'Genero', 'Nacionalidad', 'Estado civil', 'Domicilio', 'Localidad', 'Codigo postal'],
            'columnas_claves'   => ['id_difunto', 'nombre_deudo', 'nombre', 'apellido', 'dni', 'edad', 'fecha_fallecimiento', 'sexo', 'nacionalidad', 'estado_civil', 'domicilio', 'localidad', 'codigo_postal'],
            'data'              => $difuntos,
            'acciones' => function (array $fila) use ($puedeEditar, $puedeEliminar)
            {
                $id = $fila['id_difunto'];
                $url = rtrim(URL,'/') . '/difunto';
                
                $html = '';
                if ($puedeEditar) 
                {
                    $html .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> ';
                    $html .= '<form action="'.$url.'/activate/'.$id.'" method="post" style="display:inline">'
                          .  '<button class="btn btn-sm btn-success" onclick="return confirm(\'¿Activar este usuario?\');">Activar</button>'
                          .  '</form> ';
                }
                if ($puedeEliminar) {
                    $html .= '<form action="'.$url.'/delete/'.$id.'" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este usuario?\');">'
                          .  '<button class="btn btn-sm btn-danger">Eliminar</button>'
                          .  '</form>';
                }
                return $html;
            },
            'puedeCrear'      => $puedeCrear,   // por si tu partial muestra el botón “Nuevo”
            'errores'         => [],
        ];

        $this->loadView('partials/tablaAbm', $datos);
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
}
?>