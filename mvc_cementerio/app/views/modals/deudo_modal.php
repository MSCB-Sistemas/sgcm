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
