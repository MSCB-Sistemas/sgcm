<?php
$id_tipo_parcela = '';
if (isset($values['id_tipo_parcela'])) {
    $id_tipo_parcela = $values['id_tipo_parcela'];
}
$nombre_parcela= '';
if (isset($datos['values']['nombre_parcela'])) {
    $nombre_parcela = htmlspecialchars($datos['values']['nombre_parcela']);
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
                <input type="hidden" name="id_tipo_parcela" value="<?= $id_tipo_parcela ?>">
                
                <div class="mb-3">
                    <label for="nombre_parcela" class="form-label fw-bold">Nombre de Tipo de Parcela</label>
                    <input type="text" class="form-control" id="nombre_parcela" name="nombre_parcela" 
                           value="<?= $nombre_parcela ?>" required>
                    <div class="invalid-feedback">
                        Por favor ingrese un nombre válido para el tipo de parcela
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= URL ?>tipoParcela" class="btn btn-outline-secondary me-md-2">
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