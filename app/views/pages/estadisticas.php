<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link" id="vendidas-tab" data-bs-toggle="tab" data-bs-target="#vendidas" type="button" role="tab">Parcelas Vendidas</button>
    </li>
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
        <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button" role="tab">Estadísticas</button>
    </li>
</ul>

<div class="tab-content mt-4">
    <?php
    $config = $datos['configVendidas'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configDifuntos'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configTraslados'];
    include 'partials/tabla_ajax_template.php';
    ?>

    <div class="tab-pane fade" id="morosos" role="tabpanel">
        <?php if (!empty($datos['deudores_morosos'])): ?>
            <table class="table table-striped" id="tabla-morosos">
                <thead class="table-light">
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
                    <?php foreach ($datos['deudores_morosos'] as $index => $moroso) : ?>
                        <tr class="fila-moroso" data-estado="activo">
                            <td>
                                <?php
                                    if (isset($moroso['id_parcela'])) {
                                        echo htmlspecialchars($moroso['id_parcela']);
                                    } else {
                                        echo '';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (isset($moroso['dni'])) {
                                        echo htmlspecialchars($moroso['dni']);
                                    } else {
                                        echo '';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (isset($moroso['nombre'])) {
                                        echo htmlspecialchars($moroso['nombre']);
                                    } else {
                                        echo '';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (isset($moroso['apellido'])) {
                                        echo htmlspecialchars($moroso['apellido']);
                                    } else {
                                        echo '';
                                    }
                                ?>
                            </td>
                            
                            <td class="text-danger fw-bold">
                                <?= date('d/m/Y', strtotime($moroso['fecha_vencimiento'])) ?>
                            </td>

                            <td>
                                $<?= number_format($moroso['total'], 2) ?>
                            </td>

                            <td>
                                <?php 
                                    $fechaVencimiento = new DateTime($moroso['fecha_vencimiento']);
                                    $hoy = new DateTime();
                                    $dias_mora = $hoy->diff($fechaVencimiento)->days;
                                    echo '<span class="badge bg-danger">' . $dias_mora . ' día/s</span>'; 
                                ?>
                            </td>
                            
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success registrar-pago-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#pagoModal"
                                        data-deudor-id="<?= $moroso['id_deudo'] ?>"
                                        data-parcela-id="<?= $moroso['id_parcela'] ?>"
                                        data-deudor-nombre="<?php
                                            $nombre_completo = ''; 
                                            
                                            if (isset($moroso['apellido'])) {
                                                $nombre_completo .= $moroso['apellido'];
                                            }

                                            $nombre_completo .= ', ';

                                            if (isset($moroso['nombre'])) {
                                                $nombre_completo .= $moroso['nombre'];
                                            }

                                            echo htmlspecialchars($nombre_completo);
                                        ?>"
                                        data-vencimiento-anterior="<?= $moroso['fecha_vencimiento'] ?>">
                                    <i class="bi bi-cash-coin"></i> Registrar Pago
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
                                <strong>
                                    <?php
                                        if (isset($datos['total_difuntos'])) {
                                            echo $datos['total_difuntos'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                            <li class="list-group-item">Parcelas Ocupadas:
                                <strong>
                                    <?php
                                        if (isset($datos['total_parcelas'])) {
                                            echo $datos['total_parcelas'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                            <li class="list-group-item">Traslados Registrados:
                                <strong>
                                    <?php
                                        if (isset($datos['total_traslados'])) {
                                            echo $datos['total_traslados'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModalLabel">Registrar Pago para...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= URL ?>/pago/registrarPagoMantenimiento" method="POST">
                <div class="modal-body">
                    <p>Deudor: <strong id="modalDeudorNombre"></strong></p>
                    <p>Parcela: <strong id="modalParcelaId"></strong></p>
                    
                    <input type="hidden" name="deudo_id" id="modalDeudoId">
                    <input type="hidden" name="parcela_id" id="modalParcelaIdInput">

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto a Pagar</label>
                        <input type="number" step="0.01" class="form-control" name="monto" id="monto" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha del Pago</label>
                        <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Nuevo Vencimiento</label>
                        <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let lastClickedButton;

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
                { data: 'fecha_fallecimiento' }, { data: 'fecha_retiro' },
                { 
                    data: null, orderable: false,
                    render: function(data, type, row) {
                        const origen = row.parcela_origen;
                        const destino = row.parcela_destino || 'Externo';
                        return `<strong>${origen}</strong> → <strong>${destino}</strong>`;
                    }
                }
            ],
            'vendidas': [
                { data: 'id_parcela' }, { data: 'tipo_parcela' }, { data: 'nombre_titular' },
                { data: 'apellido_titular' }, { data: 'dni' }, { data: 'monto' },
                { data: 'fecha_venta' }, { data: 'fecha_vencimiento' }
            ]
        };

        function inicializarDataTableAjax(tabla) {
            if (!tabla || $.fn.DataTable.isDataTable(tabla)) return;
            
            const tablaJQ = $(tabla);
            const ajaxUrl = tablaJQ.data('ajax-url');
            const configKey = tablaJQ.data('config-key');
            const filterIds = tablaJQ.data('filter-ids').split(',');

            if (!ajaxUrl || !dataTablesConfigs[configKey]) return;

            const dt = tablaJQ.DataTable({
                retrieve: true, dom: 'Bfrtip', buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                pageLength: 10, processing: true, serverSide: true,
                ajax: {
                    url: ajaxUrl, type: 'POST',
                    data: function(d) {
                        d.fecha_inicio = $(filterIds[0].trim()).val();
                        d.fecha_fin = $(filterIds[1].trim()).val();
                    }
                },
                columns: dataTablesConfigs[configKey]
            });
            $(filterIds.join(', ')).on('change', () => dt.ajax.reload());
        }

        $('#tabla-morosos').DataTable({ dom: 'Bfrtip', buttons: ['copy', 'csv', 'excel', 'pdf', 'print'], language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }, pageLength: 8 });

        const pagoModal = document.getElementById('pagoModal');
        if (pagoModal) {
            pagoModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                lastClickedButton = button;

                const deudorId = button.getAttribute('data-deudor-id');
                const parcelaId = button.getAttribute('data-parcela-id');
                const deudorNombre = button.getAttribute('data-deudor-nombre');
                const vencimientoAnterior = button.getAttribute('data-vencimiento-anterior');

                let nuevoVencimiento = '';
                if (vencimientoAnterior) {
                    const fechaAnterior = new Date(vencimientoAnterior);
                    fechaAnterior.setFullYear(fechaAnterior.getFullYear() + 1);
                    nuevoVencimiento = fechaAnterior.toISOString().split('T')[0];
                }
                
                pagoModal.querySelector('#modalDeudorNombre').textContent = deudorNombre;
                pagoModal.querySelector('#modalParcelaId').textContent = parcelaId;
                pagoModal.querySelector('#modalDeudoId').value = deudorId;
                pagoModal.querySelector('#modalParcelaIdInput').value = parcelaId;
                pagoModal.querySelector('#fecha_vencimiento').value = nuevoVencimiento;
            });
        }

        $('#pagoModal form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#pagoModal').modal('hide');

                        const table = $('#tabla-morosos').DataTable();
                        const rowToRemove = $(lastClickedButton).closest('tr');

                        table.row(rowToRemove).remove().draw();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Ocurrio un error al procesar el pago. Intente nuevamente.');
                }
            });
        });

        const tablaDifuntos = document.getElementById('tabla-difuntos');
        if (tablaDifuntos) {
            inicializarDataTableAjax(tablaDifuntos);
        }

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', event => {
                localStorage.setItem('activeTab', event.target.getAttribute('data-bs-target'));
                
                const panelId = event.target.getAttribute('data-bs-target');
                const tablaEnPanel = document.querySelector(panelId + ' .datatable-ajax');
                if (tablaEnPanel) {
                    inicializarDataTableAjax(tablaEnPanel);
                }
            });
        });

        const lastTab = localStorage.getItem('activeTab');
        if (lastTab) {
            const tabElement = document.querySelector(`[data-bs-target="${lastTab}"]`);
            if (tabElement && lastTab !== '#difuntos') {
                new bootstrap.Tab(tabElement).show();
            }
        }
    });
</script>