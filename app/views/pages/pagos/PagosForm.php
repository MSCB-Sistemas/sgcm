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
            $campos_html = ['fecha_pago', 'fecha_vencimiento', 'importe', 'recargo', 'total'];

            $campos_directos = [
                'id_deudo' => 'id_deudo_selected',
                'id_parcela' => 'id_parcela_selected'
            ];

            foreach ($campos_html as $campo) {
                if (isset($datos['values'][$campo])) {
                    $$campo = htmlspecialchars($datos['values'][$campo]);
                } else {
                    $$campo = '';
                }
            }

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
                             <!-- Scrip -->
                        </div>
                               
                            

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
                                   value="<?php
                                        if (isset($datos['values']['fecha_vencimiento'])) {
                                            echo htmlspecialchars($datos['values']['fecha_vencimiento']);
                                        } else {
                                            echo '';
                                        }?>" required>
                            <div class="invalid-feedback">
                                Por favor ingrese la fecha de vencimiento
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="importe" class="form-label fw-bold">Importe</label>
                            <input type="number" class="form-control" id="importe" name="importe"
                                value="<?php
                                    if (isset($datos['values']['importe'])) {
                                        echo htmlspecialchars($datos['values']['importe']);
                                    } else {
                                        echo '';
                                    }
                                ?>" required oninput="calcularTotal()">
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
                                value="<?php
                                    if (isset($datos['values']['vinculo_familiar'])) {
                                        echo htmlspecialchars($datos['values']['vinculo_familiar']);
                                    } else {
                                        echo '';
                                    } 
                                ?>">
                            <div class="invalid-feedback">
                                Por favor ingrese el vincula familiar
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="responsable_tramite" class="form-label fw-bold">Responsable de tramite</label>
                            <input type="text" class="form-control" id="responsable_tramite" name="responsable_tramite"
                                value="<?php
                                    if (isset($datos['values']['responsable_tramite'])) { 
                                        echo htmlspecialchars($datos['values']['responsable_tramite']); 
                                    } else {
                                        echo '';
                                    } 
                                ?>">
                            <div class="invalid-feedback">
                                Por favor ingrese el responsable del tramite
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="recargo" class="form-label fw-bold">Recargo (%)</label>
                            <input type="number" step="0.01" class="form-control" id="recargo" name="recargo" 
                                value="<?php 
                                    if (isset($datos['values']['recargo'])) { 
                                        echo htmlspecialchars($datos['values']['recargo']); 
                                    } else {
                                        echo '';
                                    }?>" required oninput="calcularTotal()">
                            <div class="invalid-feedback">
                                Por favor ingrese el recargo
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total" class="form-label fw-bold">Total</label>
                            <input type="number" step="0.01" class="form-control" id="total" name="total"
                                value="<?php
                                    if (isset($datos['values']['total'])) { 
                                        echo htmlspecialchars($datos['values']['total']); 
                                    } else {
                                        echo '';
                                    }?>" readonly>
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