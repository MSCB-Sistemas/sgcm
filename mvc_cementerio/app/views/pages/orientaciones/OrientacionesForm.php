<?php
$descripcion = '';
if (isset($datos['values']['descripcion'])) {
    $descripcion = htmlspecialchars($datos['values']['descripcion']);
}
?>

<div class="container mt-5">
    <h2><?= $datos['title']  ?></h2>
    <?php if (!empty($datos['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($datos['errores'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="<?= $datos['action'] ?>" method="POST">
        <div class="mb-3">
            <label for="descripcion">Descripcion</label>
            <input type="text" class="form-control" name="descripcion" id="descripcion"
            value="<?= $descripcion ?>" required>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= URL ?>/orientaciones" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>