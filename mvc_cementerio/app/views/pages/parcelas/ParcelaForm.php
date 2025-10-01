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
                if (isset($datos['values']['id_tipo_parcela'])) {
                    $id_tipo_parcela_selected = $datos['values']['id_tipo_parcela'];
                } else {
                    $id_tipo_parcela_selected = '';
                }

                if (isset($datos['values']['id_deudo'])) {
                    $id_deudo_selected = $datos['values']['id_deudo'];
                } else {
                    $id_deudo_selected = '';
                }

                if (isset($datos['values']['numero_ubicacion'])) {
                    $numero_ubicacion = htmlspecialchars($datos['values']['numero_ubicacion']);
                } else {
                    $numero_ubicacion = '';
                }

                if (isset($datos['values']['hilera'])) {
                    $hilera = htmlspecialchars($datos['values']['hilera']);
                } else {
                    $hilera = '';
                }

                if (isset($datos['values']['seccion'])) {
                    $seccion = htmlspecialchars($datos['values']['seccion']);
                } else {
                    $seccion = '';
                }

                if (isset($datos['values']['fraccion'])) {
                    $fraccion = htmlspecialchars($datos['values']['fraccion']);
                } else {
                    $fraccion = '';
                }

                if (isset($datos['values']['nivel'])) {
                    $nivel = htmlspecialchars($datos['values']['nivel']);
                } else {
                    $nivel = '';
                }

                if (isset($datos['values']['id_orientacion'])) {
                    $id_orientacion_selected = $datos['values']['id_orientacion'];
                } else {
                    $id_orientacion_selected = '';
                }
            ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_parcela" class="form-label fw-bold">Tipo de parcela</label>
                            <select class="form-select" id="tipo_parcela" name="tipo_parcela" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['tipos_parcelas'] as $n): ?>
                                    <?php
                                    if ($id_tipo_parcela_selected == $n['id_tipo_parcela']) {
                                        $selected_tipo_parcela = 'selected';
                                    } else {
                                        $selected_tipo_parcela = '';
                                    }
                                    ?>
                                    <option value="<?= $n['id_tipo_parcela'] ?>" <?= $selected_tipo_parcela ?>>
                                        <?= htmlspecialchars($n['nombre_parcela']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un tipo de parcela
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deudo" class="form-label fw-bold">Deudo</label>
                            <select class="form-select" id="deudo" name="deudo">
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
                        </div>
                        
                        <div class="mb-3">
                            <label for="numero_ubicacion" class="form-label fw-bold">Número ubicación</label>
                            <input type="text" class="form-control" id="numero_ubicacion" name="numero_ubicacion" 
                                   value="<?= $numero_ubicacion ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el número de ubicación
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hilera" class="form-label fw-bold">Hilera</label>
                            <input type="text" class="form-control" id="hilera" name="hilera" 
                                   value="<?= $hilera ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la hilera
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="seccion" class="form-label fw-bold">Sección</label>
                            <input type="text" class="form-control" id="seccion" name="seccion" 
                                   value="<?= $seccion ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la sección
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fraccion" class="form-label fw-bold">Fracción</label>
                            <input type="text" class="form-control" id="fraccion" name="fraccion" 
                                   value="<?= $fraccion ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la fracción
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nivel" class="form-label fw-bold">Nivel</label>
                            <input type="text" class="form-control" id="nivel" name="nivel" 
                                   value="<?= $nivel ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="orientacion" class="form-label fw-bold">Orientación</label>
                            <select class="form-select" id="orientacion" name="orientacion" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['orientaciones'] as $n): ?>
                                    <?php
                                    if ($id_orientacion_selected == $n['id_orientacion']) {
                                        $selected_orientacion = 'selected';
                                    } else {
                                        $selected_orientacion = '';
                                    }
                                    ?>
                                    <option value="<?= $n['id_orientacion'] ?>" <?= $selected_orientacion ?>>
                                        <?= htmlspecialchars($n['descripcion']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione una orientación
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>parcela" class="btn btn-outline-secondary me-md-2">
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