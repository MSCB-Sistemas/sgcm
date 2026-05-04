<?php include __DIR__ . "/../../modals/deudo_modal.php"; ?>
<?php include __DIR__ . "/../../modals/difunto_modal.php"; ?>
<?php include __DIR__ . "/../../modals/parcela_modal.php"; ?>

<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger shadow-sm">
        <h6 class="alert-heading"><i class="bi bi-x-circle-fill me-2"></i>Errores encontrados</h6>
        <ul class="mb-0">
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div id="alertas-form"></div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <div class="mb-3 d-none">
            <label for="tipo_operacion_selector" class="form-label fw-bold">Tipo de Operación</label>
            <select class="form-select" id="tipo_operacion_selector" required>
                <option value="">Seleccione una operación...</option>
                <?php foreach ($datos['tipo_operaciones'] as $op): ?>
                    <option value="<?= $op['id_tipo_operacion'] ?>" <?= ($datos['tipo_seleccionado'] == $op['id_tipo_operacion']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($op['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (!empty($datos['tipo_seleccionado'])): ?>
            <h4 class="mb-0 text-primary fw-bold">
                <i class="bi bi-pencil-square me-2"></i>
                <?php 
                    foreach($datos['tipo_operaciones'] as $op) {
                        if($op['id_tipo_operacion'] == $datos['tipo_seleccionado']) {
                            echo htmlspecialchars($op['descripcion']);
                            break;
                        }
                    }
                ?>
            </h4>
            <hr class="mt-2 mb-4">
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Seleccione una operación desde el menú superior para comenzar.
            </div>
        <?php endif; ?>

        <form action="<?= $datos['action'] ?>" method="POST" id="operacionForm" autocomplete="off">
            <input type="hidden" name="tipo_operacion" id="tipo_operacion_hidden" value="<?= $datos['tipo_seleccionado'] ?>">

            <?php 
                if (!empty($datos['tipo_seleccionado'])) {
                    $sections = [
                        1 => '_traslado_interno',
                        2 => '_traslado_externo',
                        3 => '_bajos_recursos',
                        4 => '_libre_deuda',
                        5 => '_ingreso_difunto',
                        6 => '_renovacion_pago'
                    ];
                    $file = $sections[$datos['tipo_seleccionado']] ?? null;
                    if ($file && file_exists(__DIR__ . "/sections/{$file}.php")) {
                        include __DIR__ . "/sections/{$file}.php";
                    } else {
                        echo '<div class="alert alert-warning">No se encontró la vista para esta operación.</div>';
                    }
                }
            ?>
        </form>

        <div id="accordionParcelaInfo" class="accordion mt-4 shadow-sm">
            <!-- Se carga dinámicamente vía JS -->
        </div>
    </div>
</div>

<?php
function format_parcela($p) {
    $id = isset($p['id_parcela']) ? $p['id_parcela'] : '';
    $ubic = isset($p['numero_ubicacion']) ? $p['numero_ubicacion'] : 'S/N';
    $sec = isset($p['seccion']) ? $p['seccion'] : 'S/S';
    $hil = isset($p['hilera']) ? $p['hilera'] : 'S/H';
    return "ID: $id | Ubic: $ubic | Sec: $sec | Hil: $hil";
}

function format_persona($persona) {
    $dni = isset($persona['dni']) ? $persona['dni'] : 'S/DNI';
    $ape = isset($persona['apellido']) ? $persona['apellido'] : '';
    $nom = isset($persona['nombre']) ? $persona['nombre'] : '';
    return strtoupper("$dni - $ape, $nom");
}
?>
<datalist id="parcelasDisponibles">
    <?php foreach ($datos['parcelasDisponibles'] as $p): ?>
        <option value="<?= htmlspecialchars(format_parcela($p)) ?>" data-id="<?= $p['id_parcela'] ?>">
    <?php endforeach; ?>
</datalist>

<datalist id="parcelasOcupadas">
    <?php foreach ($datos['parcelasOcupadas'] as $p): ?>
        <option value="<?= htmlspecialchars(format_parcela($p)) ?>" data-id="<?= $p['id_parcela'] ?>">
    <?php endforeach; ?>
</datalist>

<datalist id="todasLasParcelas">
    <?php foreach ($datos['todasLasParcelas'] as $p): ?>
        <option value="<?= htmlspecialchars(format_parcela($p)) ?>" data-id="<?= $p['id_parcela'] ?>">
    <?php endforeach; ?>
</datalist>

<datalist id="deudos">
    <?php foreach ($datos['deudos'] as $d): ?>
        <option value="<?= htmlspecialchars(format_persona($d)) ?>" data-id="<?= $d['id_deudo'] ?>">
    <?php endforeach; ?>
</datalist>

<datalist id="difuntos">
    <?php foreach ($datos['difuntos'] as $di): ?>
        <option value="<?= htmlspecialchars(format_persona($di)) ?>" data-id="<?= $di['id_difunto'] ?>">
    <?php endforeach; ?>
</datalist>

<?php if (!empty($datos['tipo_seleccionado'])): ?>
<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" form="operacionForm" class="btn btn-success"><i class="bi bi-save"></i> 
        Guardar Operación
    </button>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const URL_INFO_PARCELA = "<?= URL ?>parcela/obtenerInfoParcela/";
    const URL_INFO_DEUDA = "<?= URL ?>operacion/obtenerDeudaDeudo/";
</script>
<script src="<?= URL ?>public/js/operacion_form.js"></script>