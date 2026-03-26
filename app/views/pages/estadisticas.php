<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">
<ul class="nav nav-tabs sticky-top bg-white" id="myTab" role="tablist" style="z-index: 1000; padding-top: 10px;">
    <li class="nav-item">
        <button class="nav-link" id="integral-tab" data-bs-toggle="tab" data-bs-target="#reporte_integral" type="button" role="tab">Reporte General de Pagos</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="morosos-tab" data-bs-toggle="tab" data-bs-target="#morosos" type="button" role="tab">Deudores Morosos
            <?php if (!empty($datos['total_morosos']) && $datos['total_morosos'] > 0): ?>
                <span class="badge bg-danger ms-1"><?= $datos['total_morosos'] ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="traslados-tab" data-bs-toggle="tab" data-bs-target="#traslados" type="button" role="tab">Traslados</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button" role="tab">Estadísticas</button>
    </li>
</ul>

<div class="tab-content mt-4">
    <?php
    $config = $datos['configIntegral'];
    include 'partials/tabla_ajax_template.php';
    
    $config = $datos['configTraslados'];
    include 'partials/tabla_ajax_template.php';

    $config = $datos['configMorosos'];
    include 'partials/tabla_ajax_template.php';
    ?>

    <div class="tab-pane fade" id="estadisticas" role="tabpanel">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">Registros Generales</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Personas Fallecidas:
                                <strong>
                                    <?php
                                        if (isset($datos['total_difuntos'])) {
                                            echo $datos['total_difuntos'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                            <li class="list-group-item">Parcelas Ocupadas:
                                <strong>
                                    <?php
                                        if (isset($datos['total_parcelas'])) {
                                            echo $datos['total_parcelas'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                            <li class="list-group-item">Traslados Registrados:
                                <strong>
                                    <?php
                                        if (isset($datos['total_traslados'])) {
                                            echo $datos['total_traslados'];
                                        } else {
                                            echo 0;
                                        }
                                    ?>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModalLabel">Registrar Pago para...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= URL ?>/pago/registrarPagoMantenimiento" method="POST">
                <div class="modal-body">
                    <p>Deudor: <strong id="modalDeudorNombre"></strong></p>
                    <p>Parcela: <strong id="modalParcelaId"></strong></p>
                    
                    <input type="hidden" name="deudo_id" id="modalDeudoId">
                    <input type="hidden" name="parcela_id" id="modalParcelaIdInput">

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto a Pagar</label>
                        <input type="number" step="0.01" class="form-control" name="monto" id="monto" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha del Pago</label>
                        <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Nuevo Vencimiento</label>
                        <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= URL ?>';
</script>
<script src="<?= URL ?>/public/js/estadisticas.js"></script>