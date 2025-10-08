<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="difuntos-tab" data-bs-toggle="tab" data-bs-target="#difuntos" type="button" role="tab">Padrón difuntos</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="morosos-tab" data-bs-toggle="tab" data-bs-target="#morosos" type="button" role="tab">Deudores Morosos
            <?php if (!empty($datos['total_morosos']) && $datos['total_morosos'] > 0): ?>
                <span class="badge bg-danger ms-1"><?= $datos['total_morosos'] ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="traslados-tab" data-bs-toggle="tab" data-bs-target="#traslados" type="button" role="tab">Traslados</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="vendidas-tab" data-bs-toggle="tab" data-bs-target="#vendidas" type="button" role="tab">Parcelas Vendidas</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button" role="tab">Estadísticas</button>
    </li>
</ul>

<div class="tab-content mt-4">
    <?php
    $config = $datos['configDifuntos'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configTraslados'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configVendidas'];
    include 'partials/tabla_ajax_template.php';
    ?>

    <div class="tab-pane fade" id="morosos" role="tabpanel">
        <?php if (!empty($datos['deudores_morosos'])): ?>
            <table class="table table-bordered table-striped" id="tabla-morosos">
                <thead class="th-custom">
                    <tr>
                        <th>Parcela</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Vencimiento</th>
                        <th>Monto</th>
                        <th>Días de mora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['deudores_morosos'] as $index => $moroso): ?>
                        <tr>
                            <td><?= htmlspecialchars($moroso['id_parcela']) ?></td>
                            <td><?= htmlspecialchars($moroso['dni']) ?></td>
                            <td><?= htmlspecialchars($moroso['nombre']) ?></td>
                            <td><?= htmlspecialchars($moroso['apellido']) ?></td>
                            <td class="text-danger fw-bold"><?= date('d/m/Y', strtotime($moroso['fecha_vencimiento'])) ?></td>
                            <td>$<?= number_format($moroso['total'], 2) ?></td>
                            <td>
                                <?php $dias_mora = floor((time() - strtotime($moroso['fecha_vencimiento'])) / (60 * 60 * 24));
                                echo '<span class="badge bg-danger">' . $dias_mora . ' dia/s</span>'; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning btn-toggle-estado" data-id="<?= $index ?>"
                                    data-estado-actual="activo">
                                    <i class="fas fa-toggle-off"></i> Pagar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted text-center">No hay deudores morosos.</p>
        <?php endif; ?>
    </div>

    <div class="tab-pane fade" id="estadisticas" role="tabpanel">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">Registros Generales</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Personas Fallecidas:
                                <strong><?= $datos['total_difuntos'] ?? 0 ?></strong></li>
                            <li class="list-group-item">Parcelas Ocupadas:
                                <strong><?= $datos['total_parcelas'] ?? 0 ?></strong></li>
                            <li class="list-group-item">Traslados Registrados:
                                <strong><?= $datos['total_traslados'] ?? 0 ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataTablesConfigs = {
            'difuntos': [
                { data: 'fecha_fallecimiento' }, { data: 'nombre' }, { data: 'apellido' },
                { data: 'edad' }, { data: 'dni' }, { data: 'nombre_deudo' },
                { data: 'estado_civil' }, { data: 'nacionalidad' }, { data: 'sexo' },
                { data: 'domicilio' }, { data: 'localidad' }, { data: 'codigo_postal' }
            ],
            'traslados': [
                { data: 'nombre' }, { data: 'apellido' }, { data: 'dni' },
                { data: 'fecha_fallecimiento' }, { data: 'fecha_retiro' }, { data: 'parcela_origen' },
                { data: 'parcela_destino' }, { data: 'fecha_ingreso_destino' }
            ],
            'vendidas': [
                { data: 'id_parcela' }, { data: 'tipo_parcela' }, { data: 'nombre_titular' },
                { data: 'apellido_titular' }, { data: 'dni' }, { data: 'monto' },
                { data: 'fecha_venta' }, { data: 'fecha_vencimiento' }
            ]
            // Agrega aquí la configuración para otras tablas...
        };

        function inicializarDataTable(tabla) {
            if ($.fn.DataTable.isDataTable(tabla)) {
                return;
            }

            const tablaJQ = $(tabla);
            const ajaxUrl = tablaJQ.data('ajax-url');
            const configKey = tablaJQ.data('config-key');
            const filterIds = tablaJQ.data('filter-ids').split(',');

            if (!ajaxUrl || !dataTablesConfigs[configKey]) {
                console.error("Falta configuración para la tabla: ", tabla.id);
                return;
            }

            const dt = tablaJQ.DataTable({
                retrieve: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'POST',
                    data: function(d) {
                        d.fecha_inicio = $(filterIds[0].trim()).val();
                        d.fecha_fin = $(filterIds[1].trim()).val();
                    }
                },
                columns: dataTablesConfigs[configKey]
            });

            $(filterIds.join(', ')).on('change', function() {
                dt.ajax.reload();
            });
        }

        const tablaActiva = document.querySelector('.tab-pane.active .datatable-ajax');
        if (tablaActiva) {
            inicializarDataTable(tablaActiva);
        }

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', event => {
                const panelId = event.target.getAttribute('data-bs-target');
                const tablaEnPanel = document.querySelector(panelId + ' .datatable-ajax');
                if (tablaEnPanel) {
                    inicializarDataTable(tablaEnPanel);
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const lastTab = localStorage.getItem('activeTab');
        if (lastTab) {
            const tabElement = document.querySelector(`[data-bs-target="${lastTab}"]`);
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        }

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
</script>