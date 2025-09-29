<?php
class DeudoController extends Control {
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
            'columnas' => ['ID', 'DNI', 'Nombre', 'Apellido', 'Teléfono', 'Email', 'Domicilio', 'Localidad', 'Código Postal'],
            'columnsConfig'     => [
                ['data' => 'id_deudo'],
                ['data' => 'dni'],
                ['data' => 'nombre'],
                ['data' => 'apellido'],
                ['data' => 'telefono'],
                ['data' => 'email'],
                ['data' => 'domicilio'],
                ['data' => 'localidad'],
                ['data' => 'codigo_postal']
            ],
            "acciones"  => function (array $fila) use ($puedeEditar, $puedeEliminar)
            {
                $id = $fila['id_deudo'];
                $url = rtrim(URL,'/') . '/deudo';

                $html = '';
                if ($puedeEditar) 
                {
                    $html .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> ';
                    $html .= '<form action="'.$url.'/activate/'.$id.'" method="post" style="display:inline">'
                          .  '<button class="btn btn-sm btn-success" onclick="return confirm(\'¿Activar este deudo?\');">Activar</button>'
                          .  '</form> ';
                }
                if ($puedeEliminar) {
                    $html .= '<form action="'.$url.'/delete/'.$id.'" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este deudo?\');">'
                          .  '<button class="btn btn-sm btn-danger">Eliminar</button>'
                          .  '</form>';
                }
                return $html;
            },
            'accionesSampleData' => ['id_deudo' => 1],
            'puedeCrear'      => $puedeCrear,   // por si tu partial muestra el botón “Nuevo”
            'errores'         => [],
            'csrfToken'         => $this->generateCsrfToken()
        ];

        $this->loadView("partials/tablaAbm", $datos);
    }

    public function create()
    {
        $deudos = $this->model->getAllDeudos();
        $datos = [
            'title' => 'Crear Deudo',
            'action' => URL . 'deudo/save',
            'values' => [],
            'errores' => [],
            'deudos' => $deudos
        ];

        $this->loadView('deudos/DeudoForm', $datos);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = trim($_POST['dni']);
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $telefono = trim($_POST['telefono']);
            $email = trim($_POST['email']);
            $domicilio = trim($_POST['domicilio']);
            $localidad = trim($_POST['localidad']);
            $codigo_postal = trim($_POST['codigo_postal']);
            $errores = [];
            
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

            if (!empty($errores)) {
                $datos = [
                    'title' => 'Crear Deudo',
                    'action' => URL . 'deudo/save',
                    'values' => [
                        'dni' => $dni,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'telefono' => $telefono,
                        'email' => $email,
                        'domicilio' => $domicilio,
                        'localidad' => $localidad,
                        'codigo_postal' => $codigo_postal
                    ],
                    'errores' => $errores
                ];
                $this->loadView('deudos/DeudoForm', $datos);
                return;
            }

            $idDeudo = $this->model->insertDeudo($dni, $nombre, $apellido, $telefono, $email, $domicilio, $localidad, $codigo_postal);
            if ($idDeudo) {
                header('Location: ' . URL . 'deudo');
            } else {
                die('Error al guardar el deudo');
            }

            header('Location: ' . URL . 'deudo');
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
                'dni' => $deudo['dni'],
                'nombre' => $deudo['nombre'],
                'apellido' => $deudo['apellido'],
                'telefono' => $deudo['telefono'],
                'email' => $deudo['email'],
                'domicilio' => $deudo['domicilio'],
                'localidad' => $deudo['localidad'],
                'codigo_postal' => $deudo['codigo_postal'],
            ],
            'errores' => [],
        ]);
    }

    public function update($id){  
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = trim($_POST['dni']);
            
            if (isset($_POST['nombre'])) {
                $nombre = trim($_POST['nombre']);
            } else {
                $nombre = '';
            }
            if (isset($_POST['apellido'])) {
                $apellido = trim($_POST['apellido']);
            } else {
                $apellido = '';
            }
            if (isset($_POST['telefono'])) {
                $telefono = trim($_POST['telefono']);
            } else {
                $telefono = '';
            }
            if (isset($_POST['email'])) {
                $email = trim($_POST['email']);
            } else {
                $email = '';
            }
            if (isset($_POST['domicilio'])) {
                $domicilio = trim($_POST['domicilio']);
            } else {
                $domicilio = '';
            }
            if (isset($_POST['localidad'])) {
                $localidad = trim($_POST['localidad']);
            } else {
                $localidad = '';
            }
            if (isset($_POST['codigo_postal'])) {
                $codigo_postal = trim($_POST['codigo_postal']);
            } else {
                $codigo_postal = '';
            }

            $errores = [];
            if (empty($dni)) {
                $errores[] = "Tiene que ingresar un DNI";
            }
            if (empty($nombre)) {
                $errores[] = "Tiene que ingresar un nombre";
            }
            if (empty($apellido)) {
                $errores[] = "Tiene que ingresar un apellido";
            }
            if (empty($telefono)) {
                $errores[] = "Tiene que ingresar un telefono";
            }
            if (empty($email)) {
                $errores[] = "Tiene que ingresar una direccion de mail";
            }
            if (empty($domicilio)) {
                $errores[] = "Tiene que ingresar un domicilio";
            }
            if (empty($localidad)) {
                $errores[] = "Tiene que ingresar una localidad";
            }
            if (empty($codigo_postal)) {
                $errores[] = "Tiene que ingresar un código postal";
            }

            if (!empty($errores)) {
                $deudo = [
                    "dni" => $dni,
                    "nombre" => $nombre,
                    "apellido" => $apellido,
                    "telefono" => $telefono,
                    "email" => $email,
                    "domicilio" => $domicilio,
                    "localidad" => $localidad,
                    "codigo_postal" => $codigo_postal
                ];

                $this->loadView("deudos/DeudosForm", [
                    'title' => 'Editar Deudo',
                    'action' => URL . 'deudo/update/' . $id,
                    'values' => $deudo,
                    'errores' => $errores,
                ]);
                return;
            }

            if ($this->model->updateDeudo($id, $dni, $nombre, $apellido, $telefono, $email, $domicilio, $localidad, $codigo_postal)) {
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
        
        $draw   = $_POST['draw'] ?? 1;
        $start  = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc';

        $columns = ['id_deudo','dni','nombre','apellido','telefono','email','domicilio','localidad','codigo_postal'];
        $orderCol = $columns[$orderColumnIndex] ?? 'id_deudo';

        $totalRecords = $this->model->countAll();

        if ($search) {
            $data = $this->model->getFiltered($search, $orderCol, $orderDir, $start, $length);
            $filteredRecords = $this->model->countFiltered($search);
        } else {
            $data = $this->model->getPage($orderCol, $orderDir, $start, $length);
            $filteredRecords = $totalRecords;
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
        exit;
    }

    private function generateCsrfToken()
    {
        return $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>