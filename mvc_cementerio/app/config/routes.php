<?php
return [

// Login / Home / Estadísticas
    ''                => ['AuthController',       'login',        '__public__'],
    'login'           => ['AuthController',       'login',        '__public__'],
    'logout'          => ['AuthController',       'logout',       '__login__'],
    'home'            => ['HomeController',       'index',        '__login__'],
    
// Listados / estadísticas
    'estadisticas'    => ['EstadisticasController','index',       'ver_estadisticas'],

// ABM (rutas “index” con sus permisos)
    // Usuario 
    'usuario'         => ['UsuarioController',    'index',        'ver_usuario'],
    'usuario/create'  => ['UsuarioController',    'create',       'crear_usuario'],
    'usuario/save'    => ['UsuarioController',    'save',         'crear_usuario'],
    'usuario/edit'    => ['UsuarioController',    'edit',         'editar_usuario'],
    'usuario/update'  => ['UsuarioController',    'update',       'editar_usuario'],
    'usuario/delete'  => ['UsuarioController',    'delete',       'eliminar_usuario'],
    'usuario/activate'=> ['UsuarioController',    'activate',     'editar_usuario'],
    'usuario/changePass'=>['UsuarioController',   'changePass',   '__login__'],
    'usuario/savePass'=> ['UsuarioController',    'savePass',     '__login__'],

    // Difunto
    'difunto'         => ['DifuntoController',    'index',        'ver_difunto'],
    'difunto/create'  => ['DifuntoController',    'create',       'crear_difunto'],
    'difunto/save'    => ['DifuntoController',    'save',         'crear_difunto'],
    'difunto/edit'    => ['DifuntoController',    'edit',         'editar_difunto'],
    'difunto/update'  => ['DifuntoController',    'update',       'editar_difunto'],
    'difunto/delete'  => ['DifuntoController',    'delete',       'eliminar_difunto'],
    'difunto/verificar' => ['DifuntoController',    'verificar',    '__login__'],
    'difunto/ajax'    => [DifuntoController::class, 'ajax',          '__login__'],

    // Estado civil
    'estadoCivil'         => ['EstadoCivilController', 'index',   'ver_estado_civil'],
    'estadoCivil/create'  => ['EstadoCivilController', 'create',  'crear_estado_civil'],
    'estadoCivil/save'    => ['EstadoCivilController', 'save',    'crear_estado_civil'],
    'estadoCivil/edit'    => ['EstadoCivilController', 'edit',    'editar_estado_civil'],
    'estadoCivil/update'  => ['EstadoCivilController', 'update',  'editar_estado_civil'],
    'estadoCivil/delete'  => ['EstadoCivilController', 'delete',  'eliminar_estado_civil'],

    // Parcela
    'parcela'                    => ['ParcelaController','index',            'ver_parcela'],
    'parcela/create'             => ['ParcelaController','create',           'crear_parcela'],
    'parcela/save'               => ['ParcelaController','save',             'crear_parcela'],
    'parcela/edit'               => ['ParcelaController','edit',             'editar_parcela'],
    'parcela/update'             => ['ParcelaController','update',           'editar_parcela'],
    'parcela/delete'             => ['ParcelaController','delete',           'eliminar_parcela'],
    'parcela/obtenerInfoParcela' => ['ParcelaController', 'obtenerInfoParcela', 'ver_parcela'],

    // Sexo
    'sexo'            => ['SexoController', 'index',              'ver_sexo'],
    'sexo/create'     => ['SexoController', 'create',             'crear_sexo'],
    'sexo/save'       => ['SexoController', 'save',               'crear_sexo'],
    'sexo/edit'       => ['SexoController', 'edit',               'editar_sexo'],
    'sexo/update'     => ['SexoController', 'update',             'editar_sexo'],
    'sexo/delete'     => ['SexoController', 'delete',             'eliminar_sexo'],

    // Pago
    'pago'            => ['PagoController', 'index',              'ver_pago'],
    'pago/create'     => ['PagoController', 'create',             'crear_pago'],
    'pago/save'       => ['PagoController', 'save',               'crear_pago'],
    'pago/edit'       => ['PagoController', 'edit',               'editar_pago'],
    'pago/update'     => ['PagoController', 'update',             'editar_pago'],
    'pago/delete'     => ['PagoController', 'delete',             'eliminar_pago'],
    'pago/ajax'       => [PagoController::class, 'ajax',          '__login__'],

    // TipoParcela
    'tipoParcela'         => ['TipoParcelaController', 'index',   'ver_tipo_parcela'],
    'tipoParcela/create'  => ['TipoParcelaController', 'create',  'crear_tipo_parcela'],
    'tipoParcela/save'    => ['TipoParcelaController', 'save',    'crear_tipo_parcela'],
    'tipoParcela/edit'    => ['TipoParcelaController', 'edit',    'editar_tipo_parcela'],
    'tipoParcela/update'  => ['TipoParcelaController', 'update',  'editar_tipo_parcela'],
    'tipoParcela/delete'  => ['TipoParcelaController', 'delete',  'eliminar_tipo_parcela'],

    // TipoUsuario
    'tipoUsuario'         => ['TipoUsuariosController', 'index',  'ver_tipo_usuario'],
    'tipoUsuario/create'  => ['TipoUsuariosController', 'create', 'crear_tipo_usuario'],
    'tipoUsuario/save'    => ['TipoUsuariosController', 'save',   'crear_tipo_usuario'],
    'tipoUsuario/edit'    => ['TipoUsuariosController', 'edit',   'editar_tipo_usuario'],
    'tipoUsuario/update'  => ['TipoUsuariosController', 'update', 'editar_tipo_usuario'],
    'tipoUsuario/delete'  => ['TipoUsuariosController', 'delete', 'eliminar_tipo_usuario'],

    // Deudo
    'deudo'           => ['DeudoController', 'index',             'ver_deudo'],
    'deudo/create'    => ['DeudoController', 'create',            'crear_deudo'],
    'deudo/save'      => ['DeudoController', 'save',              'crear_deudo'],
    'deudo/edit'      => ['DeudoController', 'edit',              'editar_deudo'],
    'deudo/update'    => ['DeudoController', 'update',            'editar_deudo'],
    'deudo/delete'    => ['DeudoController', 'delete',            'eliminar_deudo'],
    'deudo/verificar' => ['DeudoController',    'verificar',    '__login__'],
    'deudo/ajax'      => [DeudoController::class, 'ajax', '__login__'],

    // Nacionalidades
    'nacionalidades'         => ['NacionalidadesController','index', 'ver_nacionalidad'],
    'nacionalidades/create'  => ['NacionalidadesController','create','crear_nacionalidad'],
    'nacionalidades/save'    => ['NacionalidadesController','save',  'crear_nacionalidad'],
    'nacionalidades/edit'    => ['NacionalidadesController','edit',  'editar_nacionalidad'],
    'nacionalidades/update'  => ['NacionalidadesController','update','editar_nacionalidad'],
    'nacionalidades/delete'  => ['NacionalidadesController','delete','eliminar_nacionalidad'],

    // Orientaciones
    'orientaciones'         => ['OrientacionController','index',   'ver_orientacion'],
    'orientaciones/create'  => ['OrientacionController','create',  'crear_orientacion'],
    'orientaciones/save'    => ['OrientacionController','save',    'crear_orientacion'],
    'orientaciones/edit'    => ['OrientacionController','edit',    'editar_orientacion'],
    'orientaciones/update'  => ['OrientacionController','update',  'editar_orientacion'],
    'orientaciones/delete'  => ['OrientacionController','delete',  'eliminar_orientacion'],

    // Ubicaciones
    'ubicacion'         => ['UbicacionDifuntoController','index',  'ver_ubicacion'],
    'ubicacion/create'  => ['UbicacionDifuntoController','create', 'crear_ubicacion'],
    'ubicacion/save'    => ['UbicacionDifuntoController','save',   'crear_ubicacion'],
    'ubicacion/edit'    => ['UbicacionDifuntoController','edit',   'editar_ubicacion'],
    'ubicacion/update'  => ['UbicacionDifuntoController','update', 'editar_ubicacion'],
    'ubicacion/delete'  => ['UbicacionDifuntoController','delete', 'eliminar_ubicacion'],

    // Operacion
    'operacion'        => ['OperacionController', 'index',       'ver_operacion'],
    'operacion/create' => ['OperacionController', 'create',      'crear_operacion'],
    'operacion/save'   => ['OperacionController', 'save',        'crear_operacion'],
    'operacion/edit'   => ['OperacionController', 'edit',        'editar_operacion'],
    'operacion/update' => ['OperacionController', 'update',      'editar_operacion'],
    'operacion/delete' => ['OperacionController', 'delete',      'eliminar_operacion'],

    // Errores
    'error-permisos' => ['ErrorController', 'permisosError', '__public__'],
];

?>