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
