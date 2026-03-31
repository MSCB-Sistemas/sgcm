<div class="modal fade" id="modalParcela" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear parcela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaParcela" action="<?= URL ?>/parcela/save" method="POST">
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
                            
                            <!-- <div class="mb-3">
                                <label for="deudo_search_in_modal_parcela" class="form-label fw-bold">Deudo</label>
                                <div class="input-group">
                                    <input list="deudos" id="deudo_search_in_modal_parcela" name="deudo_search" class="form-control" placeholder="Ingrese un deudo" autocomplete="off">
                                    <input type="hidden" id="id_deudo_in_modal_parcela" name="id_deudo">
                                </div>
                                <datalist id="deudos">
                                    <?php foreach ($datos['deudos'] as $d): ?>
                                        <option value="<?= htmlspecialchars($d['dni'] . ' - ' . $d['nombre'] . ' ' . $d['apellido']) ?>"
                                        data-id="<?= $d['id_deudo'] ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div> -->
                            
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