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

        <div class="mb-3">
            <label for="tipo_operacion_selector" class="form-label fw-bold">Tipo de Operación</label>
            <select class="form-select" id="tipo_operacion_selector" required>
                <option value="">Seleccione una operación...</option>
                <?php foreach ($datos['tipo_operaciones'] as $op): ?>
                    <option value="<?= $op['id_tipo_operacion'] ?>">
                        <?= htmlspecialchars($op['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr>

        <form action="<?= $datos['action'] ?>" method="POST" id="operacionForm">
            <input type="hidden" name="tipo_operacion" id="tipo_operacion_hidden">

            <!-- Traslado Interno (Exhumación) -->
            <div id="seccion-1" class="seccion-operacion" data-prefix="ti" style="display:none;">
                <h5 class="mb-3">Traslado Interno</h5>
                <p class="text-muted small">Mueve un difunto de su ubicacion actual a una nueva parcela vacía.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a trasladar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_ti" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_ti" id="id_difunto_ti">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela de Destino (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_ti" class="form-control"
                                placeholder="Buscar parcela de destino...">
                            <input type="hidden" name="id_parcela_ti" id="id_parcela_ti">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha del Traslado</label>
                        <input type="date" class="form-control" name="fecha_traslado_ti" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'ti';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Traslado Externo (Exhumación) -->
            <div id="seccion-2" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Traslado Externo (Exhumación)</h5>
                <p class="text-muted small">Registra el retiro de un difunto del cementerio.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a exhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_te" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_te" id="id_difunto_te">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Exhumación</label>
                        <input type="date" class="form-control" name="fecha_exhumacion_te"
                            value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <!-- Persona bajos recursos -->
            <div id="seccion-3" class="seccion-operacion" data-prefix="br" style="display:none;">
                <h5 class="mb-3">Autorización para Personas de Bajos Recursos</h5>
                <p class="text-muted small">Registra un nuevo ingreso en una parcela asignada.</p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a inhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_br" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_br" id="id_difunto_br">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela Asignada (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_br" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_br" id="id_parcela_br">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php $prefix = 'br';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Libre de deuda -->
            <div id="seccion-4" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Estado de Deuda / Libre de Deuda</h5>
                <p class="text-muted small">Verifica el estado de cuenta general de un deudo e imprime su certificado correspondiente.</p>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="deudo_search_ld" class="form-label fw-bold">Deudo a verificar</label>
                        <div class="input-group">
                            <input list="deudos" id="deudo_search_ld" class="form-control"
                                placeholder="Buscar deudo y corroborar deuda..." required>
                            <input type="hidden" name="id_deudo_ld" id="id_deudo_ld" required>
                        </div>
                    </div>
                </div>
                <div id="info_deuda_ld" class="mt-4">
                    <!-- Dinamically injected from JS -->
                </div>
            </div>

            <!-- Ingreso de Difunto -->
            <div id="seccion-5" class="seccion-operacion" data-prefix="in" style="display:none;">
                <h5 class="mb-3">Ingreso de Difunto</h5>
                <p class="text-muted small">Registra la primera inhumación de un difunto en una parcela vacía.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Difunto a inhumar</label>
                        <div class="input-group">
                            <input list="difuntos" id="difunto_search_in" class="form-control"
                                placeholder="Buscar difunto...">
                            <input type="hidden" name="id_difunto_in" id="id_difunto_in">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalDifunto"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parcela de Destino (debe estar vacía)</label>
                        <div class="input-group">
                            <input list="parcelasDisponibles" id="parcela_search_in" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_in" id="id_parcela_in">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalParcela"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Ingreso</label>
                        <input type="date" class="form-control" name="fecha_ingreso_in" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'in';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>

            <!-- Renovacion de Pago -->
            <div id="seccion-6" class="seccion-operacion" data-prefix="rp" style="display:none;">
                <h5 class="mb-3">Renovación de Pago</h5>
                <p class="text-muted small">Registra la renovación del pago de una parcela ocupada.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Parcela a Renovar</label>
                        <div class="input-group">
                            <input list="parcelasOcupadas" id="parcela_search_rp" class="form-control"
                                placeholder="Buscar parcela...">
                            <input type="hidden" name="id_parcela_rp" id="id_parcela_rp">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Renovación</label>
                        <input type="date" class="form-control" name="fecha_renovacion_rp"
                            value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <?php $prefix = 'rp';
                include __DIR__ . '/../partials/_campos_pago.php'; ?>
            </div>
        </form>
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

<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" form="operacionForm" class="btn btn-success"><i class="bi bi-save"></i> 
        Guardar Operación
    </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const URL_INFO_PARCELA = "<?= URL ?>parcela/obtenerInfoParcela/";
    const URL_INFO_DEUDA = "<?= URL ?>operacion/obtenerDeudaDeudo/";
</script>
<script src="<?= URL ?>public/js/operacion_form.js"></script>