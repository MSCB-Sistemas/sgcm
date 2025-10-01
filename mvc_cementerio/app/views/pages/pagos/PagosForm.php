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
                if (isset($datos['values']['id_deudo'])) {
                    $id_deudo_selected = $datos['values']['id_deudo'];
                } else {
                    $id_deudo_selected = '';
                }

                if (isset($datos['values']['id_parcela'])) {
                    $id_parcela_selected = $datos['values']['id_parcela'];
                } else {
                    $id_parcela_selected = '';
                }

                if (isset($datos['values']['fecha_pago'])) {
                    $fecha_pago = htmlspecialchars($datos['values']['fecha_pago']);
                } else {
                    $fecha_pago = '';
                }

                if (isset($datos['values']['fecha_vencimiento'])) {
                    $fecha_vencimiento = htmlspecialchars($datos['values']['fecha_vencimiento']);
                } else {
                    $fecha_vencimiento = '';
                }

                if (isset($datos['values']['importe'])) {
                    $importe = htmlspecialchars($datos['values']['importe']);
                } else {
                    $importe = '';
                }

                if (isset($datos['values']['recargo'])) {
                    $recargo = htmlspecialchars($datos['values']['recargo']);
                } else {
                    $recargo = '';
                }

                if (isset($datos['values']['total'])) {
                    $total = htmlspecialchars($datos['values']['total']);
                } else {
                    $total = '';
                }
            ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="deudo" class="form-label fw-bold">Deudo</label>
                            <select class="form-select" id="deudo" name="deudo" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['deudos'] as $d): ?>
                                    <option value="<?= $d['id_deudo'] ?>">
                                        <?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un deudo
                            </div>
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