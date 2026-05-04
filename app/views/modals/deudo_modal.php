<div class="modal fade" id="modalDeudo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header rounded-top-4 card-header shadow-sm">
                <h5 class="modal-title">Crear deudo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoDeudo" action="<?= URL ?>/deudo/save" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dni" class="form-label fw-bold">DNI</label>
                                <p class="form-text">Todo junto sin espacios ni puntos</p>
                                <input type="number" class="form-control" id="dni" name="dni" placeholder="Ej: 12345678">
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre</label>
                                <p class="form-text">Solo el nombre del deudo</p>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Juan">
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label fw-bold">Apellido</label>
                                <p class="form-text">Solo el apellido del deudo</p>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ej: Pérez">
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <p class="form-text">Codigo de area sin 0 y numero sin espacios ni guiones</p>
                                <input type="number" class="form-control" id="telefono" name="telefono" placeholder="Ej: 2944123456">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <p class="form-text">Dirección de correo electrónico válida</p>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ej: juan.perez@gmail.com">
                            </div>

                            <div class="mb-3">
                                <label for="domicilio" class="form-label fw-bold">Domicilio</label>
                                <p class="form-text">Dirección completa del deudo</p>
                                <input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Ej: Calle Falsa 123">
                            </div>
                            
                            <div class="mb-3">
                                <label for="localidad" class="form-label fw-bold">Localidad</label>
                                <p class="form-text">Nombre de la localidad donde reside el deudo</p>
                                <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Ej: San Carlos de Bariloche">
                            </div>
                            
                            <div class="mb-3">
                                <label for="codigo_postal" class="form-label fw-bold">Código Postal</label>
                                <p class="form-text">Código postal del domicilio del deudo</p>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="Ej: R8400">
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
