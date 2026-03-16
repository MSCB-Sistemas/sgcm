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
                        <label for="deudo_search" class="form-label">Deudo</label>
                <input list="deudos" id="deudo_search" name="deudo_search" class="form-control" placeholder="Buscar deudo..." autocomplete="off" required>
                <input type="hidden" name="id_deudo" id="id_deudo">

                <datalist id="deudos">
                    <?php foreach ($datos['deudos'] as $d): ?>
                        <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['apellido'] . ' ' . $d['nombre']) ?>" data-id="<?= $d['id_deudo'] ?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
                <script>
    const input = document.getElementById('deudo_search');
    const hidden = document.getElementById('id_deudo');
    const options = Array.from(document.querySelectorAll('#deudos option'));

    // Guardamos tanto el texto como el ID en la memoria inicial
    let lastValidValue = input.value.trim(); 
    let lastValidId = hidden.value; 

    // EVENTO 1: Mientras el usuario escribe o selecciona de la lista
    input.addEventListener('input', () => {
        const val = input.value.trim();
        let valid = false;
        let newId = '';

        options.some(opt => {
            // Comparamos asegurando que no haya espacios fantasma en la base de datos
            if (opt.value.trim() === val) {
                valid = true;
                newId = opt.dataset.id;
                return true; 
            }
        });

        if (valid) {
            // ¡Match exacto! Actualizamos ID, memoria de texto y limpiamos errores
            hidden.value = newId;
            lastValidId = newId;
            lastValidValue = val; 
            input.setCustomValidity(""); 
        } else {
            // Mientras escribe mal, mantenemos el ID viejo pero marcamos el error rojo
            hidden.value = lastValidId;
            input.setCustomValidity("Debe seleccionar un deudo de la lista");
        }
    });

    // EVENTO 2: Cuando hace clic afuera (blur)
    input.addEventListener('blur', () => {
        const val = input.value.trim();
        const isValid = options.some(opt => opt.value.trim() === val);

        if (!isValid) {
            // AUTO-CORRECCIÓN: Si hizo clic afuera y lo que dejó no es válido,
            // le restauramos el último texto correcto que había elegido.
            input.value = lastValidValue;
            hidden.value = lastValidId;
            
            // Le sacamos el error rojo (ya que lo arreglamos por él)
            // Si lastValidValue estaba vacío, el 'required' del HTML hará su trabajo normal.
            input.setCustomValidity(""); 
        }
    });
</script>
                </div>        
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