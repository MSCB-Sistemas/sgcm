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
        $datos = ['title' => 'Home'];
        $this->loadView('home/HomeView', $datos);
    }

    public function login() {
        $this->loadView('LoginView', [], 'login');
    }
}
