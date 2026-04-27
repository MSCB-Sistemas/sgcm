<?php 
$user = currentUser();
$base = rtrim(URL, '/');

$can = function($perm) use ($user) 
{
    if ($perm === '__login__') {
        return (bool)$user;
    }

    if ($user) {
        if (isset($user['permisos']) && in_array($perm, $user['permisos'], true)) 
        {
            return true;
        } else {  
            return false; 
        }
    } else {   return false; }
};

$canAny = function(array $perms) use ($can) 
{
    if (empty($perms)) return true;     
    foreach ($perms as $p) {
        if ($can($p)) { return true; }
    }
    return false;
};

$guardToPerms = function($guard) 
{
    if ($guard === '__public__' || $guard === null) return [];      
    if ($guard === '__login__') return ['__login__'];               
    if (is_string($guard)) return [$guard];                        
    if (is_array($guard))  return $guard;                       
    return [];
};

$requestPath = '';
if (isset($_SERVER['REQUEST_URI'])) 
{
    $parsed = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($parsed !== null) {
        $requestPath = $parsed;
    } else {
        $requestPath = '';
    }
} else {
    $requestPath = '';
}

$activePath = trim($requestPath, '/');

$routes = require APP . '/config/routes.php';

$indexRoutes = [];
foreach ($routes as $key => [$ctrl, $method, $guard]) 
{
    if (strpos($key, '/') === false) 
    {
        $ignorar = ['', 'login', 'logout', 'error-permisos'];
        
        if (in_array($key, $ignorar, true)) continue;
        $indexRoutes[$key] = $routes[$key];
    }
}

$labelFor = [
  'home'            => 'Home',
  'estadisticas'    => 'Listas y Estadísticas',
  'operacion'       => 'Operaciones',
  'usuario'         => 'Usuarios',
  'deudo'           => 'Deudos',
  'difunto'         => 'Difuntos',
  'estadoCivil'     => 'Estados Civiles',
  'parcela'         => 'Parcelas',
  'ubicacion'       => 'Ubicaciones',
  'orientaciones'   => 'Orientaciones',
  'sexo'            => 'Sexos',
  'nacionalidades'  => 'Nacionalidades',
  'tipoParcela'     => 'Tipos de parcela',
  'tipoUsuario'     => 'Tipos de usuario',
  'tipoOperacion'   => 'Tipos de operaciones',
  'pago'            => 'Pagos',
];

$groupFor = [
  'home'           => null,
  'estadisticas'   => null,
  'operacion'      => null,
  
  'usuario'        => 'ABM',
  'deudo'          => 'ABM',
  'difunto'        => 'ABM',
  'estadoCivil'    => 'ABM',
  'parcela'        => 'ABM',
  'ubicacion'      => 'ABM',
  'orientaciones'  => 'ABM',
  'sexo'           => 'ABM',
  'nacionalidades' => 'ABM',
  'tipoParcela'    => 'ABM',
  'tipoUsuario'    => 'ABM',
  'tipoOperacion'  => 'ABM',
  'pago'           => 'ABM',
];

$iconFor = [
  'home'            => ['icon' => '#home'],  
  'estadisticas'    => ['bi'   => 'graph-up'], 
  'operacion'       => ['bi'   => 'bar-chart-line'],
  'usuario'         => ['bi'   => 'person-plus-fill'],
  'deudo'           => ['bi'   => 'person-badge'],
  'difunto'         => ['bi'   => 'snapchat'],
  'estadoCivil'     => ['bi'   => 'people'],
  'parcela'         => ['bi'   => 'grid-3x3-gap'],
  'ubicacion'       => ['bi'   => 'geo-alt'],
  'orientaciones'   => ['bi'   => 'compass'],
  'sexo'            => ['bi'   => 'gender-ambiguous'],
  'nacionalidades'  => ['bi'   => 'flag'],
  'tipoParcela'     => ['bi'   => 'columns-gap'],
  'tipoUsuario'     => ['bi'   => 'person-gear'],
  'tipoOperacion'   => ['bi'   => 'tags-fill'],
  'pago'            => ['bi'   => 'credit-card'],
];

$MENU = [];
$solo = [];
$abmChildren = [];

foreach ($indexRoutes as $path => $def) 
{
    [$ctrl, $method, $guard] = $def;
    if (isset($iconFor[$path])) { $icon = $iconFor[$path]; } else { $icon = []; }
    if (isset($labelFor[$path])) { $label = $labelFor[$path]; } else { $label = ucfirst($path); }
    $perms = $guardToPerms($guard);
    $href  = $base . '/' . $path;
    $group = array_key_exists($path, $groupFor) ? $groupFor[$path] : 'ABM';
    
    if ($group === null) 
    {
        $solo []= ['label' => $label, 'href' => $href, 'perms' => $perms] + $icon;
        continue;
    }

    if ($group === 'ABM') 
    {
        $abmChildren[] = ['label'=>$label, 'href'=>$href, 'perms'=>$perms] + $icon;
        continue;
    }    
}

$MENU = $solo;

if (!empty($abmChildren)) 
{
    $MENU[] = [
        'label' => 'Alta, Baja y Modificación',
        'perms' => [],                
        'children' => $abmChildren
    ];
}
?>

<?php   #echo '<pre>'; print_r($MENU); echo '</pre>';
#exit;
require_once APP . '/views/inc/header.php' ?>

<body>
    <?php require_once APP . '/views/inc/navbar.php'; ?>
    <div class="container-fluid py-4 main-content">
        <?php require_once $viewPath; ?>
    </div>
    <?php require_once APP . '/views/inc/footer.php' ?>