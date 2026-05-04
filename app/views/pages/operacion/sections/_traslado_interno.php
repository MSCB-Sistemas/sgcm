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
            <label class="form-label">Parcela de Destino</label>
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
    include __DIR__ . '/../../partials/_campos_pago.php'; ?>
</div>
