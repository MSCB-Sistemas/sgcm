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
    include __DIR__ . '/../../partials/_campos_pago.php'; ?>
</div>
