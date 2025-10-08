<div class="modal fade" id="modalDifunto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear difunto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDifunto">
                    <div class="row">
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
                                <label for="nombre" class="form-label fw-bold">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>

                            <div class="mb-3">
                                <label for="apellido" class="form-label fw-bold">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido">
                            </div>

                            <div class="mb-3">
                                <label for="dni" class="form-label fw-bold">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni">
                            </div>

                            <div class="mb-3">
                                <label for="edad" class="form-label fw-bold">Edad</label>
                                <input type="text" class="form-control" id="edad" name="edad">
                            </div>

                            <div class="mb-3">
                                <label for="fecha_fallecimiento" class="form-label fw-bold">Fecha fallecimiento</label>
                                <input type="date" class="form-control" id="fecha_fallecimiento" name="fecha_fallecimiento">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sexo" class="form-label fw-bold">Género</label>
                                <select class="form-select" id="sexo" name="sexo">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['sexos'] as $n): 
                                        $selected = ($id_sexo ?? '') == $n['id_sexo'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $n['id_sexo'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($n['descripcion']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nacionalidad" class="form-label fw-bold">Nacionalidad</label>
                                <select class="form-select" id="nacionalidad" name="nacionalidad">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['nacionalidades'] as $n): 
                                        $selected = ($id_nacionalidad ?? '') == $n['id_nacionalidad'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $n['id_nacionalidad'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($n['nacionalidad']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="estado_civil" class="form-label fw-bold">Estado civil</label>
                                <select class="form-select" id="estado_civil" name="estado_civil">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['estados_civiles'] as $n): 
                                        $selected = ($id_estado_civil ?? '') == $n['id_estado_civil'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $n['id_estado_civil'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($n['descripcion']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="domicilio" class="form-label fw-bold">Domicilio</label>
                                <input type="text" class="form-control" id="domicilio" name="domicilio">
                            </div>

                            <div class="mb-3">
                                <label for="localidad" class="form-label fw-bold">Localidad</label>
                                <input type="text" class="form-control" id="localidad" name="localidad">
                            </div>

                            <div class="mb-3">
                                <label for="codigo_postal" class="form-label fw-bold">Código postal</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
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
        const formDifunto = document.getElementById('formDifunto');
        if (formDifunto) {
            setupModalAJAX('formDifunto', 'ajax_guardar_difunto.php','difuntos','difunto_search',
                data => `${data.difunto.dni} - ${data.difunto.nombre} ${data.difunto.apellido}`);
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
