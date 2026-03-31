<?php
require_once __DIR__ . '/../app/init.php';

$base = '/sgcm';

$routes = require __DIR__ . '/../app/config/routes.php';

if (isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($uri === null)
        $uri = '';

} else {
    $uri = '';
}

if (!empty($base) && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

$uri = trim($uri, '/');

if ($uri === '') {
    $segments = [];
} else {
    $segments = explode('/', $uri);
}

if (isset($_SERVER['REQUEST_METHOD'])) {
    $method = $_SERVER['REQUEST_METHOD'];
} else {
    $method = 'GET';
}

$ruta = '';
$parametro = null;

if (count($segments) >= 2) {
    $ruta = $segments[0] . '/' . $segments[1];
    if (isset($segments[2])) {
        $parametro = $segments[2];
    }
} elseif (count($segments) === 1) {
    $ruta = $segments[0];
} else {
    $ruta = '';
}

if (!isset($routes[$ruta])) {
    echo errorMensaje('404', "Ruta '$ruta' no encontrada.");
    exit;
}

[$controlador, $metodo, $guard] = $routes[$ruta];

$baseUrl = rtrim(URL, '/');
$fallbackError = $baseUrl . '/error-permisos';

if ($guard === '__login__') {
    if (!isLoggedIn()) {
        header('Location: ' . $baseUrl . '/login');
        exit;
    }
} elseif (is_string($guard) && $guard !== '__public__') {
    requirePermission($guard, $fallbackError);
} elseif (is_array($guard)) {
    requirePermission($guard, $fallbackError);
}

$archivo = __DIR__ . '/../app/controllers/' . $controlador . '.php';

if (!file_exists($archivo)) {
    echo errorMensaje('404', "Archivo del controlador no encontrado.");
    exit;
}
require_once $archivo;

if (!class_exists($controlador)) {
    echo errorMensaje('404', "Controlador '$controlador' no encontrado.");
    exit;
}

$obj = new $controlador();

if (!method_exists($obj, $metodo)) {
    echo errorMensaje('405', "Método '$metodo' no existe.");
    exit;
}

try {
    if ($parametro !== null) {
        $obj->$metodo($parametro);
    } else {
        $obj->$metodo();
    }

} catch (Throwable $e) {
    echo errorMensaje('500', "Ups… algo salió mal: " . $e->getMessage());
    exit;
}

?>