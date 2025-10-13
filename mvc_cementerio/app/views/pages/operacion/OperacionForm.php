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

            <div id="seccion-1" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Traslado Interno</h5>
                <p class="text-muted small">Mueve un difunto de su ubicacion actual a una nueva parcela vacía.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="difunto_search_traslado" class="form-label">Difunto a trasladar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_traslado" name="id_difunto_ti" class="form-control" placeholder="Buscar difunto...">
                            <input type="hidden" id="id_difunto_traslado">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDifunto">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="parcela_search_destino" class="form-label">Parcela de Destino</label>
                        <div class="input-group">
                            <input list="parcelas" id="parcela_search_destino" name="id_parcela_destino" class="form-control" placeholder="Buscar parcela de destino...">
                            <input type="hidden" id="id_parcela_destino">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_traslado" class="form-label">Fecha del Traslado</label>
                        <input type="date" class="form-control" name="fecha_traslado" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php 
                    $prefix = 'ti';
                    include __DIR__ . '/../partials/_campos_pago.php'; 
                ?>
            </div>

            <div id="seccion-2" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Traslado Externo (Exhumación)</h5>
                <p class="text-muted small">Registra el retiro de un difunto del cementerio.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="difunto_search_externo" class="form-label">Difunto a exhumar</label>
                        <input list="difuntos" id="difunto_search_externo" name="id_difunto_te" class="form-control" placeholder="Buscar difunto...">
                        <input type="hidden" id="id_difunto_externo">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_exhumacion" class="form-label">Fecha de Exhumación</label>
                        <input type="date" class="form-control" name="fecha_exhumacion" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div id="seccion-3" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Autorización para Personas de Bajos Recursos</h5>
                <p class="text-muted small">Registra un nuevo ingreso en una parcela asignada.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="difunto_search_br" class="form-label">Difunto a inhumar</label>
                        <input list="difuntos" id="difunto_search_br" name="id_difunto_br" class="form-control" placeholder="Buscar o registrar difunto...">
                        <input type="hidden" id="id_difunto_br">
                    </div>
                    <div class="col-md-6">
                        <label for="parcela_search_br" class="form-label">Parcela Asignada (debe estar vacía)</label>
                        <input list="parcelas" id="parcela_search_br" name="id_parcela_br" class="form-control" placeholder="Buscar parcela...">
                        <input type="hidden" id="id_parcela_br">
                    </div>
                </div>
                <?php 
                    $prefix = 'br';
                    include __DIR__ . '/../partials/_campos_pago.php'; 
                ?>
            </div>

            <div id="seccion-4" class="seccion-operacion" style="display:none;">
                 <h5 class="mb-3">Certificado Libre de Deuda</h5>
                 <div class="row g-3">
                    <div class="col-md-6">
                        <label for="difunto_search_traslado" class="form-label">Difunto a trasladar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_traslado" name="id_difunto_ti" class="form-control" placeholder="Buscar difunto...">
                            <input type="hidden" id="id_difunto_traslado">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDifunto">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="parcela_search_destino" class="form-label">Parcela de Destino</label>
                        <div class="input-group">
                            <input list="parcelas" id="parcela_search_destino" name="id_parcela_destino" class="form-control" placeholder="Buscar parcela de destino...">
                            <input type="hidden" id="id_parcela_destino">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

<datalist id="parcelas"><?php foreach ($datos['parcelas'] as $p): ?><option value="<?= htmlspecialchars($p['id_parcela'] . ' - ...') ?>" data-id="<?= $p['id_parcela'] ?>"><?php endforeach; ?></datalist>
<datalist id="deudos"><?php foreach ($datos['deudos'] as $d): ?><option value="<?= htmlspecialchars($d['dni'] . ' - ...') ?>" data-id="<?= $d['id_deudo'] ?>"><?php endforeach; ?></datalist>
<datalist id="difuntos"><?php foreach ($datos['difuntos'] as $di): ?><option value="<?= htmlspecialchars($di['dni'] . ' - ...') ?>" data-id="<?= $di['id_difunto'] ?>"><?php endforeach; ?></datalist>

