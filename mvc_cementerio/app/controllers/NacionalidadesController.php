<?php
class NacionalidadesController extends Control {
    private NacionalidadesModel $model;

     public function __construct() {
        $this->requireLogin();
        $this->model = $this->loadModel("NacionalidadesModel");
    }

    public function index(){
        $nacionalidades = $this->model->getAllNacionalidades();
        $datos = [
            'title'             => 'Lista de Nacionalidades',
            'urlCrear'          => URL . 'nacionalidades/create',
            'columnas'          => ['ID', 'Nacionalidad'],
            'columnas_claves'   =>['id_nacionalidad', 'nacionalidad'],
            'data'              => $nacionalidades,
            'acciones'          => function($fila){
                $id     = $fila['id_nacionalidad'];
                $url    = URL . 'nacionalidades';
                return '<a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-outline-primary">Editar</a>
                <a href="' . $url . '/delete/' . $id . '" class="btn btn-sm btn-outline-primary">Eliminar</a>';
            },
            'errores'=> [],
        ];
        $this->loadView('partials/tablaAbm', $datos);
    }

    public function create(){
        $datos = [
            'title'     => 'Crear nacionalidad',
            'action'    => URL . 'nacionalidades/save',
            'values'    => [],
            'errores'   => [],
        ];
        $this->loadView('nacionalidades/NacionalidadesForm', $datos);
    }

    public function save(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
             if (isset($_POST["nacionalidad"])) {
                $nacionalidad = trim($_POST["nacionalidad"]);
            } else {
                $nacionalidad = '';
            }

            if(empty($nacionalidad))
                $errores[] = "La nacionalidad es obligatoria";

            if(!empty($errores)){
                $this->loadView('nacionalidades/NacionalidadesForm', [
                    'title'     => 'Crear Nacionalidad',
                    'action'    => URL  . 'nacionalidades/save',
                    'values'    => $_POST,
                    'errores'   => $errores,
                ]);
                return;
            }

            if($this->model->insertNacionalidad($nacionalidad)){
                header("Location: " . URL . "nacionalidades");
                exit;
            }else{
                die("Error al crear la nacionalidad");
            }
        }
    }

    public function edit($id){
        $nacionalidad = $this->model->getNacionalidad($id);
        
        if(!$nacionalidad){
            die("Nacionalidad no encontrada");
        }

        $this->loadView('nacionalidades/NacionalidadesForm', [
            'title'     => 'Editar nacionalidad',
            'action'    => URL . 'nacionalidades/update/' . $id,
            'values'    => [
                'nacionalidad' => $nacionalidad["nacionalidad"],
            ],
            'errores' => [],
        ]);

    }

    public function update($id){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

           if (isset($_POST["nacionalidad"])) {
                $nombreNacionalidad = trim($_POST["nacionalidad"]);
            } else {
                $nombreNacionalidad = '';
            }

            if(empty($nombreNacionalidad))
                $errores[] = "La nacionalidad es obligatoria";

            if(!empty($errores)){
                $nacionalidad = [
                    'id_nacionalidad'   => $id,
                    'nacionalidad'      => $nombreNacionalidad,
                ];

            $this->loadView('nacionalidades/NacionalidadesForm', [
                'title'     => 'Editar nacionalidad',
                'action'    => URL . 'nacionalidades/update/' . $id,
                'values'    => [
                'nacionalidad' => $nacionalidad["nacionalidad"],
                ],
                'errores'   => [],
            ]);
            return;
            }
            if($this->model->updateNacionalidad($id, $nombreNacionalidad)){
                header("Location: " . URL . "nacionalidades");
                exit;
            }else{
                die("Error al modificar la nacionalidad");
            }


        }
    }

    public function delete($id){
        if($this->model->deleteNacionalidad($id)){
            header("Location: " . URL . "nacionalidades");
            exit;
        }else{
            die("No se pudo eliminar la nacionalidad");
        }
    }

    

}
?>