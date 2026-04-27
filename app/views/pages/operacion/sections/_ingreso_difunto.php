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
            <label class="form-label">Parcela de Destino</label>
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
    include __DIR__ . '/../../partials/_campos_pago.php'; ?>
</div>
