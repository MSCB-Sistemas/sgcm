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
                            <label for="deudo" class="form-label fw-bold">Deudo</label>
                            <select class="form-select" id="deudo" name="deudo" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['deudos'] as $n): ?>
                                    <?php
                                    if ($id_deudo_selected == $n['id_deudo']) {
                                        $selected_deudo = 'selected';
                                    } else {
                                        $selected_deudo = '';
                                    }
                                    ?>
                                    <option value="<?= $n['id_deudo'] ?>" <?= $selected_deudo ?>>
                                        <?= htmlspecialchars($n['nombre']) ?>
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
                                <?php foreach ($datos['parcelas'] as $n): ?>
                                    <?php
                                    if ($id_parcela_selected == $n['id_parcela']) {
                                        $selected_parcela = 'selected';
                                    } else {
                                        $selected_parcela = '';
                                    }
                                    ?>
                                    <option value="<?= $n['id_parcela'] ?>" <?= $selected_parcela ?>>
                                        <?= htmlspecialchars($n['id_parcela']) ?>
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
                                   value="<?= $fecha_vencimiento ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la fecha de vencimiento
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="importe" class="form-label fw-bold">Importe</label>
                            <input type="number" class="form-control" id="importe" name="importe" 
                                   value="<?= $importe ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el importe
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="recargo" class="form-label fw-bold">Recargo</label>
                            <input type="number" class="form-control" id="recargo" name="recargo" 
                                   value="<?= $recargo ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el recargo
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total" class="form-label fw-bold">Total</label>
                            <input type="number" class="form-control" id="total" name="total" 
                                   value="<?= $total ?>" required>
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


<!-- Agrega este script para la validación del formulario -->
<script>
// Ejemplo de validación de Bootstrap
(function () {
  'use strict'

  // Selecciona todos los formularios a los que queremos aplicar estilos de validación de Bootstrap
  var forms = document.querySelectorAll('.needs-validation')

  // Bucle sobre ellos y evitar el envío
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