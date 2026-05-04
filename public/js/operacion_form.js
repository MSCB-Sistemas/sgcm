document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('input[list]').forEach(input => {
        input.required = true;
        input.setAttribute('autocomplete', 'new-password');
        input.setAttribute('name', Math.random().toString(36).substring(7));
    });

    function configurarAutocompletado(inputId, hiddenId, datalistId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        if (!input || !hidden) return;

        const normalizeText = (str) => {
            if (!str) return '';
            return str.toString()
                .toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                .replace(/[^a-z0-9]/g, '');
        };

        input.addEventListener('input', () => {
            hidden.value = '';
            const valorInputNormalizado = normalizeText(input.value);
            if (valorInputNormalizado === '') return;

            const activeDatalistId = input.getAttribute('list') || datalistId;
            const options = document.querySelectorAll(`#${activeDatalistId} option`);
            for (const option of options) {
                const valorOpcionNormalizado = normalizeText(option.value);
                if (valorOpcionNormalizado === valorInputNormalizado) {
                    hidden.value = option.dataset.id;
                    input.setCustomValidity("");
                    input.dispatchEvent(new Event('change', { 'bubbles': true }));
                    break;
                }
            }
        });

        input.addEventListener('blur', () => {
            if (!hidden.value && input.required) {
                input.setCustomValidity("Debe seleccionar un elemento válido de la lista.");
            } else {
                input.setCustomValidity("");
            }
        });
    }

    // --- 2. FUNCIÓN INFO DINÁMICA (ACCORDEON) ---
    function configurarInfoDinamica(inputId, hiddenId, urlTemplate, accordionId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        let debounceTimer;

        input.addEventListener('change', function () {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                const id = document.getElementById(hiddenId).value;
                const accordion = document.getElementById(accordionId);

                if (!id) {
                    accordion.innerHTML = '';
                    return;
                }

                accordion.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>';

                fetch(urlTemplate + id)
                    .then(res => {
                        if (!res.ok) throw new Error('Error en la respuesta del servidor');
                        return res.json();
                    })
                    .then(data => {
                        let pagosHtml = `<div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePagos">Pagos Asociados (${data.pagos.length})</button></h2><div id="collapsePagos" class="accordion-collapse collapse show"><div class="accordion-body p-0">`;
                        if (data.pagos.length > 0) {
                            pagosHtml += `<table class="table table-sm table-striped mb-0"><thead><tr><th>Fecha Pago</th><th>Vencimiento</th><th>Total</th><th>Deudo</th></tr></thead><tbody>`;
                            data.pagos.forEach(p => {
                                pagosHtml += `<tr><td>${p.fecha_pago}</td><td>${p.fecha_vencimiento}</td><td>ARS ${p.total}</td><td>${p.Deudo}</td></tr>`;
                            });
                            pagosHtml += `</tbody></table>`;
                        } else {
                            pagosHtml += `<p class="text-center text-muted p-3">No hay pagos asociados.</p>`;
                        }
                        pagosHtml += `</div></div></div>`;

                        let difuntosHtml = `<div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDifuntos">Difuntos Asociados (${data.difuntos.length})</button></h2><div id="collapseDifuntos" class="accordion-collapse collapse"><div class="accordion-body p-0">`;
                        if (data.difuntos.length > 0) {
                            difuntosHtml += `<table class="table table-sm table-striped mb-0"><thead><tr><th>DNI</th><th>Nombre</th><th>Apellido</th><th>Fecha Ubicación</th></tr></thead><tbody>`;
                            data.difuntos.forEach(d => {
                                difuntosHtml += `<tr><td>${d.dni}</td><td>${d.nombre}</td><td>${d.apellido}</td><td>${d.fecha_ubicacion}</td></tr>`;
                            });
                            difuntosHtml += `</tbody></table>`;
                        } else {
                            difuntosHtml += `<p class="text-center text-muted p-3">No hay difuntos asociados.</p>`;
                        }
                        difuntosHtml += `</div></div></div>`;

                        accordion.innerHTML = pagosHtml + difuntosHtml;
                    })
                    .catch(err => {
                        console.error("Error al cargar info dinámica:", err);
                        accordion.innerHTML = `<div class="alert alert-danger">Error al cargar los detalles.</div>`;
                    });
            }, 300);
        });
    }

    // --- 3. FUNCIÓN MODALES AJAX (GUARDADO) ---
    function configurarModalAjax(modalId, formId, datalistIds) {
        const ids = Array.isArray(datalistIds) ? datalistIds : [datalistIds];
        const form = document.getElementById(formId);
        const modalEl = document.getElementById(modalId);
        if (!form || !modalEl) return;

        let inputActivo = null;

        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (button) {
                const container = button.closest('.input-group') || button.parentElement;
                inputActivo = container ? container.querySelector('input[list]') : null;
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);
            const url = form.getAttribute('action');

            fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.json().then(data => ({ ok: response.ok, data: data })))
                .then(({ ok, data }) => {
                    if (ok && data.success) {
                        ids.forEach(datalistId => {
                            const datalist = document.getElementById(datalistId);
                            if (datalist && data.newItem) {
                                let exists = false;
                                for (let opt of datalist.options) {
                                    if (opt.dataset.id == data.newItem.id) {
                                        exists = true;
                                        break;
                                    }
                                }
                                if (!exists) {
                                    const option = document.createElement('option');
                                    option.value = data.newItem.text;
                                    option.setAttribute('data-id', data.newItem.id);
                                    datalist.appendChild(option);
                                }
                            }
                        });
                        
                        if (inputActivo && data.newItem) {
                            inputActivo.value = data.newItem.text;
                            
                            const container = inputActivo.closest('.input-group') || inputActivo.parentElement;
                            const hidden = container.querySelector('input[type="hidden"]');
                            
                            if (hidden) {
                                hidden.value = data.newItem.id;
                                console.log(`Asignando ID ${data.newItem.id} al campo ${hidden.id}`);
                            } else {
                                console.error("No se encontró el campo oculto para el input:", inputActivo.id);
                            }
                            
                            const currentList = inputActivo.getAttribute('list');
                            if (currentList) {
                                inputActivo.setAttribute('list', '');
                                inputActivo.setAttribute('list', currentList);
                            }

                            inputActivo.setCustomValidity("");
                            inputActivo.classList.remove('is-invalid');
                            
                            inputActivo.dispatchEvent(new Event('input', { bubbles: true }));
                            
                            if (hidden && !hidden.value) {
                                hidden.value = data.newItem.id;
                            }
                            
                            inputActivo.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        form.reset();
                        form.classList.remove('was-validated');
                    } else {
                        const erroresArr = data.errors || data.errores || ['Ocurrió un error.'];
                        alert('No se pudo guardar:\n' + erroresArr.join('\n'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de comunicación.');
                });
        });
    }

    // --- 4. SELECTOR DE OPERACIONES ---
    const selectorOperacion = document.getElementById('tipo_operacion_selector');
    if (selectorOperacion) {
        selectorOperacion.addEventListener('change', function () {
            const seleccion = this.value;
            document.querySelectorAll('.seccion-operacion').forEach(s => {
                s.style.display = 'none';
                s.querySelectorAll('input, select, textarea').forEach(input => input.disabled = true);
            });

            if (seleccion) {
                const seccionAMostrar = document.getElementById('seccion-' + seleccion);
                if (seccionAMostrar) {
                    seccionAMostrar.style.display = 'block';
                    seccionAMostrar.style.opacity = 0;
                    setTimeout(() => seccionAMostrar.style.opacity = 1, 10);
                    seccionAMostrar.style.transition = 'opacity 0.3s ease-in-out';

                    seccionAMostrar.querySelectorAll('input, select, textarea').forEach(input => input.disabled = false);
                }
                document.getElementById('tipo_operacion_hidden').value = seleccion;
            }
        });

        setTimeout(() => selectorOperacion.dispatchEvent(new Event('change')), 50);
    }

    // --- 5. CONFIGURACIÓN DE AUTOCOMPLETADOS ---
    // Traslado Interno (TI)
    configurarAutocompletado('difunto_search_ti', 'id_difunto_ti', 'difuntos');
    configurarAutocompletado('parcela_search_ti', 'id_parcela_ti', 'todasLasParcelas');
    configurarAutocompletado('deudo_search_ti', 'id_deudo_ti', 'deudos');

    // Traslado Externo (TE)
    configurarAutocompletado('difunto_search_te', 'id_difunto_te', 'difuntos');

    // Persona bajos recursos (BR)
    configurarAutocompletado('difunto_search_br', 'id_difunto_br', 'difuntos');
    configurarAutocompletado('parcela_search_br', 'id_parcela_br', 'parcelasDisponibles');
    configurarAutocompletado('deudo_search_br', 'id_deudo_br', 'deudos');

    // Libre de deuda (LD)
    configurarAutocompletado('deudo_search_ld', 'id_deudo_ld', 'deudos');

    const inputDeudoLd = document.getElementById('deudo_search_ld');
    let deudoDebounce;
    if (inputDeudoLd) {
        inputDeudoLd.addEventListener('change', function () {
            clearTimeout(deudoDebounce);
            deudoDebounce = setTimeout(() => {
                const idDeudo = document.getElementById('id_deudo_ld').value;
                const infoDiv = document.getElementById('info_deuda_ld');
                if (!idDeudo || !infoDiv) {
                    if (infoDiv) infoDiv.innerHTML = '';
                    return;
                }
                if (typeof URL_INFO_DEUDA === 'undefined') return;

                infoDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2">Verificando estado de cuenta...</p></div>';

                fetch(URL_INFO_DEUDA + idDeudo)
                    .then(r => r.json())
                    .then(data => {
                        if (data.ocupadas && data.ocupadas.length > 0) {
                            let html = '<div class="card bg-white border outline shadow-sm"><div class="card-body">';
                            html += '<h6 class="card-title fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Estado de Parcelas de este Deudo</h6>';
                            html += '<table class="table table-sm table-striped border-top mb-3">';
                            html += '<thead class="table-light"><tr><th>Ubicación</th><th>Difunto (Último)</th><th>Estado</th></tr></thead><tbody>';

                            let tieneDeuda = false;
                            data.ocupadas.forEach(p => {
                                let badge = p.tiene_deuda ? '<span class="badge bg-danger">Vencido</span>' : '<span class="badge bg-success">Al Día</span>';
                                if (p.tiene_deuda) tieneDeuda = true;
                                html += `<tr>
                                            <td class="align-middle">${p.tipo_parcela} (Sec. ${p.seccion}, Hil. ${p.hilera})</td>
                                            <td class="align-middle">${p.difunto}</td>
                                            <td class="align-middle">${badge} <small class="text-muted ms-1 text-nowrap">Vto: ${p.fecha_vencimiento}</small></td>
                                         </tr>`;
                            });
                            html += '</tbody></table>';

                            if (tieneDeuda) {
                                html += '<div class="alert alert-danger mb-0 py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Moroso:</strong> El sistema generará un <i>Estado de Deuda</i> reportando los saldos pendientes.</div>';
                            } else {
                                html += '<div class="alert alert-success mb-0 py-2"><i class="bi bi-check-circle-fill me-2"></i> <strong>Al día:</strong> Todo en orden. Se generará certificado de <i>Libre de Deuda</i>.</div>';
                            }

                            html += '</div></div>';
                            infoDiv.innerHTML = html;
                            infoDiv.style.opacity = 0;
                            setTimeout(() => infoDiv.style.opacity = 1, 10);
                            infoDiv.style.transition = 'opacity 0.3s ease';
                        } else {
                            infoDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-info-circle-fill me-2"></i> El deudo no tiene actualmente parcelas asignadas con un difunto en responsablidad.</div>';
                        }
                    })
                    .catch(e => {
                        console.error("Error al cargar deuda:", e);
                        infoDiv.innerHTML = '<div class="alert alert-danger py-2">Error obteniendo estado de deuda. Verifique su conexión y vuelva a intentar.</div>';
                    });
            }, 350);
        });
    }

    document.querySelectorAll('.check-exento-pago').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const prefix = this.dataset.prefix;
            const camposContainer = document.querySelectorAll(`.campos-ocultables-pago_${prefix}`);
            const isExento = this.checked;

            camposContainer.forEach(el => {
                const inputs = el.querySelectorAll('input');
                if (isExento) {
                    el.style.opacity = '0.5';
                    el.style.pointerEvents = 'none';
                    inputs.forEach(i => { i.required = false; i.value = ''; });
                } else {
                    el.style.opacity = '1';
                    el.style.pointerEvents = 'auto';
                }
            });

            const parcelaInput = document.getElementById(`parcela_search_${prefix}`);
            if (parcelaInput) {
                parcelaInput.value = '';
                document.getElementById(`id_parcela_${prefix}`).value = '';
                parcelaInput.setAttribute('list', isExento ? 'todasLasParcelas' : 'parcelasDisponibles');
            }
        });
    });

    // Ingreso de Difunto (IN)
    configurarAutocompletado('difunto_search_in', 'id_difunto_in', 'difuntos');
    configurarAutocompletado('parcela_search_in', 'id_parcela_in', 'parcelasDisponibles');
    configurarAutocompletado('deudo_search_in', 'id_deudo_in', 'deudos');


    // Renovacion de Pago (RP)
    configurarAutocompletado('deudo_search_rp', 'id_deudo_rp', 'deudos');
    configurarAutocompletado('parcela_search_rp', 'id_parcela_rp', 'parcelasOcupadas');

    // --- 6. INFO DINÁMICA ---
    // La variable URL_INFO_PARCELA se define en la vista usando PHP
    if (typeof URL_INFO_PARCELA !== 'undefined') {
        configurarInfoDinamica('parcela_search_ti', 'id_parcela_ti', URL_INFO_PARCELA, 'accordionParcelaInfo');
        configurarInfoDinamica('parcela_search_br', 'id_parcela_br', URL_INFO_PARCELA, 'accordionParcelaInfo');
        configurarInfoDinamica('parcela_search_rp', 'id_parcela_rp', URL_INFO_PARCELA, 'accordionParcelaInfo');
    } else {
        console.error('La constante URL_INFO_PARCELA no est\u00e1 definida.');
    }

    // --- 7. MODALES ---
    configurarModalAjax('modalDifunto', 'formNuevoDifunto', ['difuntos']);
    configurarModalAjax('modalParcela', 'formNuevaParcela', ['parcelasDisponibles', 'todasLasParcelas']);
    configurarModalAjax('modalDeudo', 'formNuevoDeudo', ['deudos']);

    // --- 8. CÁLCULOS DE TOTALES ---
    function configurarCalculoTotal(prefix) {
        const monto = document.getElementById(`importe_${prefix}`);
        const recargo = document.getElementById(`recargo_${prefix}`);
        const total = document.getElementById(`total_${prefix}`);

        function calcular() {
            if (!monto || !recargo || !total) return;
            const montoVal = parseFloat(monto.value) || 0;
            const recargoVal = parseFloat(recargo.value) || 0;
            total.value = (montoVal + (montoVal * recargoVal / 100)).toFixed(2);
        }

        if (monto) monto.addEventListener("input", calcular);
        if (recargo) recargo.addEventListener("input", calcular);
    }

    ['ti', 'br', 'in', 'rp'].forEach(configurarCalculoTotal);
});
