<?php

$error = '';
$filtrar = isset($_GET['filtrar']);

?>
    <link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tablas-tab" data-bs-toggle="tab" data-bs-target="#tablas" type="button" role="tab">Padron difuntos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="morosos-tab" data-bs-toggle="tab" data-bs-target="#morosos" type="button" role="tab">Deudores Morosos
                <?php if (!empty($datos['total_morosos']) && $datos['total_morosos'] > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $datos['total_morosos'] ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="traslados-tab" data-bs-toggle="tab" data-bs-target="#traslados" type="button" role="tab">Traslados de difuntos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="vendidas-tab" data-bs-toggle="tab" data-bs-target="#vendidas" type="button" role="tab">Parcelas Vendidas</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button" role="tab">Estadisticas</button>
        </li>
    </ul>

    <div class="tab-content mt-4">
        <!-- Pestaña para Padron de Difuntos -->
        <div class="tab-pane fade show active" id="tablas" role="tabpanel">
            <!-- Seleccionar tipo de filtro de búsqueda -->
            <div class="mb-4">
                <label for="tipo_filtro" class="form-label">Seleccionar filtro de búsqueda:</label>
                <select id="tipo_filtro" class="form-select w-auto" onchange="mostrarFiltroDifuntos()">
                    <option value="">Seleccionar...</option>
                    <option value="filtro_fecha_difuntos">Por Fecha de Defunción</option>
                    <option value="lista_completa_difuntos">Ver lista completa</option>
                </select>
        </div>

        <!-- Filtro por Fecha -->
        <div id="filtro_fecha_difuntos" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_defuncion" class="form-label">Desde</label>
                    <?php
                    if (isset($_GET['fecha_inicio_defuncion'])) {
                        $fecha_inicio_defuncion = $_GET['fecha_inicio_defuncion'];
                    } else {
                        $fecha_inicio_defuncion = '';
                    }
                    ?>
                    <input type="date" class="form-control" name="fecha_inicio_defuncion" value="<?= htmlspecialchars($fecha_inicio_defuncion) ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_defuncion" class="form-label">Hasta</label>

                    <?php
                    if (isset($_GET['fecha_fin_defuncion'])) {
                        $fecha_fin_defuncion = $_GET['fecha_fin_defuncion'];
                    } else {
                        $fecha_fin_defuncion = '';
                    }
                    ?>
                    <input type="date" class="form-control" name="fecha_fin_defuncion" value="<?= htmlspecialchars($fecha_fin_defuncion) ?>">                      
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
                </div>

        <!-- Mostrar error solo si hay -->
        <?php if ($error): ?>
            <div class="alert alert-warning text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($datos['movimientos'])): ?>
        <table class="table table-bordered table-striped">
            <thead class="th a">
                <tr>
                    <th style="color: white; text-decoration: none;">Fecha de fallecimiento</th>
                        <th style="color: white; text-decoration: none;">Nombre</th>
                        <th style="color: white; text-decoration: none;">Apellido</th>
                        <th style="color: white; text-decoration: none;">Edad</th>
                        <th style="color: white; text-decoration: none;">DNI</th>
                        <th style="color: white; text-decoration: none;">Deudo</th> 
                        <th style="color: white; text-decoration: none;">Estado Civil</th>
                        <th style="color: white; text-decoration: none;">Nacionalidad</th>  
                        <th style="color: white; text-decoration: none;">Sexo</th>
                        <th style="color: white; text-decoration: none;">Domicilio</th>
                        <th style="color: white; text-decoration: none;">Localidad</th>
                        <th style="color: white; text-decoration: none;">Código Postal</th>  
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($datos['movimientos'] as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['fecha_fallecimiento']) ?></td>
                            <td><?= htmlspecialchars($m['nombre']) ?></td>
                            <td><?= htmlspecialchars($m['apellido']) ?></td>
                            <td><?= htmlspecialchars($m['edad']) ?></td>
                            <td><?= htmlspecialchars($m['dni']) ?></td>
                            <td><?= htmlspecialchars($m['deudo']) ?></td>
                            <td><?= htmlspecialchars($m['estado_civil']) ?></td>
                            <td><?= htmlspecialchars($m['nacionalidad'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['sexo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['domicilio']) ?></td>
                            <td><?= htmlspecialchars($m['localidad']) ?></td>
                            <td><?= htmlspecialchars($m['codigo_postal']) ?></td> 
                        </tr>
                    <?php endforeach; ?>
                
            </tbody>
        </table>
         <?php else: ?>
                    <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="text-muted">No hay difuntos registrados</p>
            </div>
                <?php endif; ?>               
        <?php if (!empty($datos['total_paginas']) && ($datos['total_paginas']) > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                    <?php
                    // Determinar la clase
                    $class = 'page-item';
                    if ($i == $datos['pagina_actual']) {
                        $class .= ' active';
                    }

                    // Construir la URL de la página actual con los parámetros existentes
                    $queryParams = $_GET;
                    $queryParams['pagina'] = $i;
                    $url = '?' . http_build_query($queryParams);
                    ?>
                    <li class="<?= $class ?>">
                        <a class="page-link" href="<?= $url ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Pestaña para deudores morosos-->
    <div class="tab-pane fade" id="morosos" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" id="ver-activos">
                    <i class="fas fa-toggle-on me-1"></i> Pagos vencidos
                </button>
                <button type="button" class="btn btn-outline-secondary" id="ver-inactivos">
                    <i class="fas fa-toggle-off me-1"></i> Pagos saldados
                </button>
            </div>
        </div>

        <?php if (!empty($datos['deudores_morosos'])): ?>
            <table class="table table-bordered table-striped" id="tabla-morosos">
                <thead class="th a">
                    <tr>
                        <th style="color: white; text-decoration: none;">Parcela</th>
                        <th style="color: white; text-decoration: none;">DNI</th>
                        <th style="color: white; text-decoration: none;">Nombre</th>
                        <th style="color: white; text-decoration: none;">Apellido</th>
                        <th style="color: white; text-decoration: none;">Fecha de Vencimiento</th>
                        <th style="color: white; text-decoration: none;">Monto</th>
                        <th style="color: white; text-decoration: none;">Días de mora</th>
                        <th style="color: white; text-decoration: none;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['deudores_morosos'] as $index => $moroso):
                        $estado = 'activo'; 
                    ?>
                        <tr class="fila-moroso" data-estado="<?= $estado ?>">
                            <td><?= htmlspecialchars($moroso['id_parcela']) ?></td>
                            <td><?= htmlspecialchars($moroso['dni']) ?></td>
                            <td><?= htmlspecialchars($moroso['nombre']) ?></td>
                            <td><?= htmlspecialchars($moroso['apellido']) ?></td>
                            <td class="text-danger fw-bold">
                                <?= date('d/m/Y', strtotime($moroso['fecha_vencimiento'])) ?>
                            </td>
                            <td>$<?= number_format($moroso['total'], 2) ?></td>
                            <td><?php $dias_mora = floor((time() - strtotime($moroso['fecha_vencimiento'])) / (60 * 60 * 24));
                                echo '<span class="badge bg-danger">' . $dias_mora . ' dia/s</span>'; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-toggle-estado <?= $estado == 'activo' ? 'btn-warning' : 'btn-success' ?>" 
                                        data-id="<?= $index ?>"
                                        data-estado-actual="<?= $estado ?>">
                                    <i class="fas fa-toggle-off"></i> Desactivar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="text-muted">No hay deudores morosos</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pestaña de Parcelas Vendidas -->
    <div class="tab-content mt-4">
        <!-- Pestaña para Padron de Difuntos -->
        <div class="tab-pane fade" id="vendidas" role="tabpanel">
            <!-- Seleccionar tipo de filtro de búsqueda -->
            <div class="mb-4">
                <label for="tipo_filtro" class="form-label">Seleccionar filtro de búsqueda:</label>
                <select id="tipo_filtro_parcelas" class="form-select w-auto" onchange="mostrarFiltroParcelas()">
                    <option value="">Seleccionar...</option>
                    <option value="filtro_fecha_parcelas">Por Fecha de Venta</option>
                    <option value="filtro_parcela_parcelas">Por Datos de Parcela</option>
                    <option value="lista_completa_parcelas">Ver lista completa</option>
                </select>
        </div>

        <!-- Filtro por Fecha -->
        <div id="filtro_fecha_parcelas" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_parcela" class="form-label">Desde</label>
                    <?php
                    if(isset($_GET['fecha_inicio_parcela'])){
                           $fecha_inicio_parcela = $_GET['fecha_inicio_parcela'];
                    }else{
                        $fecha_inicio_parcela = '';
                    }
                    ?>
                    <input type="date" class="form-control" name="fecha_inicio_parcela" value="<?= htmlspecialchars($fecha_inicio_parcela) ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_parcela" class="form-label">Hasta</label>
                    <?php
                    if(isset($_GET['fecha_fin_parcela'])){
                           $fecha_inicio_parcela = $_GET['fecha_fin_parcela'];
                    }else{
                        $fecha_fin_parcela = '';
                    }
                    ?>
                    <input type="date" class="form-control" name="fecha_fin_parcela" value="<?= htmlspecialchars($fecha_fin_parcela) ?>">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <!-- Filtro por Datos de Parcela -->
        <div id="filtro_parcela_parcelas" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="numero_ubicacion" class="form-label">Nº de Ubicación</label>
                    <input type="text" class="form-control" name="numero_ubicacion">
                </div>
                <div class="col-md-2">
                    <label for="id_tipo_parcela" class="form-label">Tipo</label>
                    <select name="id_tipo_parcela" class="form-select">
                        <option value="">Seleccionar...</option>
                        <option value="1">Nicho</option>
                        <option value="2">Fosa</option>
                        <option value="3">Panteón</option>
                        <option value="4">Osario</option>
                        <option value="5">Especial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="seccion" class="form-label">Sección</label>
                    <input type="text" class="form-control" name="seccion">
                </div>
                <div class="col-md-2">
                    <label for="fraccion" class="form-label">Fracción</label>
                    <input type="text" class="form-control" name="fraccion">
                </div>
                <div class="col-md-2">
                    <label for="nivel" class="form-label">Nivel</label>
                    <input type="text" class="form-control" name="nivel">
                </div>
                <div class="col-md-2">
                    <label for="id_orientacion" class="form-label">Orientación</label>
                    <select name="id_orientacion" class="form-select">
                        <option value="">Seleccionar...</option>
                        <option value="1">Norte</option>
                        <option value="2">Sur</option>
                        <option value="3">Este</option>
                        <option value="4">Oeste</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="hilera" class="form-label">Hilera</label>
                    <input type="text" class="form-control" name="hilera">
                </div>

                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <!-- Filtro por Titular -->
        <div id="filtro_titular_parcelas" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-2">        
                    <label for="letra_apellido_deudo" class="form-label">Apellido Titular (A-Z)</label>
                    <select name="letra_apellido_deudo" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach (range('A', 'Z') as $letra): ?>
                            <?php
                            $selected = '';
                            if(isset($datos['letra_apellido_deudo']) && $datos['letra_apellido_deudo'] === $letra){
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?= $letra ?>" <?= $selected ?>><?= $letra ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>


        <!-- Mostrar error solo si hay -->
        <?php if ($error): ?>
            <div class="alert alert-warning text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Tabla con datos de parcelas vendidas-->
        <?php if (!empty($datos['parcelas_vendidas'])): ?>
        <table class="table table-bordered table-striped">
            <thead class="th a">
                <tr>
                    <th style="color: white; text-decoration: none;">Parcela</th>
                    <th style="color: white; text-decoration: none;">Tipo de Parcela</th>
                    <th style="color: white; text-decoration: none;">Nombre del Titular</th>
                    <th style="color: white; text-decoration: none;">Apellido del titular</th>
                    <th style="color: white; text-decoration: none;">DNI</th>    
                    <th style="color: white; text-decoration: none;">Monto</th>                             
                    <th style="color: white; text-decoration: none;">Fecha de Venta</th>    
                    <th style="color: white; text-decoration: none;">Fecha de Vencimiento</th>  
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($datos['parcelas_vendidas'] as $venta): ?>
                        <?php                            
                            $tipos = [
                                1 => 'Nicho',
                                2 => 'Fosa',
                                3 => 'Panteón',
                                4 => 'Osario',
                                5 => 'Especial'
                            ];

                            $tipoParcela = $tipos[$venta['id_tipo_parcela']] ?? 'Desconocido';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id_parcela']) ?></td>
                            <td><?= htmlspecialchars($tipoParcela) ?></td>
                            <td><?= htmlspecialchars($venta['nombre']) ?></td>
                            <td><?= htmlspecialchars($venta['apellido']) ?></td>
                            <td><?= htmlspecialchars($venta['dni']) ?></td>
                            <td>$<?= number_format($venta['monto'], 2) ?></td>
                            <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($venta['fecha_vencimiento'])) ?></td>
                        </tr>
                    <?php endforeach; ?>

                
                </tbody>
            </table>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="text-muted">No hay parcelas vendidas</p>
            </div>
            <?php endif; ?>

        <?php if (!empty($datos['total_paginas']) && !empty($datos['pagina_actual']) && $datos['total_paginas'] > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                    <?php
                    // Determinar la clase
                    $class = 'page-item';
                    if ($i == $datos['pagina_actual']) {
                        $class .= ' active';
                    }

                    // Construir la URL de la página
                    $params = $_GET;
                    $params['pagina'] = $i;
                    $url = '?' . http_build_query($params);
                    ?>
                    <li class="<?= $class ?>">
                        <a class="page-link" href="<?= $url ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>
     
    <!-- Pestaña de traslados -->
    <div class="tab-pane fade" id="traslados" role="tabpanel">

        <!-- Seleccionar tipo de filtro de búsqueda -->
        <div class="mb-4">
            <label for="tipo_filtro_traslados" class="form-label">Seleccionar filtro de búsqueda:</label>
            <select id="tipo_filtro_traslados" class="form-select w-auto" onchange="mostrarFiltroTraslados()">
                <option value="">Seleccionar...</option>
                <option value="filtro_defuncion_traslados">Por Fecha de Defunción</option>
                <option value="filtro_traslado_traslados">Por Fecha de Traslado</option>
                <option value="lista_completa_traslados">Ver lista completa</option>
            </select>
        </div>

        <!-- Filtro por apellido de difunto -->
        <div id="filtro_alfabetico_traslados" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-2">                    
                    <label for="letra_apellido_traslado" class="form-label">Apellido (A-Z)</label>
                    <select name="letra_apellido_traslado" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach (range('A', 'Z') as $letra): ?>
                            <?php
                            $selected = '';
                            if (isset($datos['letra_apellido_traslado']) && $datos['letra_apellido_traslado'] === $letra){
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?= $letra ?>" <?= $selected ?>><?= $letra ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>



        <!-- Filtro por Fecha de Defunción -->
        <div id="filtro_defuncion_traslados" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_defuncion_traslado" class="form-label">Desde</label>
                    <input type="date" class="form-control" name="fecha_inicio_defuncion_traslado">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_defuncion_traslado" class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="fecha_fin_defuncion_traslado">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <!-- Filtro por Fecha de Traslado -->
        <div id="filtro_traslado_traslados" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_traslado" class="form-label">Desde</label>
                    <input type="date" class="form-control" name="fecha_inicio_traslado">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_traslado" class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="fecha_fin_traslado">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>


          <!-- Tabla -->                    
        <?php if (!empty($datos['difuntos_trasladados'])): ?>
            <table class="table table-bordered table-striped">
                <thead class="th a">
                    <tr>
                    <th style="color: white; text-decoration: none;">Nombre</th>
                    <th style="color: white; text-decoration: none;">Apellido</th>
                    <th style="color: white; text-decoration: none;">DNI</th>
                    <th style="color: white; text-decoration: none;">Fecha de defunción</th>
                    <th style="color: white; text-decoration: none;">Fecha de traslado</th>
                    <th style="color: white; text-decoration: none;">Parcela de orígen</th>
                    <th style="color: white; text-decoration: none;">Parcela de destino</th>
                    <th style="color: white; text-decoration: none;">Fecha de ingreso a nueva parcela</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['difuntos_trasladados'] as $difunto_trasladado): ?>
                        <tr>
                        <td><?= htmlspecialchars($difunto_trasladado['nombre']) ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['apellido']) ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['dni']) ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['fecha_fallecimiento']) ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['fecha_retiro']) ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['parcela_origen']?? '') ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['parcela_destino']?? 'No corresponde') ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['fecha_ingreso_destino']?? 'No corresponde') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="text-muted">No hay traslados registrados</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pestaña de estadisticas -->
    <div class="tab-pane fade" id="estadisticas" role="tabpanel">
        <div class="row">
            <!-- Registros Generales -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Registros Generales
                    </div>
                    <div class="card-body">
                       <?php
                        function obtenerValorSeguro($array, $clave, $valorPorDefecto = 0) {
                            if (isset($array[$clave])){
                                return $array[$clave];
                            }else{
                                return $valorPorDefecto;
                            }
                        }
                        ?>
                        <?php
                        $total_difuntos  = obtenerValorSeguro($datos, 'total_difuntos');
                        $total_parcelas  = obtenerValorSeguro($datos, 'total_parcelas');
                        $total_traslados = obtenerValorSeguro($datos, 'total_traslados');
                        ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Personas Fallecidas Registradas: <strong><?= $total_difuntos ?></strong></li>
                            <li class="list-group-item">Parcelas Ocupadas: <strong><?= $total_parcelas ?></strong></li>
                            <li class="list-group-item">Traslados: <strong><?= $total_traslados ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Defunciones Mensuales -->
            <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Defunciones Mensuales 
                            </div>
                            <?php
                            // Obtener fechas desde $_GET con seguridad
                            if (isset($_GET['fecha_inicio_mensual'])) {
                                $fecha_inicio_mensual = $_GET['fecha_inicio_mensual'];
                            } else {
                                $fecha_inicio_mensual = '';
                            }

                            if (isset($_GET['fecha_fin_mensual'])) {
                                $fecha_fin_mensual = $_GET['fecha_fin_mensual'];
                            } else {
                                $fecha_fin_mensual = '';
                            }

                            // Verificar si se debe mostrar el total
                            $mostrar_total = false;
                            if (isset($_GET['filtrar_defunciones']) && isset($datos['total_defunciones_mensuales'])) {
                                $mostrar_total = true;
                            }
                            ?>

                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-5">
                                        <label for="fecha_inicio_mensual" class="form-label">Desde</label>
                                        <input type="date" class="form-control" name="fecha_inicio_mensual" value="<?= htmlspecialchars($fecha_inicio_mensual) ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="fecha_fin_mensual" class="form-label">Hasta</label>
                                        <input type="date" class="form-control" name="fecha_fin_mensual" value="<?= htmlspecialchars($fecha_fin_mensual) ?>">
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <button type="submit" name="filtrar_defunciones" class="btn btn-success">Filtrar</button>
                                    </div>
                                </form>

                                <?php if ($mostrar_total): ?>
                                    <hr>
                                    <p class="text-center fs-5">
                                        Total de defunciones en el rango seleccionado: 
                                        <strong><?= htmlspecialchars($datos['total_defunciones_mensuales']) ?></strong>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <script>
        
    document.addEventListener('DOMContentLoaded', inicializarTablas);

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', inicializarTablas);
    });

    function inicializarTablas() {
    document.querySelectorAll('table').forEach(tabla => {
        if (!$.fn.DataTable.isDataTable(tabla)) {
            $(tabla).DataTable({
                retrieve: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'csv', text: 'CSV', className: 'btn btn-primary btn-sm', bom: true, charset: 'UTF-8', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: ':not(:last-child)' } }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 8,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [],
               
            });
        }
    });
    }

   

    document.addEventListener('DOMContentLoaded', function() {
        // Restaurar el tab activo guardado
        const lastTab = localStorage.getItem('activeTab');
        if (lastTab) {
            const tabElement = document.querySelector(`[data-bs-target="${lastTab}"]`);
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        }

        // Escuchar cambio de pestaña y guardarlo
        const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabLinks.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const activeTab = e.target.getAttribute('data-bs-target');
                localStorage.setItem('activeTab', activeTab);
            });
        });
    });

    const botonesEstado = document.querySelectorAll('.btn-toggle-estado');
    botonesEstado.forEach(boton => {
        boton.addEventListener('click', function() {
            const idDeuda = this.getAttribute('data-id');
            const estadoActual = this.getAttribute('data-estado-actual');
            const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';

            console.log('Cambiando estado de deuda ' + idDeuda + ' de ' + estadoActual + ' a ' + nuevoEstado);

            // Si se elige "lista_completa", recarga sin parámetros
            if (seleccion === 'lista_completa') {
                window.location.href = window.location.pathname + '?tab=tablas';
                return;
            }

            if (!seleccion) return; // No mostrar nada si no hay selección
            
            setTimeout(() => {
                this.setAttribute('data-estado-actual', nuevoEstado);

                const fila = this.closest('.fila-moroso');

                if (nuevoEstado === 'activo') {
                    this.classList.remove('btn-success');
                    this.classList.add('btn-warning');
                    this.innerHTML = '<i class="fas fa-toggle-off"></i> Desactivar';

                    const badge = fila.querySelector('.estado-badge');
                    if (badge) {
                        badge.classList.remove('bg-secondary');
                        badge.classList.add('bg-success');
                        badge.textContent = 'Activo';
                    }

                    fila.setAttribute('data-estado', 'activo');

                    if (document.getElementById('ver-activos').classList.contains('active')) {
                        fila.style.display = '';
                    }

                } else {
                    this.classList.remove('btn-warning');
                    this.classList.add('btn-success');
                    this.innerHTML = '<i class="fas fa-toggle-off"></i> Activar';

                    const badge = fila.querySelector('.estado-badge');
                    if (badge) {
                        badge.classList.remove('bg-success');
                        badge.classList.add('bg-secondary');
                        badge.textContent = 'Inactivo';
                    }

                    fila.setAttribute('data-estado', 'inactivo');

                    if (document.getElementById('ver-activos').classList.contains('active')) {
                        fila.style.display = 'none';
                    }
                }

                alert("Deuda " + (nuevoEstado === 'activo' ? 'activada' : 'desactivada') + " correctamente");
            }, 300);
        });
    });

    document.getElementById('ver-activos').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('ver-inactivos').classList.remove('active');

        document.querySelectorAll('.fila-moroso').forEach(fila => {
            if (fila.getAttribute('data-estado') === 'activo') {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });

    document.getElementById('ver-inactivos').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('ver-activos').classList.remove('active');

        document.querySelectorAll('.fila-moroso').forEach(fila => {
            if (fila.getAttribute('data-estado') === 'inactivo') {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });

    

    function mostrarFiltroDifuntos() {
        const seleccion = document.getElementById('tipo_filtro').value;
        const filtros = document.querySelectorAll('#tablas .filtro-box');

        filtros.forEach(f => f.style.display = 'none');

        if (seleccion === 'lista_completa_difuntos') {
            window.location.href = window.location.pathname + '?tab=tablas';
            return;
        }

        if (!seleccion) return;

        const filtroSeleccionado = document.getElementById(seleccion);
        if (filtroSeleccionado) {
            filtroSeleccionado.style.display = 'block';
        }
    }

    function mostrarFiltroTraslados() {
        const seleccion = document.getElementById('tipo_filtro_traslados').value;
        const filtros = document.querySelectorAll('#traslados .filtro-box');

        filtros.forEach(f => f.style.display = 'none');

        if (seleccion === 'lista_completa_traslados') {
            window.location.href = window.location.pathname + '?tipo_filtro_traslados=lista_completa_traslados&tab=traslados';
            return;
        }

        if (!seleccion) return;

        const filtroSeleccionado = document.getElementById(seleccion);
        if (filtroSeleccionado) {
            filtroSeleccionado.style.display = 'block';
        }
    }

    function mostrarFiltroParcelas() {
        const seleccion = document.getElementById('tipo_filtro_parcelas').value;
        const filtros = document.querySelectorAll('#vendidas .filtro-box');

        filtros.forEach(f => f.style.display = 'none');

        if (seleccion === 'lista_completa_parcelas') {
            window.location.href = window.location.pathname + '?tipo_filtro_parcelas=lista_completa_parcelas&tab=vendidas';
            return;
        }

        if (!seleccion) return;

        const filtroSeleccionado = document.getElementById(seleccion);
        if (filtroSeleccionado) {
            filtroSeleccionado.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        mostrarFiltroDifuntos(); 
        mostrarFiltroParcelas();
        mostrarFiltroTraslados();
    });
</script>