<div class="modal fade" id="modalParcela" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear parcela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDifunto">
                    <div class="row">
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
                                <label for="numero_ubicacion" class="form-label fw-bold">Número ubicación</label>
                                <input type="text" class="form-control" id="numero_ubicacion" name="numero_ubicacion">
                            </div>
                            
                            <div class="mb-3">
                                <label for="hilera" class="form-label fw-bold">Hilera</label>
                                <input type="text" class="form-control" id="hilera" name="hilera">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="seccion" class="form-label fw-bold">Sección</label>
                                <input type="text" class="form-control" id="seccion" name="seccion">
                            </div>
                            
                            <div class="mb-3">
                                <label for="fraccion" class="form-label fw-bold">Fracción</label>
                                <input type="text" class="form-control" id="fraccion" name="fraccion">
                            </div>
                            
                            <div class="mb-3">
                                <label for="nivel" class="form-label fw-bold">Nivel</label>
                                <input type="text" class="form-control" id="nivel" name="nivel">
                            </div>
                            
                            <div class="mb-3">
                                <label for="orientacion" class="form-label fw-bold">Orientación</label>
                                <select class="form-select" id="orientacion" name="orientacion">
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
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setupModalAJAX(formId, endpoint, datalistId, inputId, mapOptionValue) {
        document.getElementById(formId).addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);
            fetch(endpoint, {method:'POST', body:formData})
                .then(res=>res.json())
                .then(data=>{
                    if(data.success){
                        const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                        modal.hide();
                        this.reset();

                        if(datalistId && inputId){
                            const datalist = document.getElementById(datalistId);
                            const option = document.createElement('option');
                            option.value = mapOptionValue(data);
                            option.dataset.id = data[formId==='formDifunto'?'difunto':'deudo']?.id_difunto ?? data[formId==='formDeudo'?'deudo':'parcela']?.id_deudo ?? data.parcela.id_parcela;
                            datalist.appendChild(option);

                            const input = document.getElementById(inputId);
                            input.value = option.value;
                            const hiddenId = inputId.replace('_search','');
                            if(document.getElementById('id_'+hiddenId))
                                document.getElementById('id_'+hiddenId).value = option.dataset.id;
                        }
                        alert(data.mensaje);
                    } else {
                        alert(data.mensaje);
                    }
                }).catch(err=>console.error(err));
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const formParcela = document.getElementById('formParcela');
        if (formParcela) {
            setupModalAJAX('formParcela', 'ajax_guardar_parcela.php','parcelas','parcela_search',
                data => `${data.parcela.id_parcela} - Tipo - ${data.parcela.tipo}`);
        }
    });

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
</script>