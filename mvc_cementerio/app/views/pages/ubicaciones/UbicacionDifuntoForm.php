<?php
$id_parcela = '';
if (isset($values['id_parcela'])) {
    $id_parcela = $values['id_parcela'];
}
$id_difunto= '';
if (isset($datos['values']['id_difunto'])) {
    $id_difunto = htmlspecialchars($datos['values']['id_difunto']);
}
$fecha_ingreso= '';
if (isset($datos['values']['fecha_ingreso'])) {
    $fecha_ingreso = htmlspecialchars($datos['values']['fecha_ingreso']);
}
$fecha_retiro= '';
if (isset($datos['values']['fecha_retiro'])) {
    $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
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
            <label for="parcela">Parcela</label>
            <div class="input-group">
            <select class="form-select" id="parcela" name="parcela" required>
                <option value="">Seleccione...</option>
                    <?php foreach ($datos['parcelas'] as $n): ?>
                        <option value="<?= $n['id_parcela'] ?>"
                            value="<?= $id_parcela ?>" required>
                            <?= htmlspecialchars($n['id_parcela']) ?>
                        </option>
                    <?php endforeach ?>
            </select>
            </div>
            <div class="mb-3">
            <label for="difunto">Difunto</label>
            <div class="input-group">
            <select class="form-select" id="difunto" name="difunto" required>
                <option value="">Seleccione...</option>
                    <?php foreach ($datos['difuntos'] as $n): ?>
                        <option value="<?= $n['id_difunto'] ?>"
                            value="<?= $id_difunto ?>" required>
                            <?= htmlspecialchars($n['nombre'] . ' ' . $n['apellido']) ?>
                        </option>
                    <?php endforeach ?>
            </select>
            </div>
            <div class="mb-3">
            <label for="fecha_ingreso" class="form-label">Fecha ingreso</label>
            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" 
                   value="<?= $fecha_ingreso ?>" required>
            </div>
            <div class="mb-3">
            <label for="fecha_retiro" class="form-label">Fecha retiro</label>
            <input type="date" class="form-control" id="fecha_retiro" name="fecha_retiro" 
                   value="<?= $fecha_retiro ?>" required>
        </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= URL ?>ubicacion" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>