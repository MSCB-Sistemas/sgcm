<div class="modal fade" id="modalDifunto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header rounded-top-4 card-header shadow-sm">
                <h5 class="modal-title">Crear difunto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoDifunto" action="<?= URL ?>/difunto/save" method="POST" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre</label>
                                <p class="form-text">Solo el nombre del difunto</p>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Juan" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione un nombre
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="apellido" class="form-label fw-bold">Apellido</label>
                                <p class="form-text">Solo el apellido del difunto</p>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ej: Pérez" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione un apellido
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="dni" class="form-label fw-bold">DNI</label>
                                <p class="form-text">Número de documento sin puntos ni espacios</p>
                                <input type="number" class="form-control" id="dni" name="dni" placeholder="Ej: 12345678" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione un DNI
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edad" class="form-label fw-bold">Edad</label>
                                <p class="form-text">Edad del difunto</p>
                                <input type="number" class="form-control" id="edad" name="edad" placeholder="Ej: 30" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione una edad
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="fecha_fallecimiento" class="form-label fw-bold">Fecha fallecimiento</label>
                                <p class="form-text">Fecha de fallecimiento del difunto</p>
                                <input type="date" class="form-control" id="fecha_fallecimiento"
                                    name="fecha_fallecimiento" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione una fecha de fallecimiento
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sexo" class="form-label fw-bold">Género</label>
                                <p class="form-text">Género del difunto</p>
                                <select class="form-select" id="sexo" name="sexo" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['sexos'] as $n): ?>
                                        <option value="<?= $n['id_sexo'] ?>" <?php
                                          if (isset($id_sexo) && $id_sexo == $n['id_sexo']) {
                                              echo 'selected';
                                          }
                                          ?>>
                                            <?= htmlspecialchars($n['descripcion']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un género
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="nacionalidad" class="form-label fw-bold">Nacionalidad</label>
                                <p class="form-text">Nacionalidad del difunto</p>
                                <select class="form-select" id="nacionalidad" name="nacionalidad" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['nacionalidades'] as $n): ?>
                                        <option value="<?= $n['id_nacionalidad'] ?>" <?php
                                          if (isset($id_nacionalidad) && $id_nacionalidad == $n['id_nacionalidad']) {
                                              echo 'selected';
                                          }
                                          ?>>
                                            <?= htmlspecialchars($n['nacionalidad']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione una nacionalidad
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="estado_civil" class="form-label fw-bold">Estado civil</label>
                                <p class="form-text">Estado civil del difunto</p>
                                <select class="form-select" id="estado_civil" name="estado_civil" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['estados_civiles'] as $n): ?>
                                        <option value="<?= $n['id_estado_civil'] ?>" <?php
                                          if (isset($id_estado_civil) && $id_estado_civil == $n['id_estado_civil']) {
                                              echo 'selected';
                                          }
                                          ?>>
                                            <?= htmlspecialchars($n['descripcion']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un estado civil
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="domicilio" class="form-label fw-bold">Domicilio</label>
                                <p class="form-text">Dirección completa del difunto</p>
                                <input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Ej: Calle Falsa 123" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione un domicilio
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="localidad" class="form-label fw-bold">Localidad</label>
                                <p class="form-text">Nombre de la localidad donde reside el difunto</p>
                                <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Ej: San Carlos de Bariloche" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione una localidad
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="codigo_postal" class="form-label fw-bold">Código postal</label>
                                <p class="form-text">Código postal del domicilio del difunto</p>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="Ej: R8400"
                                    required>
                                <div class="invalid-feedback">
                                    Por favor seleccione un código postal
                                </div>
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