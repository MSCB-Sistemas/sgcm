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
    include __DIR__ . '/../../partials/_campos_pago.php'; ?>
</div>
