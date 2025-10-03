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
</script>