<?php
function isLoggedIn():bool
{
    return !empty($_SESSION['usuario_id']);
}

function currentUser(): ?array 
{
    if (!isLoggedIn()) { return null; }
    return [
        'id'        => (int)($_SESSION['usuario_id']),
        'nombre'    => $_SESSION['usuario_nombre'],
        'apellido'  => $_SESSION['usuario_apellido'],
        'rol'       => (int)$_SESSION['usuario_tipo'],
        'permisos'  => $_SESSION['usuario_permisos'],
    ];
}

function userHasPermission(string $permiso): bool 
{
    if (!isLoggedIn())  { return false; }

    if (isset($_SESSION['usuario_permisos'])){
        $permisos = $_SESSION['usuario_permisos'];
    }else{
        $permisos = [];
    }
    return in_array($permiso, $permisos, true);
}

function requirePermission(string|array $permisos, ?string $redirect = null): void 
{
    $base     = rtrim(URL, '/');
    $loginUrl = $base . '/login';
    if (!$redirect)
        $redirect = $base . '/error-permisos';

    if (!isLoggedIn()) {
        header('Location: ' . $loginUrl, true, 303); 
        exit;
    }

    foreach ((array)$permisos as $p) {
        if (userHasPermission($p)) {
            return;
        }
    }
    
    $_SESSION['missing_perms'] = (array)$permisos;
    header('Location: ' . $redirect, true, 303);
    exit;
}

function requireAllPermissions(array $permisos, ?string $redirect = null): void
{
    $base     = rtrim(URL, '/');
    if (!$redirect) {
        $redirect = $base . '/error-permisos';
    }

    if (!isLoggedIn()) {
        header('Location: ' . $base . '/login', true, 303); exit;
    }

    if ($_SESSION['usuario_permisos']) { $userPerms = $_SESSION['usuario_permisos']; } else { $userPerms = []; }
    $faltantes = array_diff($permisos, $userPerms);

    if (!$faltantes) return;

    $_SESSION['flash_error'] = 'No tenés permisos para acceder.';
    header('Location: ' . $redirect, true, 303); exit;
}

