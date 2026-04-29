<?php
class HomeController extends Control
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->loadModel("UsuarioModel");
    }

    public function index()
    {
        $usuario = $this->model->getUsuarioId($_SESSION['usuario_id']);
        
        $datos = [
            'title' => 'Inicio',
            'usuario' => $usuario
        ];

        $this->loadView('home/HomeView', $datos);
    }

    public function login()
    {
        $this->loadView('LoginView', [], 'login');
    }
}
