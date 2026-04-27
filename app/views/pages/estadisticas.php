<link rel="stylesheet" href="<?= URL . '/public/css/estadisticas.css' ?>">
<ul class="nav nav-tabs sticky-top sticky-tabs-container" id="myTab" role="tablist">
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
            <!-- Capacidad y Ocupación -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-acento text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Capacidad del Recinto</h6>
                    </div>
                    <div class="card-body">
                        <?php 
                        $totalParcelas = $datos['total_parcelas_generales'] ?? 0;
                        $ocupadas = $datos['total_parcelas'] ?? 0;
                        $libres = max(0, $totalParcelas - $ocupadas);
                        $porcentaje = $totalParcelas > 0 ? round(($ocupadas / $totalParcelas) * 100) : 0;
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Nivel de Ocupación</span>
                                <span class="fw-bold"><?= $porcentaje ?>%</span>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 10px;">
                                <div class="progress-bar <?= $porcentaje > 90 ? 'bg-danger' : ($porcentaje > 70 ? 'bg-primary' : 'bg-acento') ?>" role="progressbar" style="width: <?= $porcentaje ?>%;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total de Parcelas Registradas
                                <span class="badge bg-light text-dark rounded-pill border"><?= $totalParcelas ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Parcelas Ocupadas
                                <span class="badge bg-acento rounded-pill"><?= $ocupadas ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Parcelas Libres
                                <span class="badge bg-primary rounded-pill"><?= $libres ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i>Resumen Financiero</h6>
                    </div>
                    <div class="card-body">
                        <?php 
                        $deudaEstimada = $datos['deuda_estimada'] ?? 0;
                        $ingresosMes = $datos['ingresos_mes'] ?? 0;
                        $totalMorosos = $datos['total_morosos'] ?? 0;
                        ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Ingresos del Mes Actual
                                <span class="fw-bold text-success">$<?= number_format($ingresosMes, 2, ',', '.') ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Deudores Morosos Activos
                                <span class="badge bg-danger rounded-pill"><?= $totalMorosos ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Deuda Estimada Pendiente</span>
                                <span class="fw-bold text-danger">$<?= number_format($deudaEstimada, 2, ',', '.') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actividad de Registro -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Actividad de Registro Histórica</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <div class="d-flex justify-content-between align-items-center p-2">
                                    <span>Personas Fallecidas Registradas</span>
                                    <span class="fs-4 fw-bold text-acento"><?= isset($datos['total_difuntos']) ? $datos['total_difuntos'] : 0 ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center p-2">
                                    <span>Traslados Realizados</span>
                                    <span class="fs-4 fw-bold text-primary"><?= isset($datos['total_traslados']) ? $datos['total_traslados'] : 0 ?></span>
                                </div>
                            </div>
                        </div>
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