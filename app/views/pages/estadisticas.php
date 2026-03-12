<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link" id="integral-tab" data-bs-toggle="tab" data-bs-target="#reporte_integral" type="button" role="tab">Reporte General de Pagos</button>
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
    $config = $datos['configIntegral'];
    include 'partials/tabla_ajax_template.php';
    
    $config = $datos['configTraslados'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configMorosos'];
    include 'partials/tabla_ajax_template.php';
    ?>

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
            'integral': [
                { 
                    data: 'id_pago',
                    title: '# ID',
                    render: (data, type, row) => {
                        const esActual = (row.id_pago == row.ultimo_pago_id);
                        return esActual 
                            ? `<span class="badge bg-success" title="Último pago">#${data} - ACTUAL</span>`
                            : `<span class="text-muted small">#${data} (Histórico)</span>`;
                    }
                },
                { 
                    data: null,
                    title: 'Sujetos (Deudo / Difunto)',
                    render: function(data, type, row) {
                        const difunto = row.difunto_nombre 
                            ? `<small class="text-muted"><i class="bi bi-person"></i> Dif: ${row.difunto_apellido}, ${row.difunto_nombre}</small>`
                            : '<small class="text-muted italic text-opacity-50">Sin difunto asociado</small>';
                        
                        return `<div>
                                    <div class="fw-bold text-primary">${row.deudo_apellido}, ${row.deudo_nombre}</div>
                                    ${difunto}
                                </div>`;
                    }
                },
                { 
                    data: null,
                    title: 'Ubicación / Parcela',
                    render: function(data, type, row) {
                        return `<div class="lh-sm">
                                    <span class="badge border text-dark bg-light mb-1">${row.tipo_nombre || 'S/T'}</span><br>
                                    <small class="text-secondary">ID interno: ${row.id_parcela}</small><br>
                                    <small class="text-muted">Seccion: ${row.seccion || '-'} Hilera: ${row.hilera || '-'} Nivel: ${row.numero_ubicacion || '-'}</small>
                                </div>`;
                    }
                },
                { 
                    data: 'total',
                    title: 'Monto y Pago',
                    render: (data, type, row) => {
                        const fecha = (!row.fecha_pago || row.fecha_pago === '0000-00-00 00:00:00') 
                            ? 'N/A' 
                            : new Date(row.fecha_pago).toLocaleDateString('es-AR');
                        
                        return `<div class="text-end">
                                    <span class="fw-bold text-success d-block">$${parseFloat(data).toFixed(2)}</span>
                                    <small class="text-muted" title="Fecha de pago">${fecha}</small>
                                </div>`;
                    }
                },
                { 
                    data: 'fecha_vencimiento',
                    title: 'Estado de Deuda',
                    render: (data, type, row) => {
                        if (!data || data === '0000-00-00') return '<span class="text-muted">N/A</span>';
                        
                        const fechaVenc = new Date(data);
                        const hoy = new Date();
                        const esActual = (row.id_pago == row.ultimo_pago_id);
                        const estaVencido = (fechaVenc < hoy);
                        const fechaFormateada = fechaVenc.toLocaleDateString('es-AR');

                        if (esActual && estaVencido) {
                            return `<div class="text-center">
                                        <span class="badge bg-danger mb-1">VENCIDO</span><br>
                                        <span class="text-danger fw-bold small">${fechaFormateada}</span>
                                    </div>`;
                        }

                        return `<div class="text-center">
                                    <span class="text-muted small">${fechaFormateada}</span>
                                </div>`;
                    }
                },
                {
                    data: 'archivo_pago',
                    title: 'Archivo',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data) {
                            return `<a href="<?= URL ?>/public/uploads/${data}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark-pdf"></i> Ver Archivo
                                    </a>`;
                        }
                        return '<span class="text-muted">No disponible</span>';
                    }
                }
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
            'morosos': [
                { data: 'id_parcela' }, 
                { data: 'dni' },        
                { data: 'nombre' },     
                { data: 'apellido' },   
                { 
                    data: 'fecha_vencimiento', 
                    render: function(data) {
                        if (!data) return '';
                        return `<span class="text-danger fw-bold">${new Date(data).toLocaleDateString('es-AR')}</span>`;
                    }
                }, 
                { 
                    data: 'total', 
                    render: data => `$${parseFloat(data).toFixed(2)}`
                },
                { 
                    data: 'fecha_vencimiento', 
                    orderable: false,
                    render: function(data) {
                        const diff = Math.ceil((new Date() - new Date(data)) / (1000 * 60 * 60 * 24));
                        return `<span class="badge bg-danger">${diff > 0 ? diff : 0} día/s</span>`;
                    }
                },
                {
                    data: null, 
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button type="button" class="btn btn-sm btn-success registrar-pago-btn" 
                                    data-bs-toggle="modal" data-bs-target="#pagoModal"
                                    data-deudor-id="${row.id_deudo}" data-parcela-id="${row.id_parcela}"
                                    data-deudor-nombre="${row.apellido}, ${row.nombre}"
                                    data-vencimiento-anterior="${row.fecha_vencimiento}">
                                    <i class="bi bi-cash-coin"></i> Registrar
                                </button>`;
                    }
                }
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
                        const modalEl = document.getElementById('pagoModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Tab(modalEl);
                        modalInstance.hide();

                        const table = $('#morosos .datatable-ajax').DataTable();
                        if (lastClickedButton) {
                            const rowToRemove = $(lastClickedButton).closest('tr');
                            table.row(rowToRemove).remove().draw(false);
                        }

                        const badge = $('#morosos-tab .badge');
                        if (badge.length) {
                            const info = table.page.info();
                            const nuevoTotal = info.recordsDisplay; 

                            if (nuevoTotal > 0) {
                                badge.text(nuevoTotal);
                            } else {
                                badge.remove();
                            }
                        }

                        alert('Pago registrado correctamente.');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Ocurrió un error al procesar el pago. Intente nuevamente.');
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