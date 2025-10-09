<div class="modal fade" id="modalDeudo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear deudo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDeudo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dni" class="form-label fw-bold">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni">
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
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
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
                                <label for="codigo_postal" class="form-label fw-bold">Código Postal</label>
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
        const formDeudo = document.getElementById('formDeudo');
        if (formDeudo) {
            setupModalAJAX('formDeudo', 'ajax_guardar_deudo.php','deudos','deudo_search',
                data => `${data.deudo.dni} - ${data.deudo.nombre} ${data.deudo.apellido}`);
        }
    });
</script>
