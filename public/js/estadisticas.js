let lastClickedButton;

function escapeHTML(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.addEventListener('DOMContentLoaded', function () {
    const dataTablesConfigs = {
        'integral': [
            {
                data: 'id_pago',
                title: '# ID',
                render: (data, type, row) => {
                    const esActual = (row.id_pago == row.ultimo_pago_id);
                    return esActual
                        ? `<span class="badge bg-success" title="Último pago">#${escapeHTML(data)} - ACTUAL</span>`
                        : `<span class="text-muted small">#${escapeHTML(data)} (Histórico)</span>`;
                }
            },
            {
                data: null,
                title: 'Sujetos (Deudo / Difunto)',
                render: function (data, type, row) {
                    const difunto = row.difunto_nombre
                        ? `<small class="text-muted"><i class="bi bi-person"></i> Dif: ${escapeHTML(row.difunto_apellido)}, ${escapeHTML(row.difunto_nombre)}</small>`
                        : '<small class="text-muted italic text-opacity-50">Sin difunto asociado</small>';

                    return `<div>
                                <div class="fw-bold text-primary">${escapeHTML(row.deudo_apellido || '')}, ${escapeHTML(row.deudo_nombre || '')}</div>
                                ${difunto}
                            </div>`;
                }
            },
            {
                data: null,
                title: 'Ubicación / Parcela',
                render: function (data, type, row) {
                    return `<div class="lh-sm">
                                <span class="badge border text-dark bg-light mb-1">${escapeHTML(row.tipo_nombre || 'S/T')} </span><br>
                                <span class="badge border text-dark bg-light mb-1">ID INTERNO: ${escapeHTML(row.id_parcela || 'S/T')} </span><br>
                                <small class="text-secondary">Numero ubicacion: ${escapeHTML(row.numero_ubicacion || '-')} </small><br>
                                <small class="text-muted">Seccion: ${escapeHTML(row.seccion || '-')} Hilera: ${escapeHTML(row.hilera || '-')} Nivel: ${escapeHTML(row.numero_ubicacion || '-')}</small>
                            </div>`;
                }
            },
            {
                data: 'total',
                title: 'Monto y Pago',
                render: (data, type, row) => {
                    let fecha = 'N/A';
                    if (row.fecha_pago && row.fecha_pago !== '0000-00-00 00:00:00') {
                        const partes = row.fecha_pago.split(/[- :]/);
                        const fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
                        fecha = fechaObj.toLocaleDateString('es-AR');
                    }

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

                    const partesVenc = data.substring(0, 10).split('-');
                    const fechaVenc = new Date(partesVenc[0], partesVenc[1] - 1, partesVenc[2]);
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);

                    const esActual = (row.id_pago == row.ultimo_pago_id);
                    const estaVencido = (fechaVenc < hoy);
                    const fechaFormateada = fechaVenc.toLocaleDateString('es-AR');

                    const tieneDifunto = parseInt(row.tiene_difunto) > 0;

                    if (esActual && estaVencido) {
                        if (tieneDifunto) {
                            return `<div class="text-center">
                                        <span class="badge bg-danger mb-1" title="Deuda con ocupación física">MOROSO</span><br>
                                        <span class="text-danger fw-bold small">${fechaFormateada}</span>
                                    </div>`;
                        } else {
                            return `<div class="text-center">
                                        <span class="badge bg-secondary mb-1" title="Sin difunto: Parcela disponible">CADUCADO</span><br>
                                        <span class="text-muted small">${fechaFormateada}</span>
                                    </div>`;
                        }
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
                render: function (data, type, row) {
                    if (data) {
                        return `<a href="${BASE_URL}/public/uploads/${escapeHTML(data)}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-pdf"></i> Ver Archivo
                                </a>`;
                    }
                    return '<span class="text-muted">No disponible</span>';
                }
            }
        ],
        'traslados': [
            { data: 'nombre', render: $.fn.dataTable.render.text() },
            { data: 'apellido', render: $.fn.dataTable.render.text() },
            { data: 'dni', render: $.fn.dataTable.render.text() },
            { data: 'fecha_fallecimiento' },
            { data: 'fecha_retiro' },
            {
                data: null, orderable: false,
                render: function (data, type, row) {
                    const origen = escapeHTML(row.parcela_origen);
                    const destino = escapeHTML(row.parcela_destino || 'Externo');
                    return `<strong>${origen}</strong> &rarr; <strong>${destino}</strong>`;
                }
            }
        ],
        'morosos': [
            { data: 'id_parcela', render: $.fn.dataTable.render.text() },
            { data: 'dni', render: $.fn.dataTable.render.text() },
            { data: 'nombre', render: $.fn.dataTable.render.text() },
            { data: 'apellido', render: $.fn.dataTable.render.text() },
            {
                data: 'fecha_vencimiento',
                render: function (data) {
                    if (!data) return '';
                    const p = data.substring(0, 10).split('-');
                    return `<span class="text-danger fw-bold">${new Date(p[0], p[1] - 1, p[2]).toLocaleDateString('es-AR')}</span>`;
                }
            },
            {
                data: 'total',
                render: data => `$${parseFloat(data).toFixed(2)}`
            },
            {
                data: 'fecha_vencimiento',
                orderable: false,
                render: function (data) {
                    if (!data) return '';
                    const p = data.substring(0, 10).split('-');
                    const venc = new Date(p[0], p[1] - 1, p[2]);
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);

                    const diff = Math.ceil((hoy - venc) / (1000 * 60 * 60 * 24));
                    return `<span class="badge bg-danger">${diff > 0 ? diff : 0} día/s</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-sm btn-success registrar-pago-btn" 
                                data-bs-toggle="modal" data-bs-target="#pagoModal"
                                data-deudor-id="${escapeHTML(row.id_deudo)}" data-parcela-id="${escapeHTML(row.id_parcela)}"
                                data-deudor-nombre="${escapeHTML(row.apellido)}, ${escapeHTML(row.nombre)}"
                                data-vencimiento-anterior="${escapeHTML(row.fecha_vencimiento)}">
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
        const filterIds = tablaJQ.data('filter-ids') ? tablaJQ.data('filter-ids').split(',') : [];

        if (!ajaxUrl || !dataTablesConfigs[configKey]) return;

        const dt = tablaJQ.DataTable({
            retrieve: true, dom: 'Bfrtip', buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 10, processing: true, serverSide: true,
            ajax: {
                url: ajaxUrl, type: 'POST',
                data: function (d) {
                    if (filterIds && filterIds.length >= 2) {
                        d.fecha_inicio = $(filterIds[0].trim()).val();
                        d.fecha_fin = $(filterIds[1].trim()).val();
                    }
                }
            },
            columns: dataTablesConfigs[configKey]
        });

        if (filterIds.length) {
            $(filterIds.join(', ')).on('change', () => dt.ajax.reload());
        }
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

            const soloFecha = vencimientoAnterior.substring(0, 10);
            const partes = soloFecha.split('-');

            if (partes.length === 3) {
                let fecha = new Date(partes[0], partes[1] - 1, partes[2]);

                fecha.setFullYear(fecha.getFullYear() + 1);

                const y = fecha.getFullYear();
                const m = String(fecha.getMonth() + 1).padStart(2, '0');
                const d = String(fecha.getDate()).padStart(2, '0');

                nuevoVencimiento = `${y}-${m}-${d}`;
            } else {
                let hoy = new Date();
                hoy.setFullYear(hoy.getFullYear() + 1);
                nuevoVencimiento = hoy.toISOString().split('T')[0];
            }

            pagoModal.querySelector('#modalDeudorNombre').textContent = deudorNombre;
            pagoModal.querySelector('#modalParcelaId').textContent = parcelaId;
            pagoModal.querySelector('#modalDeudoId').value = deudorId;
            pagoModal.querySelector('#modalParcelaIdInput').value = parcelaId;
            pagoModal.querySelector('#fecha_vencimiento').value = nuevoVencimiento;
        });
    }

    $('#pagoModal form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const data = form.serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function (response) {
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
            error: function (xhr) {
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
