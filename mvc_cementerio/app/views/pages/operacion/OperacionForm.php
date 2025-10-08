<?php include __DIR__ . "/../../modals/deudo_modal.php"; ?>
<?php include __DIR__ . "/../../modals/difunto_modal.php"; ?>
<?php include __DIR__ . "/../../modals/parcela_modal.php"; ?>

<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger shadow-sm">
        <h6 class="alert-heading"><i class="bi bi-x-circle-fill me-2"></i>Errores encontrados</h6>
        <ul class="mb-0">
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div id="alertas-form"></div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="<?= isset($datos['action']) ? $datos['action'] : '' ?>" method="POST" id="operacionForm">
            <div class="col-md-4">
            <label for="tipo_operacion_search" class="form-label">Tipo de operacion</label>
                <div class="input-group">
                    <select list="tipo_operaciones" id="tipo_operaciones_search" name="tipo_operacion_search"
                            class="form-control" autocomplete="off" required>
                    <select type="hidden" id="id_tipo_operacion" name="id_tipo_operacion">
                </div>
            </div>    
            <hr>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="parcela_search" class="form-label">Parcela</label>
                    <div class="input-group">
                        <input list="parcelas" id="parcela_search" name="parcela_search"
                               class="form-control" placeholder="Ingrese una parcela" autocomplete="off" required>
                        <input type="hidden" id="id_parcela" name="id_parcela">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalParcela">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <datalist id="parcelas">
                        <?php foreach ($datos['parcelas'] as $p): ?>
                            <option value="<?= htmlspecialchars($p['id_parcela'] . ' - Tipo - ' . $p['id_tipo_parcela'] . ' - Ubicacion - ' . $p['numero_ubicacion'] . ' - Hilera - ' . $p['hilera'] . ' - Seccion - ' . $p['seccion'] . ' - Fraccion - ' . $p['fraccion'] . ' - Nivel - ' . $p['nivel']) ?>"
                            data-id="<?= $p['id_parcela'] ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="col-md-6">
                    <label for="deudo_search" class="form-label">Deudo</label>
                    <div class="input-group">
                        <input list="deudos" id="deudo_search" name="deudo_search"
                               class="form-control" placeholder="Ingrese un deudo" autocomplete="off" required>
                        <input type="hidden" id="id_deudo" name="id_deudo">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDeudo">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <datalist id="deudos">
                        <?php foreach ($datos['deudos'] as $d): ?>
                            <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>"
                            data-id="<?= $d['id_deudo'] ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="difunto_search" class="form-label">Difunto</label>
                    <div class="input-group">
                        <input list="difuntos" id="difunto_search" name="difunto_search"
                               class="form-control" placeholder="Ingrese un difunto" autocomplete="off" required>
                        <input type="hidden" id="id_difunto" name="id_difunto">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDifunto">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <datalist id="difuntos">
                        <?php foreach ($datos['difuntos'] as $di): ?>
                            <option value="<?= htmlspecialchars($di['dni'] . ' - ' . $di['nombre'] . ' ' . $di['apellido']) ?>"
                            data-id="<?= $di['id_difunto'] ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="col-md-3">
                    <label for="fecha_traslado" class="form-label">Fecha traslado</label>
                    <input type="date" class="form-control" id="fecha_traslado" name="fecha_traslado"
                           value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="col-md-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="importe" class="form-label">Importe</label>
                    <input type="number" step="0.01" class="form-control" id="importe" name="importe" required>
                </div>
                <div class="col-md-4">
                    <label for="recargo" class="form-label">Recargo (%)</label>
                    <input type="number" step="0.01" class="form-control" id="recargo" name="recargo" required>
                </div>
                <div class="col-md-4">
                    <label for="total" class="form-label">Total</label>
                    <input type="text" class="form-control fw-bold" id="total" name="total" readonly>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="my-4"></div>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <!-- Info Parcela -->
        <div class="accordion mb-3" id="accordionParcelaInfo"></div>
    </div>
