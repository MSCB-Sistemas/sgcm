<div class="container mt-5">
    <h2><?= $datos['title'] ?></h2>
    
    <?php if (!empty($datos['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($datos['errores'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php    
        if (isset($datos['values']['id_parcela'])) {
            $id_parcela_seleccionada = $datos['values']['id_parcela'];
        } else {
            $id_parcela_seleccionada = '';
        }

       
        if (isset($datos['values']['id_difunto'])) {
            $id_difunto_seleccionado = $datos['values']['id_difunto'];
        } else {
            $id_difunto_seleccionado = '';
        }

       
        if (isset($datos['values']['fecha_ingreso'])) {
            $fecha_ingreso = htmlspecialchars($datos['values']['fecha_ingreso']);
        } else {
            $fecha_ingreso = '';
        }

        if (isset($datos['values']['fecha_retiro'])) {
            $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
        } else {
            $fecha_retiro = '';
        }
    ?>

    
    <form action="<?= $datos['action'] ?>" method="POST">
          <div class="mb-3">
                <label for="parcela_search" class="form-label">Parcela</label>
                <input list="parcelas" id="parcela_search" name="parcela_search" class="form-control" placeholder="Buscar parcela..." autocomplete="off" required>
                <input type="hidden" name="id_parcela" id="id_parcela">

                <datalist id="parcelas">
                    <?php foreach ($datos['parcelas'] as $p): ?>
                    
                        <option value="<?= htmlspecialchars($p['id_parcela'] . ' - ' . $p['id_tipo_parcela'] .
                            ' - ' . $p['numero_ubicacion'] . ' - | ' . $p['hilera'] . ' | ' .
                            $p['seccion'] . ' | ' . $p['fraccion'] . ' | ' . $p['nivel'] . ' |') ?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
                <script>
                    const input = document.getElementById('parcela_search');
                    const hidden = document.getElementById('id_parcela');
                    const options = Array.from(document.querySelectorAll('#parcelas option'));

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
                            input.setCustomValidity("Debe seleccionar una parcela de la lista");
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
            <label for="difunto_search" class="form-label">Difunto</label>
            <input list="difuntos" id="difunto_search" name="difunto_search" class="form-control" placeholder="Buscar difunto..." autocomplete="off" required>
            <input type="hidden" name="id_difunto" id="id_difunto">

            <datalist id="difuntos">
                <?php foreach ($datos['difuntos'] as $d): ?>
                    <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>"
                    data-id="<?= $d['id_difunto'] ?>">
                <?php endforeach; ?>
            </datalist>
            <!-- SCRIPT PARA QUE FUNCIONE EL DATALIST Y ENVIE LA ID CORRECTA -->
            <script>
                const input = document.getElementById('difunto_search');
                const hidden = document.getElementById('id_difunto');
                const options = Array.from(document.querySelectorAll('#difuntos option'));

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
                        input.setCustomValidity("Debe seleccionar un difunto de la lista");
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
            <label for="fecha_ingreso" class="form-label">Fecha ingreso</label>
            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" 
                   value="<?= $fecha_ingreso ?>">
        </div>

        <div class="mb-3">
            <label for="fecha_retiro" class="form-label">Fecha retiro</label>
            <input type="date" class="form-control" id="fecha_retiro" name="fecha_retiro" 
                   value="<?= $fecha_retiro ?>">
        </div>

        <div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= URL ?>ubicacion" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
