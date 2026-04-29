<!-- Traslado Externo (Exhumación) -->
<div id="seccion-2" class="seccion-operacion" style="display:none;">
    <h5 class="mb-3">Traslado Externo (Exhumación)</h5>
    <p class="text-muted small">Registra el retiro de un difunto del cementerio.</p>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Difunto a exhumar</label>
            <div class="input-group">
                <input list="difuntos" id="difunto_search_te" class="form-control" placeholder="Buscar difunto...">
                <input type="hidden" name="id_difunto_te" id="id_difunto_te">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha de Exhumación</label>
            <input type="date" class="form-control" name="fecha_exhumacion_te" value="<?= date('Y-m-d'); ?>">
        </div>
    </div>
</div>