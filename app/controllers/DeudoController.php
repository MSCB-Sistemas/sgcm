<?php
class DeudoController extends Control
{
    private DeudoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("DeudoModel");
    }

    public function index()
    {
        $puedeCrear    = $this->can('crear_deudo');
        $puedeEditar   = $this->can('editar_deudo');
        $puedeEliminar = $this->can('eliminar_deudo');

        $datos = [
            "title"             => "Lista de Deudos",
            'urlCrear'          => URL . 'deudo/create',
            'ajaxUrl'           => URL . 'deudo/ajax',
            'baseUrl'           => URL . 'deudo/',
            'columnas'  => ['ID', 'DNI', 'Nombre', 'Apellido', 'Teléfono', 'Email', 'Domicilio', 'Localidad', 'Código Postal'],
            'columnsConfig'     => [
                ['data' => 'id_deudo'],
                ['data' => 'dni'],
                ['data' => 'nombre'],
                ['data' => 'apellido'],
                ['data' => 'telefono'],
                ['data' => 'email'],
                ['data' => 'domicilio'],
                ['data' => 'localidad'],
                ['data' => 'codigo_postal'],
                ['data' => 'acciones', 'orderable' => false, 'searchable' => false]
            ],
            'puedeCrear'      => $puedeCrear,
            'errores'         => []
        ];

        $this->loadView("partials/tablaAbmAjax", $datos);
    }

    public function create()
    {
        $deudos = $this->model->getAllDeudos();
        $datos = [
            'title'     => 'Crear Deudo',
            'action'    => URL . 'deudo/save',
            'values'    => [],
            'errores'   => [],
            'deudos'    => $deudos
        ];

        $this->loadView('deudos/DeudoForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'deudo/create');
            exit;
        }

        $dni            = trim($_POST['dni']);
        $nombre         = trim($_POST['nombre']);
        $apellido       = trim($_POST['apellido']);
        $telefono       = trim($_POST['telefono']);
        $email          = trim($_POST['email']);
        $domicilio      = trim($_POST['domicilio']);
        $localidad      = trim($_POST['localidad']);
        $codigo_postal  = trim($_POST['codigo_postal']);
        $errores        = [];
        
        if (empty($dni)) {
            $errores[] = "El DNI es obligatorio.";
        }
        if (empty($nombre)) {
            $errores[] = "Tenes que ingresar un nombre.";
        }
        if (empty($apellido)) {
            $errores[] = "Tenes que ingresar un apellido.";
        }
        if (empty($telefono)) {
            $errores[] = "Tenes que ingresar un telefono de referencia.";
        }
        if (empty($email)) {
            $errores[] = "Ingresa un mail.";
        }
        if (empty($domicilio)) {
            $errores[] = "Tiene que ingresar un domicilio.";
        }
        if (empty($localidad)) {
            $errores[] = "Tiene que ingresar una localidad.";
        }
        if (empty($codigo_postal)) {
            $errores[] = "El codigo postal es obligatorio.";
        }

        $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (!empty($errores)) {
            if ($es_ajax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errores]);
                exit;
            } else {
                $this->loadView('deudos/DeudoForm', [
                    'title' => 'Crear Deudo', 'action' => URL . 'deudo/save',
                    'values' => $_POST, 'errores' => $errores
                ]);
                return;
            }
        }

        $nuevo_ingreso = $this->model->insertDeudo($dni, $nombre, $apellido, $telefono, $email, $domicilio, $localidad, $codigo_postal);
        
        if ($nuevo_ingreso) {
            if ($es_ajax) {
                $texto_completo = '';
                if (!empty($dni)) {
                    $texto_completo = "$dni - $apellido, $nombre";
                } else {
                    $texto_completo = "$apellido, $nombre";
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'newItem' => [
                        'id'   => $nuevo_ingreso,
                        'text' => strtoupper($texto_completo)
                    ]
                ]);
                exit;
            } else {
                header("Location: " . URL . "deudo");
                exit;
            }
        } else {
            $errorMsg = ['Error al guardar el deudo en la base de datos.'];
            if ($es_ajax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errorMsg]);
                exit;
            } else {
                die($errorMsg[0]);
            }
        }
    }

    public function edit($id)
    {
        $deudo = $this->model->getDeudo($id);

        if (!$deudo) {
            die("Deudo no encontrado.");
        }

        $this->loadView("deudos/DeudoForm", [
            'title' => 'Editar Deudo',
            'action' => URL . 'deudo/update/' . $id,
            'values' => [
                'dni'           => $deudo['dni'],
                'nombre'        => $deudo['nombre'],
                'apellido'      => $deudo['apellido'],
                'telefono'      => $deudo['telefono'],
                'email'         => $deudo['email'],
                'domicilio'     => $deudo['domicilio'],
                'localidad'     => $deudo['localidad'],
                'codigo_postal' => $deudo['codigo_postal'],
            ],
            'errores' => [],
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $campos = [
                'dni'           => 'DNI',
                'nombre'        => 'nombre',
                'apellido'      => 'apellido',
                'telefono'      => 'teléfono',
                'email'         => 'dirección de mail',
                'domicilio'     => 'domicilio',
                'localidad'     => 'localidad',
                'codigo_postal' => 'código postal',
            ];

            $datos = [];
            $errores = [];

            foreach ($campos as $campo => $etiqueta) {
                if (isset($_POST[$campo])) {
                    $valor = trim($_POST[$campo]);
                } else {
                    $valor = '';
                }

                $datos[$campo] = $valor;

                if (empty($valor)) {
                    $errores[] = "Tiene que ingresar un {$etiqueta}";
                }
            }

            if (!empty($errores)) {
                $this->loadView("deudos/DeudosForm", [
                    'title'   => 'Editar Deudo',
                    'action'  => URL . 'deudo/update/' . $id,
                    'values'  => $datos,
                    'errores' => $errores,
                ]);
                return;
            }

            $exito = $this->model->updateDeudo(
                $id,
                $datos['dni'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['telefono'],
                $datos['email'],
                $datos['domicilio'],
                $datos['localidad'],
                $datos['codigo_postal']
            );

            if ($exito) {
                header("Location: " . URL . "deudo");
                exit;
            } else {
                die("Error al actualizar el servicio.");
            }
        }
    }
    public function delete($id)
    {
        $eliminado = $this->model->deleteDeudo($id);

        if (!$eliminado) {
            die('Error al eliminar el deudo');
        }
        header("Location: " . URL . "deudo");
        exit;
    }

    public function ajax()
    {
        header('Content-Type: application/json; charset=utf-8');
      
        if ($_POST['draw']) { $draw   = $_POST['draw']; } else { $draw = 1; }
        if (intval($_POST['start'])) { $start = intval($_POST['start']); } else { $start = 0; }
        if (intval($_POST['length'])) { $length = intval($_POST['length']); } else { $length = 10; }
        if ($_POST['search']['value']) { $search = $_POST['search']['value']; } else { $search = ''; }

        $orderColumnIndex = 0;
        $orderDir = 'asc';

        if (isset($_POST['order'][0]['column'])) { $orderColumnIndex = $_POST['order'][0]['column']; } else { $orderColumnIndex = 0; }
        if (isset($_POST['order'][0]['dir'])) { $orderDir = $_POST['order'][0]['dir']; } else { $orderDir = 'asc'; }

        $columns = ['id_deudo', 'dni', 'nombre', 'apellido', 'telefono', 'email', 'domicilio', 'localidad', 'codigo_postal'];

        if (isset($columns[$orderColumnIndex])) {
            $orderCol = $columns[$orderColumnIndex];
        } else {
            $orderCol = 'id_deudo';
        }

        $totalRecords = $this->model->countAll();

        if ($search) {
            $data = $this->model->getFiltered($search, $orderCol, $orderDir, $start, $length);
            $filteredRecords = $this->model->countFiltered($search);
        } else {
            $data = $this->model->getPage($orderCol, $orderDir, $start, $length);
            $filteredRecords = $totalRecords;
        }

        foreach ($data as &$fila) {
            $id  = $fila['id_deudo'];
            $url = rtrim(URL, '/') . '/deudo';

            $acciones = '';

            if ($this->can('editar_deudo')) {
                $acciones .= '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a> ';
                $acciones .= '<form action="' . $url . '/activate/' . $id . '" method="post" style="display:inline">'
                    . '</form> ';
            }

            if ($this->can('eliminar_deudo')) {
                $acciones .= '<form action="' . $url . '/delete/' . $id . '" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este deudo?\');">'
                    . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                    . '</form>';
            }

            $fila['acciones'] = $acciones;
        }

        echo json_encode([
            "draw"              => intval($draw),
            "recordsTotal"      => $totalRecords,
            "recordsFiltered"   => $filteredRecords,
            "data"              => $data
        ]);
        exit;
    }
}
