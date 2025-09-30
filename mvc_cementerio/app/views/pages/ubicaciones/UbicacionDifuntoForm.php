<div class="container mt-5">
    <h2><?= $datos['title'] ?></h2>
    
    <?php if (!empty($datos['errores'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($datos['errores'] as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php    
        if (isset($datos['values']['id_parcela'])) {
            $id_parcela_seleccionada = $datos['values']['id_parcela'];
        } else {
            $id_parcela_seleccionada = '';
        }

       
        if (isset($datos['values']['id_difunto'])) {
            $id_difunto_seleccionado = $datos['values']['id_difunto'];
        } else {
            $id_difunto_seleccionado = '';
        }

       
        if (isset($datos['values']['fecha_ingreso'])) {
            $fecha_ingreso = htmlspecialchars($datos['values']['fecha_ingreso']);
        } else {
            $fecha_ingreso = '';
        }

        if (isset($datos['values']['fecha_retiro'])) {
            $fecha_retiro = htmlspecialchars($datos['values']['fecha_retiro']);
        } else {
            $fecha_retiro = '';
        }
    ?>

    
    <form action="<?= $datos['action'] ?>" method="POST">
        <div class="mb-3">
            <label for="parcela">Parcela</label>
            <div class="input-group">
                <select class="form-select" id="parcela" name="parcela" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['parcelas'] as $n): ?>
                        <?php 
                        if ($id_parcela_seleccionada == $n['id_parcela']) {
                            $selected_parcela = 'selected';
                        } else {
                            $selected_parcela = '';
                        }
                        ?>
                        <option value="<?= $n['id_parcela'] ?>" <?= $selected_parcela ?>>
                            <?= htmlspecialchars($n['id_parcela']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="difunto">Difunto</label>
            <div class="input-group">
                <select class="form-select" id="difunto" name="difunto" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($datos['difuntos'] as $n): ?>
                        <?php 
                        if ($id_difunto_seleccionado == $n['id_difunto']) {
                            $selected_difunto = 'selected';
                        } else {
                            $selected_difunto = '';
                        }
                        ?>
                        <option value="<?= $n['id_difunto'] ?>" <?= $selected_difunto ?>>
                            <?= htmlspecialchars($n['nombre'] . ' ' . $n['apellido']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="fecha_ingreso" class="form-label">Fecha ingreso</label>
            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" 
                   value="<?= $fecha_ingreso ?>">
        </div>

        <div class="mb-3">
            <label for="fecha_retiro" class="form-label">Fecha retiro</label>
            <input type="date" class="form-control" id="fecha_retiro" name="fecha_retiro" 
                   value="<?= $fecha_retiro ?>">
        </div>

        <div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="<?= URL ?>ubicacion" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
