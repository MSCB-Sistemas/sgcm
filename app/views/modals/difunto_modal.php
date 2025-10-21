<div class="modal fade" id="modalDifunto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear difunto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoDifunto" action="<?= URL ?>/difunto/save" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deudo_search_in_modal" class="form-label fw-bold">Deudo</label>
                                <div class="input-group">
                                    <input list="deudos" id="deudo_search_in_modal" class="form-control" placeholder="Ingrese un deudo" autocomplete="off" required>
                                    <input type="hidden" id="id_deudo_in_modal" name="id_deudo">
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
                                    <?php foreach ($datos['sexos'] as $n): ?>
                                        <option value="<?= $n['id_sexo'] ?>" 
                                            <?php 
                                                if (isset($id_sexo) && $id_sexo == $n['id_sexo']) { 
                                                    echo 'selected'; 
                                                } 
                                            ?>>
                                            <?= htmlspecialchars($n['descripcion']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nacionalidad" class="form-label fw-bold">Nacionalidad</label>
                                <select class="form-select" id="nacionalidad" name="nacionalidad">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['nacionalidades'] as $n): ?>
                                        <option value="<?= $n['id_nacionalidad'] ?>"
                                            <?php 
                                                if (isset($id_nacionalidad) && $id_nacionalidad == $n['id_nacionalidad']) {
                                                    echo 'selected';
                                                }
                                            ?>>
                                            <?= htmlspecialchars($n['nacionalidad']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="estado_civil" class="form-label fw-bold">Estado civil</label>
                                <select class="form-select" id="estado_civil" name="estado_civil">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['estados_civiles'] as $n): ?>
                                        <option value="<?= $n['id_estado_civil'] ?>"
                                            <?php 
                                                if (isset($id_estado_civil) && $id_estado_civil == $n['id_estado_civil']) {
                                                    echo 'selected';
                                                }
                                            ?>>
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
