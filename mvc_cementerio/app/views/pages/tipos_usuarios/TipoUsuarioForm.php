<?php
$id_tipo_usuario = '';
if (isset($values['id_tipo_usuario'])) {
    $id_tipo_parcela = $values['id_tipo_usuario'];
}
$descripcion= '';
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
                    <h5 class="alert-heading">Errores encontrados</h5>
                    <ul class="mb-0">
                        <?php foreach ($datos['errores'] as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?= $datos['action'] ?>" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_tipo_usuario" value="<?= $id_tipo_parcela ?>">
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-bold">Nombre de Tipo de Usuario</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" 
                           value="<?= $descripcion ?>" required>
                    <div class="invalid-feedback">
                        Por favor ingrese un nombre válido para el tipo de usuario
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>tipoUsuario" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-arrow-left-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>