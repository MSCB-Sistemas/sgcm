<?php
class ErrorController extends Control {
    public function permisosError() {
        echo errorMensaje('403', 'No tenés permisos para acceder a esta sección.');
        exit;
    }
}
?>
