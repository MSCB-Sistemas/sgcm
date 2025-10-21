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
            <label for="nacionalidad">Nacionalidad</label>
            <?php
                if (isset($datos['values']['nacionalidad'])) {
                    $nacionalidad_value = htmlspecialchars($datos['values']['nacionalidad']);
                } else {
                    $nacionalidad_value = '';
                }
            ?>
            <input type="text" class="form-control" name="nacionalidad" id="nacionalidad"
            value="<?= $nacionalidad_value ?>" required>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= URL ?>nacionalidades" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
