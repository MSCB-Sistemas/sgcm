<?php
$id_estado_civil = '';
if (isset($values['id_estado_civil'])) {
    $id_estado_civil = $values['id_estado_civil'];
}
$descripcion = '';
if (isset($datos['values']['descripcion'])) {
    $descripcion = htmlspecialchars($datos['values']['descripcion']);
}
?>

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
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= $id_estado_civil ?>">
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-bold">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" 
                           value="<?= $descripcion ?>" required>
                    <div class="invalid-feedback">
                        Por favor ingrese una descripción
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= URL ?>estadoCivil" class="btn btn-outline-secondary me-md-2">
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