</div>
<!-- Botones -->
<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Guardar
    </button>
    <a href="<?= URL ?>home" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> Cancelar
    </a>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function configurarAutocompletado(inputId, hiddenId, datalistId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        input.addEventListener('input', () => {
            hidden.value = '';
            const val = input.value;
            const options = document.querySelectorAll(`#${datalistId} option`);
            const match = Array.from(options).find(opt => opt.value === val);
            if (match) {
                hidden.value = match.dataset.id;
                input.setCustomValidity("");
            }
        });

        input.addEventListener('blur', () => {
            if (!hidden.value) { 
                input.setCustomValidity("Debe seleccionar un elemento de la lista");
            } else {
                input.setCustomValidity("");
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        configurarAutocompletado('deudo_search', 'id_deudo', 'deudos');
        configurarAutocompletado('parcela_search', 'id_parcela', 'parcelas');
        configurarAutocompletado('difunto_search', 'id_difunto', 'difuntos');
    });

    document.getElementById('parcela_search').addEventListener('change', function() {
        const idParcela = this.value;

        if (!idParcela) {
            document.getElementById('accordionParcelaInfo').innerHTML = '';
            return;
        }

        fetch(`/cementerio/mvc_cementerio/parcela/obtenerInfoParcela/${idParcela}`)
            .then(res => res.json())
            .then(data => {
                console.log(data);
                const accordion = document.getElementById('accordionParcelaInfo')
                accordion.innerHTML = "";

                let pagosHtml = `<div class="accordion-item">
                    <h2 class="accordion-header" id="headingPagos">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagos" aria-expanded="true" aria-controls="collapsePagos">
                            <i class="fas fa-receipt me-2"></i>Pagos Asociados (${data.pagos.length})
                        </button>
                    </h2>
                    <div id="collapsePagos" class="accordion-collapse collapse show" aria-labelledby="headingPagos" data-bs-parent="#accordionParcelaInfo">
                        <div class="accordion-body p-0">`;

                if (data.pagos.length > 0) {
                    pagosHtml += `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">Fecha Pago</th>
                                    <th class="text-nowrap">Fecha Vencimiento</th>
                                    <th class="text-nowrap">Total</th>
                                    <th class="text-nowrap">Deudo</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.pagos.forEach(p => {
                        const estadoClass = p.Deudo === 'Pagado' ? 'badge bg-success' : 'badge bg-danger';
                        pagosHtml += `
                                <tr>
                                    <td class="text-nowrap">${p.fecha_pago}</td>
                                    <td class="text-nowrap">${p.fecha_vencimiento}</td>
                                    <td class="text-nowrap">ARS ${p.total}</td>
                                    <td class="text-nowrap">${p.Deudo}</td>
                                </tr>`;
                    });

                    pagosHtml += `
                            </tbody>
                        </table>
                    </div>`;
                } else {
                    pagosHtml += `
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No hay pagos asociados a esta parcela</p>
                    </div>`;
                }

                pagosHtml += `</div></div></div>`;

                let difuntosHtml = `<div class="accordion-item">
                    <h2 class="accordion-header" id="headingDifuntos">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDifuntos" aria-expanded="false" aria-controls="collapseDifuntos">
                            <i class="fas fa-users me-2"></i>Difuntos Asociados (${data.difuntos.length})
                        </button>
                    </h2>
                    <div id="collapseDifuntos" class="accordion-collapse collapse" aria-labelledby="headingDifuntos" data-bs-parent="#accordionParcelaInfo">
                        <div class="accordion-body p-0">`;

                if (data.difuntos.length > 0) {
                    difuntosHtml += `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">DNI</th>
                                    <th class="text-nowrap">Nombre Completo</th>
                                    <th class="text-nowrap">Apellido</th>
                                    <th class="text-nowrap">Fecha Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.difuntos.forEach(d => {
                        difuntosHtml += `
                                <tr>
                                    <td class="text-nowrap">${d.dni}</td>
                                    <td class="text-nowrap fw-bold">${d.nombre}</td>
                                    <td class="text-nowrap">${d.apellido}</td>
                                    <td class="text-nowrap">${d.fecha_ubicacion}</td>
                                </tr>`;
                    });

                    difuntosHtml += `
                            </tbody>
                        </table>
                    </div>`;
                } else {
                    difuntosHtml += `
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No hay difuntos registrados en esta parcela</p>
                    </div>`;
                }

                difuntosHtml += `</div></div></div>`;
                accordion.innerHTML = pagosHtml + difuntosHtml;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('accordionParcelaInfo').innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>Error al cargar la información de la parcela.</div>
                    </div>`;
            });
    });

    document.getElementById("deudo_search").addEventListener("change", function () {
        const idDeudo = document.getElementById("id_deudo").value;
        if (idDeudo) {
            fetch(`/deudo/verificar/${idDeudo}`)
                .then(res => res.json())
                .then(data => {
                    mostrarAdvertencia(data.advertencias);
                });
        }
    });

    document.getElementById("difunto_search").addEventListener("change", function () {
        const idDifunto = document.getElementById("id_difunto").value;
        if (idDifunto) {
            fetch(`/difunto/verificar/${idDifunto}`)
                .then(res => res.json())
                .then(data => {
                    mostrarAdvertencia(data.advertencias);
                });
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        const monto = document.getElementById("importe");
        const recargo = document.getElementById("recargo");
        const total = document.getElementById("total");

        function calcularTotal() {
            const montoVal = parseFloat(monto.value) || 0;
            const recargoVal = parseFloat(recargo.value) || 0;
            const totalVal = montoVal + (montoVal * recargoVal / 100);
            total.value = totalVal.toFixed(2);
        }

        monto.addEventListener("input", calcularTotal);
        recargo.addEventListener("input", calcularTotal);
    });
</script>