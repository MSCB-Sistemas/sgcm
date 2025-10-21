<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><?= $datos['title'] ?></h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($datos['errores'])): ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">¡Error!</h5>
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php                
                if (isset($values['id_tipo_operacion'])) {
                    $id_tipo_operacion = $values['id_tipo_operacion'];
                } else {
                    $id_tipo_operacion = '';
                }

                if (isset($datos['values']['descripcion'])) {
                    $descripcion = htmlspecialchars($datos['values']['descripcion']);
                } else {
                    $descripcion = '';
                }
            ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_tipo_operacion" value="<?= $id_tipo_operacion ?>">
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-bold">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" 
                           value="<?= $descripcion ?>" required>
                    <div class="invalid-feedback">
                        Por favor ingrese una descripción válida
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>tipo_operacion" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
