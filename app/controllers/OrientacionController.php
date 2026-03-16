<?php
class OrientacionController extends Control {
    private OrientacionModel $model;

    public function __construct(){
        $this->requireLogin();
       $this->model = $this->loadModel("OrientacionModel"); 
    }

    public function index(){
        $orientaciones = $this->model->getAllOrientaciones();
        $datos = [
            'title'             => 'Lista de Orientaciones',
            'urlCrear'          => URL . 'orientaciones/create',
            'columnas'          => ['ID', 'Descripción'],
            'columnas_claves'   => ['id_orientacion', 'descripcion'],
            'data'              => $orientaciones,
            'acciones'          =>  function($fila){
                $id = $fila['id_orientacion'];
                $url = URL . 'orientaciones';
                return '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-primary">Editar</a>
                <a href="' . $url . '/delete/' . $id . '" class="btn btn-sm btn-danger">Eliminar</a>';
            },
            'errores' => [],
        ];
        $this->loadView('partials/tablaAbm', $datos);        
    }

    public function create(){
        $datos = [
            'title'     => 'Crear orientacion',
            'action'    => URL . 'orientaciones/save',
            'values'    => [],
            'errores'   => [],
        ];
        $this->loadView('orientaciones/OrientacionesForm', $datos);
    }

    public function save(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (isset($_POST["descripcion"])) {
                $descripcion = trim($_POST["descripcion"]);
            } else {
                $descripcion = '';
            }

            if(empty($descripcion))
                $errores[] = "La orientacion es obligatoria";

            if(!empty($errores)){
                $this->loadView('orientaciones/OrientacionesForm', [
                    'title'     => 'Crear Orientacion',
                    'action'    => URL . 'orientaciones/save',
                    'values'    => $_POST,
                    'errores'   => $errores,
                ]);
                return;
            }
        if($this->model->insertOrientacion($descripcion)){
            header("Location: " . URL . "orientaciones");
                exit;
            }else{
                die("Error al crear la orientación");
            }
        }    
    }

    public function edit($id){
        $orientacion = $this->model->getOrientacion($id);

        if(!$orientacion){
            die("Orientación no encontrada");
        }

        $this->loadView('orientaciones/OrientacionesForm', [
            'title'     => 'Editar Orientacion',
            'action'    => URL . 'orientaciones/update/' . $id,
            'values'    => [
                'descripcion' => $orientacion["descripcion"],
            ],
            'errores' => [],
        ]);
    }

    public function update($id){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (isset($_POST["descripcion"])) {
                $descripcion = trim($_POST["descripcion"]);
            } else {
                $descripcion = '';
            }

            $errores = [];
            if(empty($descripcion)){
                $errores[] = "La descripción es obligatoria";
            }
            if(!empty($errores)){
                $orientacion = [
                    'id_orientacion'    => $id,
                    'descripcion'       => $descripcion,
                ];
                
                $this->loadView('orientaciones/OrientacionesForm', [
                    'title'     => 'Editar Orientacion',
                    'action'    => URL . 'orientaciones/update/' . $id,
                    'values'    => [
                        'descripcion' => $orientacion["descripcion"],
                    ],
                    'errores'   => [],
                ]);
                return;
            }
            if($this->model->updateOrientacion($id, $descripcion)){
                header("Location: " . URL . "orientaciones");
                exit;
            } else {
                die("Error al modificar la orientación");
            }
        }
    }
    
    public function delete($id){
        if($this->model->deleteOrientacion($id)){
            header("Location: " . URL . "orientaciones");
            exit;
        }else{
            die("No se pudo eliminar la orientación");
        }
    } 
}

?>