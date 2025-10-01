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
                    <option value="lista_completa_difuntos">Padrón general de Difuntos</option>
                    <option value="filtro_titular_difuntos">Por Orden Alfabético</option>
                    <option value="filtro_fecha_difuntos">Por Fecha de Defunción</option>
                </select>
        </div>

        <!-- Filtro por Fecha -->
        <div id="filtro_fecha_difuntos" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_defuncion" class="form-label">Desde</label>
                    <input type="date" class="form-control" name="fecha_inicio_defuncion" value="<?= htmlspecialchars($_GET['fecha_inicio_defuncion'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_defuncion" class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="fecha_fin_defuncion" value="<?= htmlspecialchars($_GET['fecha_fin_defuncion'] ?? '') ?>">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <!-- Filtro por Apellido de Difunto -->
        <div id="filtro_titular_difuntos" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-2">                    
                    <label for="letra_apellido_difunto" class="form-label">Apellido (A-Z)</label>
                    <select name="letra_apellido_difunto" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach (range('A', 'Z') as $letra): ?>
                            <option value="<?= $letra ?>" <?= (isset($datos['letra_apellido_difunto']) && $datos['letra_apellido_difunto'] === $letra) ? 'selected' : '' ?>><?= $letra ?></option>
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

        <table class="table table-bordered table-striped">
            <thead class="th a">
                <tr>
                    <th><?= generarOrdenLink('fecha_fallecimiento', 'Fecha de Fallecimiento', $datos) ?></th>
                        <th><?= generarOrdenLink('nombre', 'Nombre', $datos) ?></th>
                        <th><?= generarOrdenLink('apellido', 'Apellido', $datos) ?></th>
                        <th><?= generarOrdenLink('edad', 'Edad', $datos) ?></th>
                        <th><?= generarOrdenLink('dni', 'Dni', $datos) ?></th>
                        <th><?= generarOrdenLink('deudo', 'Deudo', $datos) ?></th> 
                        <th><?= generarOrdenLink('estado_civil', 'Estado Civil', $datos) ?></th>
                        <th><?= generarOrdenLink('nacionalidad', 'Nacionalidad', $datos) ?></th>  
                        <th><?= generarOrdenLink('sexo', 'Sexo', $datos) ?></th>
                        <th><?= generarOrdenLink('domicilio', 'Domicilio', $datos) ?></th>
                        <th><?= generarOrdenLink('localidad', 'Localidad', $datos) ?></th>
                        <th><?= generarOrdenLink('codigo_postal', 'Código Postal', $datos) ?></th>  
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos['movimientos'])): ?>
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
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($datos['total_paginas']) && ($datos['total_paginas']) > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                    <li class="page-item <?= ($i == $datos['pagina_actual']) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
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
                        <th><?= generarOrdenLink('Parcela', 'Parcela', $datos) ?></th>
                        <th><?= generarOrdenLink('DNI', 'DNI', $datos) ?></th>
                        <th><?= generarOrdenLink('Nombre', 'Nombre', $datos) ?></th>
                        <th><?= generarOrdenLink('Apellido', 'Apellido', $datos) ?></th>
                        <th><?= generarOrdenLink('Fecha de vencimiento', 'Fecha vencimiento', $datos) ?></th>
                        <th><?= generarOrdenLink('Monto', 'Total', $datos) ?></th>
                        <th><?= generarOrdenLink('Dias de Mora', 'Dia/s de mora', $datos) ?></th>
                        <th><?= generarOrdenLink('Acciones', 'Acciones', $datos)?></th>
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
                    <option value="lista_completa_parcelas">Lista de Parcelas</option>
                    <option value="filtro_fecha_parcelas">Por Fecha de Venta</option>
                    <option value="filtro_parcela_parcelas">Por Datos de Parcela</option>
                    <option value="filtro_titular_parcelas">Por Titular</option>
                </select>
        </div>

        <!-- Filtro por Fecha -->
        <div id="filtro_fecha_parcelas" class="filtro-box mb-4" style="display: none;">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio_parcela" class="form-label">Desde</label>
                    <input type="date" class="form-control" name="fecha_inicio_parcela" value="<?= htmlspecialchars($_GET['fecha_inicio_parcela'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin_parcela" class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="fecha_fin_parcela" value="<?= htmlspecialchars($_GET['fecha_fin_parcela'] ?? '') ?>">
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
                            <option value="<?= $letra ?>" <?= (isset($datos['letra_apellido_deudo']) && $datos['letra_apellido_deudo'] === $letra) ? 'selected' : '' ?>><?= $letra ?></option>
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
        <table class="table table-bordered table-striped">
            <thead class="th a">
                <tr>
                    <th><?= generarOrdenLink('id_parcela', 'Parcela', $datos) ?></th>
                    <th><?= generarOrdenLink('id_tipo_parcela', ' Tipo de Parcela', $datos) ?></th>
                    <th><?= generarOrdenLink('nombre', 'Nombre Titular', $datos) ?></th>
                    <th><?= generarOrdenLink('apellido', 'Apellido Titular', $datos) ?></th>
                    <th><?= generarOrdenLink('dni', 'DNI', $datos) ?></th>    
                    <th><?= generarOrdenLink('monto', 'Monto', $datos) ?></th>                             
                    <th><?= generarOrdenLink('fecha_venta', 'Fecha de Venta', $datos) ?></th>    
                    <th><?= generarOrdenLink('fecha_vencimiento', 'Fecha de Vencimiento', $datos) ?></th>  
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos['parcelas_vendidas'])): ?>
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

                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No se encontraron resultados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($datos['total_paginas']) && !empty($datos['pagina_actual']) && $datos['total_paginas'] > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                    <li class="page-item <?= ($i == $datos['pagina_actual']) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
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
                <option value="lista_completa_traslados">Lista de Traslados</option>
                <option value="filtro_alfabetico_traslados">Por Orden Alfabético</option>
                <option value="filtro_defuncion_traslados">Por Fecha de Defunción</option>
                <option value="filtro_traslado_traslados">Por Fecha de Traslado</option>
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
                            <option value="<?= $letra ?>" <?= (isset($datos['letra_apellido_traslado']) && $datos['letra_apellido_traslado'] === $letra) ? 'selected' : '' ?>><?= $letra ?></option>
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
                    <th><?= generarOrdenLink('nombre', 'Nombre', $datos) ?></th>
                    <th><?= generarOrdenLink('apellido', 'Apellido', $datos) ?></th>
                    <th><?= generarOrdenLink('dni', 'DNI', $datos) ?></th>
                    <th><?= generarOrdenLink('fecha_fallecimiento', 'Fecha de defunción', $datos) ?></th>
                    <th><?= generarOrdenLink('fecha_retiro', 'Fecha de traslado', $datos) ?></th>
                    <th><?= generarOrdenLink('parcela_origen', 'Parcela de Origen', $datos) ?></th>
                    <th><?= generarOrdenLink('parcela_destino', 'Parcela Destino', $datos) ?></th>
                    <th><?= generarOrdenLink('fecha_ingreso_destino', 'Fecha de ingreso a nueva parcela', $datos) ?></th>
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
                            <td><?= htmlspecialchars($difunto_trasladado['parcela_destino']?? '') ?></td>
                            <td><?= htmlspecialchars($difunto_trasladado['fecha_ingreso_destino']?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p class="text-muted">No hay difuntos trasladados</p>
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
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Personas Fallecidas Registradas: <strong><?= $datos['total_difuntos'] ?? 0 ?></strong></li>
                            <li class="list-group-item">Parcelas Ocupadas: <strong><?= $datos['total_parcelas'] ?? 0 ?></strong></li>
                            <li class="list-group-item">Traslados: <strong><?= $datos['total_traslados'] ?? 0 ?></strong></li>
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
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-5">
                                        <label for="fecha_inicio_mensual" class="form-label">Desde</label>
                                        <input type="date" class="form-control" name="fecha_inicio_mensual" value="<?= htmlspecialchars($_GET['fecha_inicio_mensual'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="fecha_fin_mensual" class="form-label">Hasta</label>
                                        <input type="date" class="form-control" name="fecha_fin_mensual" value="<?= htmlspecialchars($_GET['fecha_fin_mensual'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <button type="submit" name="filtrar_defunciones" class="btn btn-success">Filtrar</button>
                                    </div>
                                </form>

                                <?php if (isset($_GET['filtrar_defunciones']) && isset($datos['total_defunciones_mensuales'])): ?>
                                    <hr>
                                    <p class="text-center fs-5">
                                        Total de defunciones en el rango seleccionado: 
                                        <strong><?= $datos['total_defunciones_mensuales'] ?></strong>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<?php
// Función para generar links con ordenamiento (orden asc/desc)
    function generarOrdenLink($columna, $etiqueta, $datos) {
        $direccion_actual = 'asc';
        if (!empty($datos['sort_dir'])) {
            $direccion_actual = strtolower($datos['sort_dir']);
        }

        $columna_actual = '';
        if (!empty($datos['sort_col'])) {
            $columna_actual = strtolower($datos['sort_col']);
        }

        // Cambia la dirección si la columna es la misma, sino por defecto asc
        $direccion_siguiente = 'asc';
        if ($columna_actual === $columna && $direccion_actual === 'asc') {
            $direccion_siguiente = 'desc';
        }

        $query_params = $_GET;
        $query_params['sort_col'] = $columna;
        $query_params['sort_dir'] = $direccion_siguiente;

        $link = '?' . http_build_query($query_params);

        $flecha = '';
        if ($columna_actual === $columna) {
            $flecha = strtoupper($direccion_actual) === 'ASC' ? ' ▲' : ' ▼';
        }

        return "<a href=\"$link\" style=\"color: white; text-decoration: none;\">$etiqueta$flecha</a>";
    }
    ?>

    <script>
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