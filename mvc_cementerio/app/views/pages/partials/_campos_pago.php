<hr>
<h5 class="mb-3">Informacion de Pago</h5>
<p class="text-muted small">Registra el pago de un difunto del cementerio.</p>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="deudo_search_<?= $prefix ?>" class="form-label">Deudo Responsable</label>
        <div class="input-group">
            <input list="deudos" id="deudo_search_<?= $prefix ?>" class="form-control" placeholder="Buscar deudo por DNI o nombre...">
            <input type="hidden" name="id_deudo" id="id_deudo_<?= $prefix ?>">
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDeudo">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6">
        <label for="fecha_vencimiento_<?= $prefix ?>" class="form-label">Fecha de Próximo Vencimiento</label>
        <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento_<?= $prefix ?>">
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <label for="importe_<?= $prefix ?>" class="form-label">Importe</label>
        <input type="number" step="0.01" class="form-control" name="importe" id="importe_<?= $prefix ?>">
    </div>
    <div class="col-md-4">
        <label for="recargo_<?= $prefix ?>" class="form-label">Recargo (%)</label>
        <input type="number" step="0.01" class="form-control" name="recargo" id="recargo_<?= $prefix ?>" value="0">
    </div>
    <div class="col-md-4">
        <label for="total_<?= $prefix ?>" class="form-label">Total</label>
        <input type="text" class="form-control fw-bold" id="total_<?= $prefix ?>" name="total" readonly>
    </div>
</div>