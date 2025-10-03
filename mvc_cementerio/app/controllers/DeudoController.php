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
            'columnas'  => ['ID', 'DNI', 'Nombre', 'Apellido', 'Vínculo Familiar', 'Teléfono', 'Email', 'Domicilio', 'Localidad', 'Código Postal'],
            'columnsConfig'     => [
                ['data' => 'id_deudo'],
                ['data' => 'dni'],
                ['data' => 'nombre'],
                ['data' => 'apellido'],
                ['data' => 'vinculo_familiar'],
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni            = trim($_POST['dni']);
            $nombre         = trim($_POST['nombre']);
            $apellido       = trim($_POST['apellido']);
            $vinculo_familiar = trim($_POST['vinculo_familiar']);
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
            if (empty($vinculo_familiar)) {
                $errores[] = "Tenes que ingresar un vínculo familiar con el difunto.";
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
                        'dni'           => $dni,
                        'nombre'        => $nombre,
                        'apellido'      => $apellido,
                        'vinculo_familiar' => $vinculo_familiar,
                        'telefono'      => $telefono,
                        'email'         => $email,
                        'domicilio'     => $domicilio,
                        'localidad'     => $localidad,
                        'codigo_postal' => $codigo_postal
                    ],
                    'errores' => $errores
                ];
                $this->loadView('deudos/DeudoForm', $datos);
                return;
            }

            $idDeudo = $this->model->insertDeudo($dni, $nombre, $apellido, $vinculo_familiar, $telefono, $email, $domicilio, $localidad, $codigo_postal);
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
                'dni'           => $deudo['dni'],
                'nombre'        => $deudo['nombre'],
                'apellido'      => $deudo['apellido'],
                'vinculo_familiar' => $deudo['vinculo_familiar'],
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
            // Definir campos y etiquetas personalizadas
            $campos = [
                'dni'           => 'DNI',
                'nombre'        => 'nombre',
                'apellido'      => 'apellido',
                'vinculo_familiar' => 'vínculo familiar',
                'telefono'      => 'teléfono',
                'email'         => 'dirección de mail',
                'domicilio'     => 'domicilio',
                'localidad'     => 'localidad',
                'codigo_postal' => 'código postal',
            ];

            $datos = [];
            $errores = [];

            // Procesar todos los campos
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

            // Si hay errores, mostrar el formulario con datos y errores
            if (!empty($errores)) {
                $this->loadView("deudos/DeudosForm", [
                    'title'   => 'Editar Deudo',
                    'action'  => URL . 'deudo/update/' . $id,
                    'values'  => $datos,
                    'errores' => $errores,
                ]);
                return;
            }

            // Intentar actualizar el modelo
            $exito = $this->model->updateDeudo(
                $id,
                $datos['dni'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['vinculo_familiar'],
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
        
        // Función auxiliar para obtener valores simples de $_POST
        function getPost($key, $default = '') {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            } else {
                return $default;
            }
        }

        // Función auxiliar para obtener valores anidados (por ejemplo: $_POST['order'][0]['dir'])
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

        // Obtener parámetros de DataTables sin estructuras ternarias
        $draw = getPost('draw', 1);

        $startRaw = getPost('start', 0);
        $start = intval($startRaw);

        $lengthRaw = getPost('length', 10);
        $length = intval($lengthRaw);

        $search = getNestedPost(['search', 'value'], '');

        $orderColumnIndex = getNestedPost(['order', 0, 'column'], 0);
        $orderDir = getNestedPost(['order', 0, 'dir'], 'asc');

        // Columnas permitidas para ordenamiento
        $columns = ['id_deudo','dni','nombre','apellido', 'vinculo_familiar', 'telefono','email','domicilio','localidad','codigo_postal'];

        // Validar si el índice de columna existe
        if (isset($columns[$orderColumnIndex])) {
            $orderCol = $columns[$orderColumnIndex];
        } else {
            $orderCol = 'id_deudo';
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

        foreach ($data as &$fila) {
            $id  = $fila['id_deudo'];
            $url = rtrim(URL,'/') . '/deudo';
    
            $acciones = '';
    
            if ($this->can('editar_deudo')) {
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> ';
                $acciones .= '<form action="'.$url.'/activate/'.$id.'" method="post" style="display:inline">'
                           . '</form> ';
            }
    
            if ($this->can('eliminar_deudo')) {
                $acciones .= '<form action="'.$url.'/delete/'.$id.'" method="post" style="display:inline" onsubmit="return confirm(\'¿Eliminar este deudo?\');">'
                           . '<button class="btn btn-sm btn-danger">Eliminar</button>'
                           . '</form>';
            }
    
            $fila['acciones'] = $acciones; // Esta es la clave que espera DataTables
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
?>