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
            $campos_html = ['nombre', 'apellido', 'dni', 'edad', 'fecha_fallecimiento', 'domicilio', 'localidad', 'codigo_postal'];
            $campos_directos = ['id_deudo', 'id_sexo', 'id_nacionalidad', 'id_estado_civil'];

            foreach ($campos_html as $campo) {
                if (isset($datos['values'][$campo])) {
                    $$campo = htmlspecialchars($datos['values'][$campo]);
                } else {
                    $$campo = '';
                }
            }

            foreach ($campos_directos as $campo) {
                if (isset($datos['values'][$campo])) {
                    $$campo = $datos['values'][$campo];
                } else {
                    $$campo = '';
                }
            }
            ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Primera columna -->
                    <div class="col-md-6">   
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= $nombre ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el nombre
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="apellido" class="form-label fw-bold">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                   value="<?= $apellido ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el apellido
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dni" class="form-label fw-bold">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" 
                                   value="<?= $dni ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese el DNI
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edad" class="form-label fw-bold">Edad</label>
                            <input type="text" class="form-control" id="edad" name="edad" 
                                   value="<?= $edad ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha_fallecimiento" class="form-label fw-bold">Fecha fallecimiento</label>
                            <input type="date" class="form-control" id="fecha_fallecimiento" name="fecha_fallecimiento" 
                                   value="<?= $fecha_fallecimiento ?>">
                        </div>
                    </div>
                    
                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sexo" class="form-label fw-bold">Género</label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['sexos'] as $n): ?>
                                    <?php
                                    $selected = '';
                                    if ($id_sexo == $n['id_sexo']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $n['id_sexo'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($n['descripcion']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un género
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nacionalidad" class="form-label fw-bold">Nacionalidad</label>
                            <select class="form-select" id="nacionalidad" name="nacionalidad" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['nacionalidades'] as $n): ?>
                                    <?php
                                    $selected = '';
                                    if ($id_nacionalidad == $n['id_nacionalidad']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $n['id_nacionalidad'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($n['nacionalidad']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione una nacionalidad
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="estado_civil" class="form-label fw-bold">Estado civil</label>
                            <select class="form-select" id="estado_civil" name="estado_civil" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['estados_civiles'] as $n): ?>
                                    <?php
                                    $selected = '';
                                    if ($id_estado_civil == $n['id_estado_civil']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $n['id_estado_civil'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($n['descripcion']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor seleccione un estado civil
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="domicilio" class="form-label fw-bold">Domicilio</label>
                            <input type="text" class="form-control" id="domicilio" name="domicilio" 
                                   value="<?= $domicilio ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="localidad" class="form-label fw-bold">Localidad</label>
                            <input type="text" class="form-control" id="localidad" name="localidad" 
                                   value="<?= $localidad ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="codigo_postal" class="form-label fw-bold">Código postal</label>
                            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" 
                                   value="<?= $codigo_postal ?>">
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>difunto" class="btn btn-outline-secondary me-md-2">
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

<!-- Script para validación -->
<script>
// Ejemplo de validación de Bootstrap
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