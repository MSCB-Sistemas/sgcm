<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($advertencias)): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <h5 class="alert-heading">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Advertencia
        </h5>
        <ul class="mb-0">
            <?php foreach ($advertencias as $a): ?>
                <li><?= htmlspecialchars($a) ?></li>
            <?php endforeach ?>
        </ul>
        <hr class="my-2">
        <p class="mb-0"><small>La operación se realizó igualmente.</small></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div id="alertas-form"></div>

<form action="<?= isset($datos['action']) ? $datos['action'] : '' ?>" method="POST" id="operacionForm">
    <div class="row mb-3">
        <!-- Parcela -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="parcela_search" class="form-label">Parcela</label>
                <input list="parcelas" id="parcela_search" name="parcela_search" class="form-control" placeholder="Ingrese una parcela" autocomplete="off" required>
                <input type="hidden" id="id_parcela" name="id_parcela">

                <datalist id="parcelas">
                    <?php foreach ($datos['parcelas'] as $p): ?>
                        <option value="<?= htmlspecialchars($p['id_parcela'] . ' - ' . $p['id_tipo_parcela'] . ' - ' . $p['numero_ubicacion'] . ' - ' . $p['hilera'] . '/' . $p['seccion'] . '/' . $p['fraccion'] . '/' . $p['nivel']) ?>"
                        data-id="<?= $p['id_parcela'] ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal"
                data-bs-target="#modalParcela">+</button>
        </div>

        <!-- Deudo -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="deudo_search" class="form-label">Deudo</label>
                <input list="deudos" id="deudo_search" name="deudo_search" class="form-control" placeholder="Ingrese DNI o Nombre" autocomplete="off" required>
                <input type="hidden" id="id_deudo" name="id_deudo">

                <datalist id="deudos">
                    <?php foreach ($datos['deudos'] as $d): ?>
                        <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>"
                        data-id="<?= $d['id_deudo'] ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal"
                data-bs-target="#modalDeudo">+</button>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Difunto -->
        <div class="col-md-6 d-flex align-items-end">
            <div class="flex-grow-1">
                <label for="difunto_search" class="form-label">Difunto</label>
                <input list="difuntos" id="difunto_search" name="difunto_search" class="form-control" placeholder="Ingrese un difunto" autocomplete="off" required>
                <input type="hidden" id="id_difunto" name="id_difunto">

                <datalist id="difuntos">
                    <?php foreach ($datos['difuntos'] as $di): ?>
                        <option value="<?= htmlspecialchars($di['dni'] . ' - ' . $di['nombre'] . ' ' . $di['apellido']) ?>"
                        data-id="<?= $di['id_difunto'] ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal"
                data-bs-target="#modalDifunto">+</button>
        </div>

        <!-- Fecha -->
        <div class="col-md-3">
            <label for="fecha_traslado" class="form-label">Fecha traslado</label>
            <input type="date" class="form-control" id="fecha_traslado" name="fecha_traslado" required
                value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="col-md-3">
            <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
        </div>

        <div class="col-12 mt-2">
                <div class="accordion" id="accordionParcelaInfo"></div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <button type="submit" class="btn btn-success"> <i class="bi bi-save"></i> Guardar</button>
        <a href="<?= URL ?>home" class="btn btn-secondary"> <i class="bi bi-x-circle"></i> Cancelar</a>
    </div>
</form>

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

    function mostrarAdvertencia(mensajes) {
        const contenedor = document.getElementById("alertas-form");
        contenedor.innerHTML = "";

        if (mensajes.length > 0) {
            let html = `
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Advertencia
                </h6>
                <ul class="mb-0">
            `;
            mensajes.forEach(msg => {
                html += `<li>${msg}</li>`;
            });
            html += `
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            contenedor.innerHTML = html;
        }
    }

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
</script>