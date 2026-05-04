<hr>
<?php if ($prefix === 'in' || $prefix === 'ti' || $prefix === 'br'): ?>
    <div class="form-check form-switch mt-3">
        <input class="form-check-input check-exento-pago" type="checkbox" role="switch" name="exento_pago_<?= $prefix ?>" id="exento_pago_<?= $prefix ?>" data-prefix="<?= $prefix ?>">
        <label class="form-check-label fw-bold text-primary small" for="exento_pago_<?= $prefix ?>">
            Parcela reservada / familiar (No cobrar concesión)
        </label>
    </div>
<?php endif; ?>
<hr>
<h5 class="mb-3">Informacion de Pago</h5>
<p class="text-muted small">Registra el pago de un difunto del cementerio.</p>

<div class="row g-3 mb-3">
    <div class="col-md-6 campo-deudo-pago_<?= $prefix ?>">
        <label for="deudo_search_<?= $prefix ?>" class="form-label">Deudo Responsable</label>
        <div class="input-group">
            <input list="deudos" id="deudo_search_<?= $prefix ?>" class="form-control" placeholder="Buscar deudo por DNI o nombre...">
            
            <input type="hidden" name="id_deudo_<?= $prefix ?>" id="id_deudo_<?= $prefix ?>">
            
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDeudo">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6 campos-ocultables-pago_<?= $prefix ?>">
        <label for="fecha_vencimiento_<?= $prefix ?>" class="form-label">Próximo Vencimiento</label>
        <input type="date" class="form-control" name="fecha_vencimiento_<?= $prefix ?>" id="fecha_vencimiento_<?= $prefix ?>">
    </div>
</div>

<div class="row g-3 campos-ocultables-pago_<?= $prefix ?>">
    <div class="col-md-4">
        <label for="importe_<?= $prefix ?>" class="form-label">Importe</label>
        <input type="number" step="0.01" class="form-control" name="importe_<?= $prefix ?>" id="importe_<?= $prefix ?>">
    </div>
    <div class="col-md-4">
        <label for="recargo_<?= $prefix ?>" class="form-label">Recargo (%)</label>
        <input type="number" step="0.01" class="form-control" name="recargo_<?= $prefix ?>" id="recargo_<?= $prefix ?>" value="0">
    </div>
    <div class="col-md-4">
        <label for="total_<?= $prefix ?>" class="form-label">Total</label>
        <input type="text" class="form-control fw-bold" id="total_<?= $prefix ?>" name="total_<?= $prefix ?>" readonly>
    </div>
</div>