<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" form="operacionForm" class="btn btn-success"><i class="bi bi-save"></i> Guardar Operación</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function configurarAutocompletado(inputId, hiddenId, datalistId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        if (!input || !hidden) return;

        input.addEventListener('input', () => {
            hidden.value = '';
            const val = input.value;
            const options = document.querySelectorAll(`#${datalistId} option`);
            const match = Array.from(options).find(opt => opt.value === val);
            if (match) {
                hidden.value = match.dataset.id;
            }
        });
    }

    // Traslado Interno
    configurarAutocompletado('difunto_search_traslado', 'id_difunto_traslado', 'difuntos');
    configurarAutocompletado('parcela_search_destino', 'id_parcela_destino', 'parcelas');
    configurarAutocompletado('deudo_search_ti', 'id_deudo_ti', 'deudos'); 
    
    // Bajos Recursos
    configurarAutocompletado('difunto_search_br', 'id_difunto_br', 'difuntos');
    configurarAutocompletado('parcela_search_br', 'id_parcela_br', 'parcelas');
    configurarAutocompletado('deudo_search_br', 'id_deudo_br', 'deudos'); 

    // Libre de Deuda
    configurarAutocompletado('deudo_search_libredeuda', 'id_deudo_libredeuda', 'deudos');
    configurarAutocompletado('parcela_search_libredeuda', 'id_parcela_libredeuda', 'parcelas');

    const selectorOperacion = document.getElementById('tipo_operacion_selector');
    const inputHiddenOperacion = document.getElementById('tipo_operacion_hidden');

    selectorOperacion.addEventListener('change', function() {
        const seleccion = this.value;
        document.querySelectorAll('.seccion-operacion').forEach(s => s.style.display = 'none');
        
        if (seleccion) {
            const seccionAMostrar = document.getElementById('seccion-' + seleccion);
            if (seccionAMostrar) seccionAMostrar.style.display = 'block';
            inputHiddenOperacion.value = seleccion;
        } else {
            inputHiddenOperacion.value = '';
        }
    });

    function configurarCalculoTotal(prefix) {
        const monto = document.getElementById(`importe_${prefix}`);
        const recargo = document.getElementById(`recargo_${prefix}`);
        const total = document.getElementById(`total_${prefix}`);

        function calcular() {
            if (!monto || !recargo || !total) return;
            const montoVal = parseFloat(monto.value) || 0;
            const recargoVal = parseFloat(recargo.value) || 0;
            const totalVal = montoVal + (montoVal * recargoVal / 100);
            total.value = totalVal.toFixed(2);
        }

        if (monto) monto.addEventListener("input", calcular);
        if (recargo) recargo.addEventListener("input", calcular);
    }
    
    configurarCalculoTotal('ti');
    configurarCalculoTotal('br');

    function configurarModalAjax(modalId, formId, datalistId, targetInputId, targetHiddenId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const url = form.getAttribute('action');

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.newItem) {
                    const option = document.createElement('option');
                    option.value = data.newItem.text; 
                    option.dataset.id = data.newItem.id; 

                    document.getElementById(datalistId).appendChild(option);

                    document.getElementById(targetInputId).value = data.newItem.text;
                    document.getElementById(targetHiddenId).value = data.newItem.id;

                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    modal.hide();
                    form.reset();
                } else {
                    alert('Errores: ' + (data.errors || []).join('\n'));
                }
            })
            .catch(error => console.error('Error en el envío del modal:', error));
        });
    }

    configurarModalAjax('modalDifunto', 'formNuevoDifunto', 'difuntos', 'difunto_search_traslado', 'id_difunto_traslado');
    configurarModalAjax('modalParcela', 'formNuevaParcela', 'parcelas', 'parcela_search_destino', 'id_parcela_destino');
    configurarModalAjax('modalDeudo', 'formNuevoDeudo', 'deudos', 'deudo_search_ti', 'id_deudo_ti');
</script>