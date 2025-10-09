<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><?= $datos['title'] ?></h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($datos['errores'])): ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">¡Error!</h5>
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            // Campos que requieren htmlspecialchars
            $campos_html = ['fecha_pago', 'fecha_vencimiento', 'importe', 'recargo', 'total'];

            // Campos directos (sin htmlspecialchars)
            $campos_directos = [
                'id_deudo' => 'id_deudo_selected',
                'id_parcela' => 'id_parcela_selected'
            ];

            // Asignación de campos con htmlspecialchars
            foreach ($campos_html as $campo) {
                if (isset($datos['values'][$campo])) {
                    $$campo = htmlspecialchars($datos['values'][$campo]);
                } else {
                    $$campo = '';
                }
            }

            // Asignación de campos directos con nombres personalizados
            foreach ($campos_directos as $campo => $variable) {
                if (isset($datos['values'][$campo])) {
                    $$variable = $datos['values'][$campo];
                } else {
                    $$variable = '';
                }
            }
            ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="deudo_search" class="form-label fw-bold">Deudo</label>
                            <div class="input-group">
                                <input list="deudos" id="deudo_search" name="deudo_search"
                                    class="form-control" placeholder="Ingrese un deudo" autocomplete="off" required>
                                <input type="hidden" id="id_deudo" name="id_deudo">
                            </div>
                            <datalist id="deudos">
                                <?php foreach ($datos['deudos'] as $d): ?>
                                    <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>"
                                    data-id="<?= $d['id_deudo'] ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="mb-3">
                            <label for="parcela" class="form-label fw-bold">Parcela</label>
                            <select class="form-select" id="parcela" name="parcela" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['parcelas'] as $p): ?>
                                    <option value="<?= $p['id_parcela'] ?>">
                                        <?= htmlspecialchars($p['id_parcela'] . ' - ' . $p['id_tipo_parcela'] . ' - ' . $p['numero_ubicacion'] . ' - | ' . $p['hilera'] . ' | ' . $p['seccion'] . ' | ' . $p['fraccion'] . ' | ' . $p['nivel'] . ' |') ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione una parcela
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_operacion" class="form-label fw-bold">Operacion</label>
                            <select class="form-select" id="tipo_operacion" name="tipo_operacion" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['tipo_operaciones'] as $to): ?>
                                    <option value="<?= $to['id_tipo_operacion'] ?>">
                                        <?= htmlspecialchars($to['id_tipo_operacion'] . ' - ' . $to['descripcion']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un tipo de operacion
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha_pago" class="form-label fw-bold">Fecha de pago</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                                   value="<?= $fecha_pago ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la fecha de pago
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label fw-bold">Fecha de vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                   value="<?= htmlspecialchars($datos['values']['fecha_vencimiento'] ?? '') ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la fecha de vencimiento
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="importe" class="form-label fw-bold">Importe</label>
                            <input type="number" class="form-control" id="importe" name="importe"
                                value="<?= htmlspecialchars($datos['values']['importe'] ?? '') ?>" required oninput="calcularTotal()">
                            <div class="invalid-feedback">
                                Por favor ingrese el importe
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vinculo_familiar" class="form-label fw-bold">Vinculo familiar</label>
                            <input type="text" class="form-control" id="vinculo_familiar" name="vinculo_familiar"
                                value="<?= htmlspecialchars($datos['values']['vinculo_familiar'] ?? '') ?>">
                            <div class="invalid-feedback">
                                Por favor ingrese el vincula familiar
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="responsable_tramite" class="form-label fw-bold">Responsable de tramite</label>
                            <input type="text" class="form-control" id="responsable_tramite" name="responsable_tramite"
                                value="<?= htmlspecialchars($datos['values']['responsable_tramite'] ?? '') ?>">
                            <div class="invalid-feedback">
                                Por favor ingrese el responsable del tramite
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="recargo" class="form-label fw-bold">Recargo (%)</label>
                            <input type="number" step="0.01" class="form-control" id="recargo" name="recargo" 
                                value="<?= htmlspecialchars($datos['values']['recargo'] ?? '') ?>" required oninput="calcularTotal()">
                            <div class="invalid-feedback">
                                Por favor ingrese el recargo
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total" class="form-label fw-bold">Total</label>
                            <input type="number" step="0.01" class="form-control" id="total" name="total"
                                value="<?= htmlspecialchars($datos['values']['total'] ?? '') ?>" readonly>
                            <div class="invalid-feedback">
                                Por favor ingrese la fracción
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>pago" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    });

function calcularTotal()
{
    const importe = parseFloat(document.getElementById('importe').value) || 0;
    const recargo = parseFloat(document.getElementById('recargo').value) || 0;

    const montoRecargo = importe * (recargo / 100);
    const total = importe + montoRecargo;

    document.getElementById('total').value = total.toFixed(2);
}

(function () {
  'use strict'

  var forms = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>

<script>
    function configurarAutocompletado(inputId, hiddenId, datalistId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const options = document.querySelectorAll(`#${datalistId} option`);

        input.addEventListener('input', () => {
            const val = input.value.trim();
            hidden.value = '';
            let valid = false;

            options.forEach(opt => {
                if (opt.value === val) {
                    hidden.value = opt.dataset.id;
                    valid = true;
                }
            });

            if (!valid) {
                input.setCustomValidity("Debe seleccionar un elemento de la lista");
            } else {
                input.setCustomValidity("");
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        configurarAutocompletado('deudo_search', 'id_deudo', 'deudos');
    });
</script>