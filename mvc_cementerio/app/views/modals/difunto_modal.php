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
                                <label for="deudo" class="form-label fw-bold">Deudo</label>
                                <select class="form-select" id="deudo" name="deudo" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['deudos'] as $n): 
                                        $selected = ($id_deudo ?? '') == $n['id_deudo'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $n['id_deudo'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($n['nombre']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">Por favor seleccione un deudo</div>
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
