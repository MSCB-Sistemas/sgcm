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

        <div class="mb-3">
            <label for="tipo_operacion_selector" class="form-label fw-bold">Tipo de Operación</label>
            <select class="form-select" id="tipo_operacion_selector" required>
                <option value="">Seleccione una operación...</option>
                <?php foreach ($datos['tipo_operaciones'] as $op): ?>
                    <option value="<?= $op['id_tipo_operacion'] ?>">
                        <?= htmlspecialchars($op['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr>

        <form action="<?= $datos['action'] ?>" method="POST" id="operacionForm">
            <input type="hidden" name="tipo_operacion" id="tipo_operacion_hidden">

            <!-- Traslado Interno (Exhumación) -->
            <div id="seccion-1" class="seccion-operacion" data-prefix="ti" style="display:none;">
                <h5 class="mb-3">Traslado Interno</h5>
                <p class="text-muted small">Mueve un difunto de su ubicacion actual a una nueva parcela vacía.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a trasladar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_ti" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_ti" id="id_difunto_ti">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela de Destino (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_ti" class="form-control"
                                placeholder="Buscar parcela de destino...">
                            <input type="hidden" name="id_parcela_ti" id="id_parcela_ti">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha del Traslado</label>
                        <input type="date" class="form-control" name="fecha_traslado_ti" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'ti';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Traslado Externo (Exhumación) -->
            <div id="seccion-2" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Traslado Externo (Exhumación)</h5>
                <p class="text-muted small">Registra el retiro de un difunto del cementerio.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a exhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_te" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_te" id="id_difunto_te">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Exhumación</label>
                        <input type="date" class="form-control" name="fecha_exhumacion_te"
                            value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <!-- Persona bajos recursos -->
            <div id="seccion-3" class="seccion-operacion" data-prefix="br" style="display:none;">
                <h5 class="mb-3">Autorización para Personas de Bajos Recursos</h5>
                <p class="text-muted small">Registra un nuevo ingreso en una parcela asignada.</p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a inhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_br" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_br" id="id_difunto_br">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela Asignada (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_br" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_br" id="id_parcela_br">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php $prefix = 'br';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Libre de deuda -->
            <div id="seccion-4" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Certificado Libre de Deuda</h5>
                <p class="text-muted small">Imprime comprobante de libre de deuda para un deudo y parcela especifica.
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="deudo_search_ld" class="form-label">Deudo a verificar</label>
                        <div class="input-group">
                            <input list="deudos" id="deudo_search_ld" class="form-control"
                                placeholder="Buscar deudo...">
                            <input type="hidden" name="id_deudo_ld" id="id_deudo_ld">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela a verificar</label>
                        <div class="input-group">
                            <input list="parcelasOcupadas" id="parcela_search_ld" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_ld" id="id_parcela_ld">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingreso de Difunto -->
            <div id="seccion-5" class="seccion-operacion" data-prefix="in" style="display:none;">
                <h5 class="mb-3">Ingreso de Difunto</h5>
                <p class="text-muted small">Registra la primera inhumación de un difunto en una parcela vacía.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a inhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_in" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_in" id="id_difunto_in">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela de Destino (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_in" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_in" id="id_parcela_in">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Ingreso</label>
                        <input type="date" class="form-control" name="fecha_ingreso_in" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'in';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Renovacion de Pago -->
            <div id="seccion-6" class="seccion-operacion" data-prefix="rp" style="display:none;">
                <h5 class="mb-3">Renovación de Pago</h5>
                <p class="text-muted small">Registra la renovación del pago de una parcela ocupada.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Deudo Responsable</label>
                        <div class="input-group">
                            <input list="deudos" id="deudo_search_rp" class="form-control"
                                placeholder="Buscar deudo...">
                            <input type="hidden" name="id_deudo_rp" id="id_deudo_rp">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDeudo"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela a Renovar</label>
                        <div class="input-group">
                            <input list="parcelasPagasPorDeudo" id="parcela_search_rp" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_rp" id="id_parcela_rp">
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Renovación</label>
                        <input type="date" class="form-control" name="fecha_renovacion_rp"
                            value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'rp';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>
        </form>
    </div>
</div>

<datalist id="parcelasPagasPorDeudo">
    <?php foreach ($datos['parcelasPagasPorDeudo'] as $p): ?>
        <?php
        $texto_parcela = "ID: ";

        if (isset($p['id_parcela'])) {
            $texto_parcela .= $p['id_parcela'];
        } else {
            $texto_parcela .= '';
        }

        $texto_parcela .= " | Ubic: ";
        if (isset($p['numero_ubicacion'])) {
            $texto_parcela .= $p['numero_ubicacion'];
        } else {
            $texto_parcela .= 'S/N';
        }

        $texto_parcela .= " | Sec: ";
        if (isset($p['seccion'])) {
            $texto_parcela .= $p['seccion'];
        } else {
            $texto_parcela .= 'S/S';
        }

        $texto_parcela .= " | Hil: ";
        if (isset($p['hilera'])) {
            $texto_parcela .= $p['hilera'];
        } else {
            $texto_parcela .= 'S/H';
        }
        ?>
        <option value="<?= htmlspecialchars($texto_parcela) ?>" data-id="<?= $p['id_parcela'] ?>">
        <?php endforeach; ?>
</datalist>

<datalist id="parcelasDisponibles">
    <?php foreach ($datos['parcelasDisponibles'] as $p): ?>
        <?php
        $texto_parcela = "ID: ";

        if (isset($p['id_parcela'])) {
            $texto_parcela .= $p['id_parcela'];
        } else {
            $texto_parcela .= '';
        }

        $texto_parcela .= " | Ubic: ";
        if (isset($p['numero_ubicacion'])) {
            $texto_parcela .= $p['numero_ubicacion'];
        } else {
            $texto_parcela .= 'S/N';
        }

        $texto_parcela .= " | Sec: ";
        if (isset($p['seccion'])) {
            $texto_parcela .= $p['seccion'];
        } else {
            $texto_parcela .= 'S/S';
        }

        $texto_parcela .= " | Hil: ";
        if (isset($p['hilera'])) {
            $texto_parcela .= $p['hilera'];
        } else {
            $texto_parcela .= 'S/H';
        }
        ?>
        <option value="<?= htmlspecialchars($texto_parcela) ?>" data-id="<?= $p['id_parcela'] ?>">
        <?php endforeach; ?>
</datalist>

<datalist id="parcelasOcupadas">
    <?php foreach ($datos['parcelasOcupadas'] as $p): ?>
        <?php
        $texto_parcela = "ID: ";

        if (isset($p['id_parcela'])) {
            $texto_parcela .= $p['id_parcela'];
        } else {
            $texto_parcela .= '';
        }

        $texto_parcela .= " | Ubic: ";
        if (isset($p['numero_ubicacion'])) {
            $texto_parcela .= $p['numero_ubicacion'];
        } else {
            $texto_parcela .= 'S/N';
        }

        $texto_parcela .= " | Sec: ";
        if (isset($p['seccion'])) {
            $texto_parcela .= $p['seccion'];
        } else {
            $texto_parcela .= 'S/S';
        }

        $texto_parcela .= " | Hil: ";
        if (isset($p['hilera'])) {
            $texto_parcela .= $p['hilera'];
        } else {
            $texto_parcela .= 'S/H';
        }
        ?>
        <option value="<?= htmlspecialchars($texto_parcela) ?>" data-id="<?= $p['id_parcela'] ?>">
        <?php endforeach; ?>
</datalist>

<datalist id="deudos">
    <?php foreach ($datos['deudos'] as $d): ?>
        <?php
        if (isset($d['dni'])) {
            $texto_deudo = $d['dni'];
        } else {
            $texto_deudo = 'S/DNI';
        }

        $texto_deudo .= ' - ';

        if (isset($d['apellido'])) {
            $texto_deudo .= $d['apellido'];
        } else {
            $texto_deudo .= '';
        }

        $texto_deudo .= ', ';

        if (isset($d['nombre'])) {
            $texto_deudo .= $d['nombre'];
        } else {
            $texto_deudo .= '';
        }
        ?>
        <option value="<?= htmlspecialchars(strtoupper($texto_deudo)) ?>" data-id="<?= $d['id_deudo'] ?>">
        <?php endforeach; ?>
</datalist>

<datalist id="difuntos">
    <?php foreach ($datos['difuntos'] as $di): ?>
        <?php
        if (isset($di['dni'])) {
            $texto_difunto = $di['dni'];
        } else {
            $texto_difunto = 'S/DNI';
        }

        $texto_difunto .= ' - ';

        if (isset($di['apellido'])) {
            $texto_difunto .= $di['apellido'];
        } else {
            $texto_difunto .= '';
        }

        $texto_difunto .= ', ';

        if (isset($di['nombre'])) {
            $texto_difunto .= $di['nombre'];
        } else {
            $texto_difunto .= '';
        }
        ?>
        <option value="<?= htmlspecialchars(strtoupper($texto_difunto)) ?>" data-id="<?= $di['id_difunto'] ?>">
        <?php endforeach; ?>
</datalist>

<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" form="operacionForm" class="btn btn-success"><i class="bi bi-save"></i> 
        Guardar Operación
    </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- 1. FUNCIÓN AUTOCOMPLETADO ---
        function configurarAutocompletado(inputId, hiddenId, datalistId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            if (!input || !hidden) return;

            const normalizeText = (str) => str.toLowerCase().replace(/[\s-]+/g, '');

            input.addEventListener('input', () => {
                hidden.value = '';
                const valorInputNormalizado = normalizeText(input.value);
                if (valorInputNormalizado === '') return;

                const options = document.querySelectorAll(`#${datalistId} option`);
                for (const option of options) {
                    const valorOpcionNormalizado = normalizeText(option.value);
                    if (valorOpcionNormalizado === valorInputNormalizado) {
                        hidden.value = option.dataset.id;
                        input.setCustomValidity("");
                        input.dispatchEvent(new Event('change', { 'bubbles': true }));
                        break;
                    }
                }
            });

            input.addEventListener('blur', () => {
                if (!hidden.value && input.required) {
                    input.setCustomValidity("Debe seleccionar un elemento válido de la lista.");
                } else {
                    input.setCustomValidity("");
                }
            });
        }

        // --- 2. FUNCIÓN INFO DINÁMICA (ACCORDEON) ---
        function configurarInfoDinamica(inputId, hiddenId, urlTemplate, accordionId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('change', function () {
                const id = document.getElementById(hiddenId).value;
                const accordion = document.getElementById(accordionId);
                
                if (!id) {
                    accordion.innerHTML = '';
                    return;
                }
                
                accordion.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>';

                fetch(urlTemplate + id)
                    .then(res => {
                        if (!res.ok) throw new Error('Error en la respuesta del servidor');
                        return res.json();
                    })
                    .then(data => {
                        let pagosHtml = `<div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagos">Pagos Asociados (${data.pagos.length})</button></h2><div id="collapsePagos" class="accordion-collapse collapse show"><div class="accordion-body p-0">`;
                        if (data.pagos.length > 0) {
                            pagosHtml += `<table class="table table-sm table-striped mb-0"><thead><tr><th>Fecha Pago</th><th>Vencimiento</th><th>Total</th><th>Deudo</th></tr></thead><tbody>`;
                            data.pagos.forEach(p => {
                                pagosHtml += `<tr><td>${p.fecha_pago}</td><td>${p.fecha_vencimiento}</td><td>ARS ${p.total}</td><td>${p.Deudo}</td></tr>`;
                            });
                            pagosHtml += `</tbody></table>`;
                        } else {
                            pagosHtml += `<p class="text-center text-muted p-3">No hay pagos asociados.</p>`;
                        }
                        pagosHtml += `</div></div></div>`;

                        let difuntosHtml = `<div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDifuntos">Difuntos Asociados (${data.difuntos.length})</button></h2><div id="collapseDifuntos" class="accordion-collapse collapse"><div class="accordion-body p-0">`;
                        if (data.difuntos.length > 0) {
                             difuntosHtml += `<table class="table table-sm table-striped mb-0"><thead><tr><th>DNI</th><th>Nombre</th><th>Apellido</th><th>Fecha Ubicación</th></tr></thead><tbody>`;
                            data.difuntos.forEach(d => {
                                difuntosHtml += `<tr><td>${d.dni}</td><td>${d.nombre}</td><td>${d.apellido}</td><td>${d.fecha_ubicacion}</td></tr>`;
                            });
                            difuntosHtml += `</tbody></table>`;
                        } else {
                            difuntosHtml += `<p class="text-center text-muted p-3">No hay difuntos asociados.</p>`;
                        }
                        difuntosHtml += `</div></div></div>`;

                        accordion.innerHTML = pagosHtml + difuntosHtml;
                    })
                    .catch(err => {
                        console.error("Error al cargar info dinámica:", err);
                        accordion.innerHTML = `<div class="alert alert-danger">Error al cargar los detalles.</div>`;
                    });
            });
        }

        // --- 3. FUNCIÓN MODALES AJAX (GUARDADO) ---
        function configurarModalAjax(modalId, formId, datalistId) {
            const form = document.getElementById(formId);
            const modalEl = document.getElementById(modalId);
            if (!form || !modalEl) return;

            let inputActivo = null;

            modalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (button) {
                    const container = button.closest('.input-group') || button.parentElement;
                    inputActivo = container ? container.querySelector('input[list]') : null;
                }
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                
                const formData = new FormData(form);
                const url = form.getAttribute('action');

                fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.json().then(data => ({ ok: response.ok, data: data })))
                    .then(({ ok, data }) => {
                        if (ok && data.success) {
                            const datalist = document.getElementById(datalistId);
                            if (datalist && data.newItem) {
                                const option = document.createElement('option');
                                option.value = data.newItem.text;
                                option.dataset.id = data.newItem.id;
                                datalist.appendChild(option);

                                if (inputActivo) {
                                    inputActivo.value = data.newItem.text;
                                    const hiddenId = inputActivo.id.replace('search', 'id');
                                    const hidden = document.getElementById(hiddenId);
                                    if(hidden) hidden.value = data.newItem.id;
                                }
                            }
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();
                            form.reset();
                            form.classList.remove('was-validated');
                            alert(data.mensaje || 'Creado con éxito.');
                        } else {
                            const errorMsg = data.errors ? data.errors.join('\n') : 'Ocurrió un error.';
                            alert('No se pudo guardar:\n' + errorMsg);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de comunicación.');
                    });
            });
        }

        // --- 4. SELECTOR DE OPERACIONES ---
        const selectorOperacion = document.getElementById('tipo_operacion_selector');
        if (selectorOperacion) {
            selectorOperacion.addEventListener('change', function () {
                const seleccion = this.value;
                document.querySelectorAll('.seccion-operacion').forEach(s => s.style.display = 'none');
                if (seleccion) {
                    const seccionAMostrar = document.getElementById('seccion-' + seleccion);
                    if (seccionAMostrar) seccionAMostrar.style.display = 'block';
                    document.getElementById('tipo_operacion_hidden').value = seleccion;
                }
            });
        }

        // --- 5. CONFIGURACIÓN DE AUTOCOMPLETADOS ---
        // Traslado Interno (TI)
        configurarAutocompletado('difunto_search_ti', 'id_difunto_ti', 'difuntos');
        configurarAutocompletado('parcela_search_ti', 'id_parcela_ti', 'parcelas');
        configurarAutocompletado('deudo_search_ti', 'id_deudo_ti', 'deudos');

        // Traslado Externo (TE)
        configurarAutocompletado('difunto_search_te', 'id_difunto_te', 'difuntos');

        // Persona bajos recursos (BR)
        configurarAutocompletado('difunto_search_br', 'id_difunto_br', 'difuntos');
        configurarAutocompletado('parcela_search_br', 'id_parcela_br', 'parcelas');
        configurarAutocompletado('deudo_search_br', 'id_deudo_br', 'deudos');

        // Libre de deuda (LD)
        configurarAutocompletado('parcela_search_ld', 'id_parcela_ld', 'parcelas');
        configurarAutocompletado('deudo_search_ld', 'id_deudo_ld', 'deudos');

        // Ingreso de Difunto (IN)
        configurarAutocompletado('difunto_search_in', 'id_difunto_in', 'difuntos');
        configurarAutocompletado('parcela_search_in', 'id_parcela_in', 'parcelas');
        configurarAutocompletado('deudo_search_in', 'id_deudo_in', 'deudos');

        // Renovacion de Pago (RP)
        configurarAutocompletado('deudo_search_rp', 'id_deudo_rp', 'deudos');
        configurarAutocompletado('parcela_search_rp', 'id_parcela_rp', 'parcelasOcupadas');

        // --- 6. INFO DINÁMICA ---
        const urlInfoParcela = "<?= URL ?>parcela/obtenerInfoParcela/";
        configurarInfoDinamica('parcela_search_ti', 'id_parcela_ti', urlInfoParcela, 'accordionParcelaInfo');
        configurarInfoDinamica('parcela_search_br', 'id_parcela_br', urlInfoParcela, 'accordionParcelaInfo');
        configurarInfoDinamica('parcela_search_ld', 'id_parcela_ld', urlInfoParcela, 'accordionParcelaInfo');
        configurarInfoDinamica('parcela_search_rp', 'id_parcela_rp', urlInfoParcela, 'accordionParcelaInfo');

        // --- 7. MODALES ---
        configurarModalAjax('modalDifunto', 'formNuevoDifunto', 'difuntos');
        configurarModalAjax('modalParcela', 'formNuevaParcela', 'parcelas');
        configurarModalAjax('modalDeudo', 'formNuevoDeudo', 'deudos');

        // --- 8. CÁLCULOS DE TOTALES ---
        function configurarCalculoTotal(prefix) {
            const monto = document.getElementById(`importe_${prefix}`);
            const recargo = document.getElementById(`recargo_${prefix}`);
            const total = document.getElementById(`total_${prefix}`);

            function calcular() {
                if (!monto || !recargo || !total) return;
                const montoVal = parseFloat(monto.value) || 0;
                const recargoVal = parseFloat(recargo.value) || 0;
                total.value = (montoVal + (montoVal * recargoVal / 100)).toFixed(2);
            }

            if (monto) monto.addEventListener("input", calcular);
            if (recargo) recargo.addEventListener("input", calcular);
        }

        ['ti', 'br', 'in', 'rp'].forEach(configurarCalculoTotal);
    });
</script>