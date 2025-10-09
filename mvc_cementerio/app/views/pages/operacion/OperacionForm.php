<?php include __DIR__ . "/../../modals/deudo_modal.php"; ?>
<?php include __DIR__ . "/../../modals/difunto_modal.php"; ?>
<?php include __DIR__ . "/../../modals/parcela_modal.php"; ?>

<?php if (!empty($datos['errores'])): ?>
    <div class="alert alert-danger shadow-sm">
        <h6 class="alert-heading"><i class="bi bi-x-circle-fill me-2"></i>Errores encontrados</h6>
        <ul class="mb-0">
            <?php foreach ($datos['errores'] as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<div id="alertas-form"></div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-3">
            <label for="tipo_operacion" class="form-label fw-bold">Tipo de Operación</label>
            <select class="form-select" id="tipo_operacion" name="tipo_operacion_selector" required>
                <option value="">Seleccione una operación...</option>
                <?php foreach ($datos['tipo_operaciones'] as $op): ?>
                    <option value="<?= $op['id_tipo_operacion'] ?>">
                        <?= htmlspecialchars($op['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div> 
        <hr>

        <form action="<?= $datos['action'] ?>" method="POST" id="operacionForm">
            <input type="hidden" id="tipo_operacion_id" name="tipo_operacion_id" value="">

            <div id="seccion-1" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3">Traslado Interno</h5>
                <p class="text-muted small">Move un difunto de su parcela actual a una nueva parcela vacía.</p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="difunto_search_traslado" class="form-label">Difunto a trasladar</label>
                        <input list="difuntos" id="difunto_search_traslado" name="id_difunto" class="form-control" placeholder="Buscar difunto...">
                    </div>
                    <div class="col-md-6">
                        <label for="parcela_search_destino" class="form-label">Parcela de Destino (tiene que estar vacía)</label>
                        <input list="parcelas" id="parcela_search_destino" name="id_parcela_destino" class="form-control" placeholder="Buscar parcela de destino...">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                     <div class="col-md-4">
                        <label for="fecha_traslado" class="form-label">Fecha del Traslado</label>
                        <input type="date" class="form-control" name="fecha_traslado" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div id="seccion-4" class="seccion-operacion" style="display:none;">
                <h5 class="mb-3"><i class="bi bi-file-earmark-check"></i> Certificado Libre de Deuda</h5>
                <p class="text-muted small">Verifica el estado de cuenta y genera un certificado para un deudo y su parcela.</p>
                 <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="deudo_search_libredeuda" class="form-label">Deudo</label>
                        <input list="deudos" id="deudo_search_libredeuda" name="id_deudo" class="form-control" placeholder="Buscar deudo...">
                    </div>
                     <div class="col-md-6">
                        <label for="parcela_search_libredeuda" class="form-label">Parcela a verificar</label>
                        <input list="parcelas" id="parcela_search_libredeuda" name="id_parcela" class="form-control" placeholder="Buscar parcela...">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" form="operacionForm" class="btn btn-success">
        <i class="bi bi-save"></i> Procesar Operación
    </button>
    <a href="<?= URL ?>/home" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> Cancelar
    </a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectorOperacion = document.getElementById('tipo_operacion');
    const inputHiddenOperacion = document.getElementById('tipo_operacion_id');

    selectorOperacion.addEventListener('change', function() {
        const seleccion = this.value;

        document.querySelectorAll('.seccion-operacion').forEach(seccion => {
            seccion.style.display = 'none';
        });

        if (seleccion) {
            const seccionVisible = document.getElementById('seccion-' + seleccion);
            if (seccionVisible) {
                seccionVisible.style.display = 'block';
            }
            inputHiddenOperacion.value = seleccion;
        } else {
            inputHiddenOperacion.value = '';
        }
    });

    function configurarAutocompletado(inputId, hiddenId, datalistId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);

        input.addEventListener('input', () => {
            hidden.value = '';
            const val = input.value;
            const options = document.querySelectorAll(`#${datalistId} option`);
            const match = Array.from(options).find(opt => opt.value === val);
            if (match) {
                hidden.value = match.dataset.id;
                input.setCustomValidity("");
            }
        });

        input.addEventListener('blur', () => {
            if (!hidden.value) { 
                input.setCustomValidity("Debe seleccionar un elemento de la lista");
            } else {
                input.setCustomValidity("");
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        configurarAutocompletado('deudo_search', 'id_deudo', 'deudos');
        configurarAutocompletado('parcela_search', 'id_parcela', 'parcelas');
        configurarAutocompletado('difunto_search', 'id_difunto', 'difuntos');
    });

    document.getElementById('parcela_search').addEventListener('change', function() {
        const idParcela = this.value;

        if (!idParcela) {
            document.getElementById('accordionParcelaInfo').innerHTML = '';
            return;
        }

        fetch(`/cementerio/mvc_cementerio/parcela/obtenerInfoParcela/${idParcela}`)
            .then(res => res.json())
            .then(data => {
                console.log(data);
                const accordion = document.getElementById('accordionParcelaInfo')
                accordion.innerHTML = "";

                let pagosHtml = `<div class="accordion-item">
                    <h2 class="accordion-header" id="headingPagos">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagos" aria-expanded="true" aria-controls="collapsePagos">
                            <i class="fas fa-receipt me-2"></i>Pagos Asociados (${data.pagos.length})
                        </button>
                    </h2>
                    <div id="collapsePagos" class="accordion-collapse collapse show" aria-labelledby="headingPagos" data-bs-parent="#accordionParcelaInfo">
                        <div class="accordion-body p-0">`;

                if (data.pagos.length > 0) {
                    pagosHtml += `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">Fecha Pago</th>
                                    <th class="text-nowrap">Fecha Vencimiento</th>
                                    <th class="text-nowrap">Total</th>
                                    <th class="text-nowrap">Deudo</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.pagos.forEach(p => {
                        const estadoClass = p.Deudo === 'Pagado' ? 'badge bg-success' : 'badge bg-danger';
                        pagosHtml += `
                                <tr>
                                    <td class="text-nowrap">${p.fecha_pago}</td>
                                    <td class="text-nowrap">${p.fecha_vencimiento}</td>
                                    <td class="text-nowrap">ARS ${p.total}</td>
                                    <td class="text-nowrap">${p.Deudo}</td>
                                </tr>`;
                    });

                    pagosHtml += `
                            </tbody>
                        </table>
                    </div>`;
                } else {
                    pagosHtml += `
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No hay pagos asociados a esta parcela</p>
                    </div>`;
                }

                pagosHtml += `</div></div></div>`;

                let difuntosHtml = `<div class="accordion-item">
                    <h2 class="accordion-header" id="headingDifuntos">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDifuntos" aria-expanded="false" aria-controls="collapseDifuntos">
                            <i class="fas fa-users me-2"></i>Difuntos Asociados (${data.difuntos.length})
                        </button>
                    </h2>
                    <div id="collapseDifuntos" class="accordion-collapse collapse" aria-labelledby="headingDifuntos" data-bs-parent="#accordionParcelaInfo">
                        <div class="accordion-body p-0">`;

                if (data.difuntos.length > 0) {
                    difuntosHtml += `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">DNI</th>
                                    <th class="text-nowrap">Nombre Completo</th>
                                    <th class="text-nowrap">Apellido</th>
                                    <th class="text-nowrap">Fecha Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.difuntos.forEach(d => {
                        difuntosHtml += `
                                <tr>
                                    <td class="text-nowrap">${d.dni}</td>
                                    <td class="text-nowrap fw-bold">${d.nombre}</td>
                                    <td class="text-nowrap">${d.apellido}</td>
                                    <td class="text-nowrap">${d.fecha_ubicacion}</td>
                                </tr>`;
                    });

                    difuntosHtml += `
                            </tbody>
                        </table>
                    </div>`;
                } else {
                    difuntosHtml += `
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No hay difuntos registrados en esta parcela</p>
                    </div>`;
                }

                difuntosHtml += `</div></div></div>`;
                accordion.innerHTML = pagosHtml + difuntosHtml;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('accordionParcelaInfo').innerHTML = `
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>Error al cargar la información de la parcela.</div>
                    </div>`;
            });
    });

    document.getElementById("deudo_search").addEventListener("change", function () {
        const idDeudo = document.getElementById("id_deudo").value;
        if (idDeudo) {
            fetch(`/deudo/verificar/${idDeudo}`)
                .then(res => res.json())
                .then(data => {
                    mostrarAdvertencia(data.advertencias);
                });
        }
    });

    document.getElementById("difunto_search").addEventListener("change", function () {
        const idDifunto = document.getElementById("id_difunto").value;
        if (idDifunto) {
            fetch(`/difunto/verificar/${idDifunto}`)
                .then(res => res.json())
                .then(data => {
                    mostrarAdvertencia(data.advertencias);
                });
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        const monto = document.getElementById("importe");
        const recargo = document.getElementById("recargo");
        const total = document.getElementById("total");

        function calcularTotal() {
            const montoVal = parseFloat(monto.value) || 0;
            const recargoVal = parseFloat(recargo.value) || 0;
            const totalVal = montoVal + (montoVal * recargoVal / 100);
            total.value = totalVal.toFixed(2);
        }

        monto.addEventListener("input", calcularTotal);
        recargo.addEventListener("input", calcularTotal);
    });
});
</script>