// assets/js/pqrs.js
// VERSIÓN CORREGIDA PARA FAQ

console.log('PQRS JS cargado correctamente');

/* =======================
   FAQ (GLOBAL) - VERSIÓN CORREGIDA
   ======================= */
function initFAQ() {
    console.log("=== INICIALIZANDO FAQ ===");
    
    // Esperar a que todo esté completamente cargado
    setTimeout(() => {
        const faqItems = document.querySelectorAll('.faq-item');
        console.log(`Encontrados ${faqItems.length} elementos .faq-item`);
        
        if (faqItems.length === 0) {
            console.warn("No se encontraron elementos .faq-item en el DOM");
            return;
        }

        faqItems.forEach((item, index) => {
            const button = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            
            if (!button || !answer) {
                console.warn(`FAQ ${index + 1}: elementos faltantes`);
                return;
            }

            console.log(`FAQ ${index + 1}: Configurando...`);

            // Limpiar eventos anteriores para evitar duplicados
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Agregar evento al botón nuevo
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log(`CLICK en FAQ ${index + 1}`);
                
                const isActive = item.classList.contains('active');
                console.log(`Estado anterior: ${isActive ? 'ACTIVO' : 'INACTIVO'}`);
                
                // Cerrar todos los otros FAQs (opcional - descomenta si solo quieres uno abierto)
                // faqItems.forEach(otherItem => {
                //     if (otherItem !== item) {
                //         otherItem.classList.remove('active');
                //     }
                // });
                
                // Alternar estado del FAQ actual
                if (isActive) {
                    item.classList.remove('active');
                    console.log(`FAQ ${index + 1} CERRADO`);
                } else {
                    item.classList.add('active');
                    console.log(`FAQ ${index + 1} ABIERTO`);
                }
                
                // Debug del estado final
                console.log(`Clases finales:`, item.className);
                
                // Verificar que los estilos se aplicaron correctamente
                setTimeout(() => {
                    const computedAnswer = window.getComputedStyle(answer);
                    console.log(`Max-height aplicado: ${computedAnswer.maxHeight}`);
                    console.log(`Padding aplicado: ${computedAnswer.padding}`);
                }, 50);
            });

            console.log(`FAQ ${index + 1}: Event listener agregado correctamente`);
        });

        console.log("=== FAQ INICIALIZACIÓN COMPLETA ===");
    }, 200); // Delay para asegurar que CSS esté completamente cargado
}

/* =======================
   SMOOTH SCROLL para enlaces internos
   ======================= */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/* =======================
   AUTO-OCULTAR ALERTAS
   ======================= */
function initAlertas() {
    setTimeout(() => {
        const alertas = document.querySelectorAll('.alerta');
        alertas.forEach(alerta => {
            alerta.style.opacity = '0';
            alerta.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (alerta.parentNode) {
                    alerta.remove();
                }
            }, 300);
        });
    }, 5000);
}

/* =======================
   MAIN: se ejecuta al cargar el DOM
   ======================= */
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM cargado, iniciando PQRS...");
    console.log("Timestamp:", new Date().toLocaleString());
    
    // Inicializar componentes
    initFAQ();
    initSmoothScroll();
    initAlertas();

    // ========================
    // MODAL CONSULTA PQR
    // ========================
    const modalConsulta = document.getElementById("modal");
    const openBtn = document.getElementById("openModal");
    const closeBtn = document.querySelector(".close");
    const formConsulta = document.getElementById("pqr-form");
    const resultadoDiv = document.getElementById("resultado-pqr");

    if (openBtn && modalConsulta) {
        openBtn.addEventListener("click", () => {
            modalConsulta.style.display = "flex";
            if (resultadoDiv) resultadoDiv.innerHTML = "";
        });
    }

    if (closeBtn && modalConsulta) {
        closeBtn.addEventListener("click", () => {
            modalConsulta.style.display = "none";
        });
    }

    if (modalConsulta) {
        window.addEventListener("click", function (event) {
            if (event.target === modalConsulta) {
                modalConsulta.style.display = "none";
            }
        });
    }

    if (formConsulta) {
        formConsulta.addEventListener("submit", function (e) {
            e.preventDefault();
            const cedulaInput = document.getElementById("cedula");
            if (!cedulaInput) return;
            const cedula = cedulaInput.value.trim();
            if (!cedula) {
                alert("Ingresa la cédula.");
                return;
            }

            resultadoDiv.innerHTML = "<p>Cargando...</p>";

            fetch("../controller/consultar_pqr.php?cedula=" + encodeURIComponent(cedula))
                .then(res => res.text())
                .then(html => {
                    resultadoDiv.innerHTML = html;
                    inicializarTablaResultados();
                })
                .catch(() => {
                    resultadoDiv.innerHTML = "<p>Error al consultar.</p>";
                });
        });
    }

    // ========================
    // FORMULARIO PRINCIPAL PQRS
    // ========================
    const formularioPQRS = document.getElementById("formPQRS");

    if (formularioPQRS) {
        console.log('Formulario PQRS encontrado, configurando validaciones...');

        const tipoSelect = document.getElementById('tipo_pqr');
        const asuntoInput = document.getElementById('asunto');
        const mensajeTextarea = document.getElementById('mensaje');
        const medioCheckboxes = document.querySelectorAll('input[name="medio_respuesta[]"]');
        const btnEnviar = document.getElementById('btn-enviar');
        const archivosInput = document.getElementById('archivos');

        actualizarEstadoBoton();

        if (tipoSelect) tipoSelect.addEventListener('change', actualizarEstadoBoton);
        if (asuntoInput) {
            asuntoInput.addEventListener('input', actualizarEstadoBoton);
            asuntoInput.addEventListener('blur', actualizarEstadoBoton);
        }
        if (mensajeTextarea) {
            mensajeTextarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
                actualizarEstadoBoton();
            });
            mensajeTextarea.addEventListener('blur', actualizarEstadoBoton);
        }
        medioCheckboxes.forEach(checkbox => checkbox.addEventListener('change', actualizarEstadoBoton));

        if (archivosInput) {
            archivosInput.addEventListener('change', function () {
                const files = this.files;
                const maxSize = 5 * 1024 * 1024;
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
                for (let i = 0; i < files.length; i++) {
                    const f = files[i];
                    if (f.size > maxSize) { alert(`El archivo "${f.name}" es muy grande. Máximo 5MB por archivo.`); this.value = ''; return; }
                    if (!allowedTypes.includes(f.type)) { alert(`El archivo "${f.name}" no es un formato válido.`); this.value = ''; return; }
                }
            });
        }

        formularioPQRS.addEventListener("submit", function (e) {
            e.preventDefault();
            if (!validarFormularioCompleto()) return false;

            if (btnEnviar) {
                const textoOriginal = btnEnviar.innerHTML;
                btnEnviar.disabled = true;
                btnEnviar.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Enviando...';

                const formData = new FormData(formularioPQRS);

                fetch('../controller/pqrsController.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => {
                        const contentType = response.headers.get('content-type') || '';
                        if (contentType.includes('application/json')) return response.json();
                        return response.text().then(t => { throw new Error('Respuesta no JSON: ' + t.slice(0, 200)); });
                    })
                    .then(data => {
                        if (data.success) {
                            mostrarMensajeExito(data.message || '¡Tu PQRS ha sido enviada exitosamente!');
                            formularioPQRS.reset();
                            actualizarEstadoBoton();
                            setTimeout(() => {
                                window.location.href = data.redirect || '../views/mis_pqrs.php';
                            }, 2000);
                        } else {
                            mostrarErrores(data.errors && data.errors.length ? data.errors : ['Error desconocido al procesar la solicitud']);
                            restaurarBoton(btnEnviar, textoOriginal);
                        }
                    })
                    .catch(error => {
                        console.error('Error en el envío AJAX:', error);
                        mostrarErrores(['Error de conexión o procesamiento.', 'Verifica tu conexión a internet e intenta nuevamente.', 'Si el problema persiste, contacta al administrador.']);
                        restaurarBoton(btnEnviar, textoOriginal);
                    });
            }
            return false;
        });

        function validarFormularioCompleto() {
            const tipo = tipoSelect?.value;
            const asunto = asuntoInput?.value?.trim();
            const mensaje = mensajeTextarea?.value?.trim();
            const medios = document.querySelectorAll('input[name="medio_respuesta[]"]:checked').length;
            if (!tipo) { alert('Debe seleccionar el tipo de solicitud'); tipoSelect?.focus(); return false; }
            if (!asunto || asunto.length < 5) { alert('El asunto debe tener al menos 5 caracteres'); asuntoInput?.focus(); return false; }
            if (!mensaje || mensaje.length < 10) { alert('La descripción debe tener al menos 10 caracteres'); mensajeTextarea?.focus(); return false; }
            if (medios === 0) { alert('Debe seleccionar al menos un medio para recibir respuesta'); return false; }
            return true;
        }
        
        function actualizarEstadoBoton() {
            if (!btnEnviar) return;
            const tipoSel = tipoSelect?.value;
            const asuntoOk = (asuntoInput?.value?.trim().length || 0) >= 5;
            const mensajeOk = (mensajeTextarea?.value?.trim().length || 0) >= 10;
            const medioOk = document.querySelectorAll('input[name="medio_respuesta[]"]:checked').length > 0;
            const ok = tipoSel && asuntoOk && mensajeOk && medioOk;
            btnEnviar.disabled = !ok;
            btnEnviar.classList.toggle('btn-enabled', !!ok);
            btnEnviar.classList.toggle('btn-disabled', !ok);
        }
        
        function restaurarBoton(boton, texto) { 
            if (boton) { 
                boton.disabled = false; 
                boton.innerHTML = texto; 
                actualizarEstadoBoton(); 
            } 
        }
        
        function mostrarMensajeExito(m) { 
            removerMensajesTemporales(); 
            const d = document.createElement('div'); 
            d.className = 'alert alert-success mensaje-temporal'; 
            d.innerHTML = `<i class="ri-check-circle-fill"></i><strong>${m}</strong>`; 
            formularioPQRS.insertBefore(d, formularioPQRS.firstChild); 
            d.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
        }
        
        function mostrarErrores(errs) { 
            removerMensajesTemporales(); 
            let h = '<div class="alert alert-danger mensaje-temporal"><i class="ri-error-warning-fill"></i><strong>Por favor, corrige los siguientes errores:</strong><ul>'; 
            errs.forEach(e => h += `<li>${e}</li>`); 
            h += '</ul></div>'; 
            formularioPQRS.insertAdjacentHTML('afterbegin', h); 
            const n = document.querySelector('.mensaje-temporal'); 
            n && n.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
        }
        
        function removerMensajesTemporales() { 
            document.querySelectorAll('.mensaje-temporal').forEach(m => m.remove()); 
        }
        
        // Auto-remover mensajes del servidor después de 5 segundos
        setTimeout(() => { 
            const ok = document.getElementById('mensaje-exito-servidor'); 
            const er = document.getElementById('mensaje-errores-servidor'); 
            if (ok) { ok.style.opacity = '0'; setTimeout(() => ok.remove(), 500); } 
            if (er) { er.style.opacity = '0'; setTimeout(() => er.remove(), 500); } 
        }, 5000);
    } else {
        console.log('Formulario PQRS no encontrado en esta página');
    }

    // ========================
    // ELIMINAR PQR (delegado)
    // ========================
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-eliminar");
        if (!btn) return;
        const id = btn.dataset.id || btn.getAttribute('data-id');
        if (!id) return;
        if (!confirm("¿Estás seguro de que deseas eliminar esta PQR?")) return;

        fetch("../controller/eliminarPqr.php?id=" + encodeURIComponent(id))
            .then(res => res.text())
            .then(() => { 
                alert("PQR eliminada correctamente."); 
                location.reload(); 
            })
            .catch(() => alert("Error de red al intentar eliminar."));
    });

    // ========================
    // VER / EDITAR PQR (delegado)
    // ========================
    document.addEventListener("click", function (e) {
        const verBtn = e.target.closest(".btn-ver");
        if (verBtn) {
            const id = verBtn.dataset.id || verBtn.getAttribute('data-id');
            if (id) verDetallesCompletos(id);
            return;
        }
        const editBtn = e.target.closest(".btn-editar");
        if (editBtn) {
            const id = editBtn.dataset.id || editBtn.getAttribute('data-id');
            if (id) editarPqr(id);
        }
    });
});

/* =========================================================
   FUNCIONES AUXILIARES
   ========================================================= */

// Tabla de resultados
function inicializarTablaResultados() {
    const tableEl = document.getElementById("tabla-resultado");
    if (!tableEl) return;

    if (typeof $ !== "undefined" && $.fn.DataTable && $.fn.DataTable.isDataTable(tableEl)) {
        $(tableEl).DataTable().destroy();
    }
    if (typeof $ !== "undefined" && $.fn.DataTable) {
        $(tableEl).DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            responsive: true
        });
    }
}

// Modal de detalles completos
function ensureDetallesModal() {
    let modal = document.getElementById('modalDetallesCompletos');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'modalDetallesCompletos';
    modal.className = 'modal';
    modal.style.cssText = 'display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.4);';
    modal.innerHTML = `
        <div class="modal-content" style="background:#fefefe;margin:5% auto;padding:20px;border:2px solid #9D825D;border-radius:10px;width:90%;max-width:800px;max-height:80vh;overflow-y:auto;position:relative;">
            <span class="close-detalles" style="color:#9D825D;float:right;font-size:28px;font-weight:bold;cursor:pointer;">&times;</span>
            <h2 style="margin-top:0;color:#9D825D;">Detalles Completos de PQRS</h2>
            <div id="contenido-detalles-completos"></div>
        </div>`;
    document.body.appendChild(modal);

    modal.querySelector('.close-detalles').onclick = () => modal.style.display = 'none';
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });
    return modal;
}

// Modal de edición
function ensureEditarModal() {
    let modal = document.getElementById('modalEditar');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'modalEditar';
    modal.className = 'modal';
    modal.style.cssText = 'display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.4);';
    modal.innerHTML = `
        <div class="modal-content" style="background:#fefefe;margin:5% auto;padding:20px;border:2px solid #9D825D;border-radius:10px;width:90%;max-width:600px;max-height:80vh;overflow-y:auto;position:relative;">
            <span class="close-editar" style="color:#9D825D;float:right;font-size:28px;font-weight:bold;cursor:pointer;">&times;</span>
            <h2 style="margin-top:0;color:#9D825D;">Editar PQRS</h2>
            <form id="form-editar" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:15px;">
                <input type="hidden" name="id" id="edit-id">
                <div>
                    <label for="edit-tipo" style="display:block;margin-bottom:5px;font-weight:600;color:#333;">Tipo</label>
                    <select name="tipo_pqr" id="edit-tipo" required style="width:100%;padding:8px;border:2px solid #9D825D;border-radius:5px;">
                        <option value="peticion">Petición</option>
                        <option value="queja">Queja</option>
                        <option value="reclamo">Reclamo</option>
                        <option value="sugerencia">Sugerencia</option>
                    </select>
                </div>
                <div>
                    <label for="edit-asunto" style="display:block;margin-bottom:5px;font-weight:600;color:#333;">Asunto</label>
                    <input type="text" name="asunto" id="edit-asunto" required minlength="5" maxlength="255" style="width:100%;padding:8px;border:2px solid #9D825D;border-radius:5px;">
                </div>
                <div>
                    <label for="edit-mensaje" style="display:block;margin-bottom:5px;font-weight:600;color:#333;">Descripción</label>
                    <textarea name="mensaje" id="edit-mensaje" required minlength="10" rows="4" style="width:100%;padding:8px;border:2px solid #9D825D;border-radius:5px;resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="display:block;margin-bottom:5px;font-weight:600;color:#333;">Medio de respuesta</label>
                    <div style="display:flex;gap:15px;">
                        <label style="display:flex;align-items:center;gap:5px;"><input type="checkbox" name="medio_respuesta[]" value="correo" id="edit-resp-correo"> Correo</label>
                        <label style="display:flex;align-items:center;gap:5px;"><input type="checkbox" name="medio_respuesta[]" value="sms" id="edit-resp-sms"> SMS</label>
                    </div>
                </div>
                <div>
                    <label for="edit-archivos" style="display:block;margin-bottom:5px;font-weight:600;color:#333;">Reemplazar archivos (opcional)</label>
                    <input type="file" name="archivos[]" id="edit-archivos" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="width:100%;padding:8px;border:2px solid #9D825D;border-radius:5px;">
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;">
                    <button type="submit" style="background:#7b9a82;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Guardar cambios</button>
                    <button type="button" id="btn-cerrar-editar" style="background:#6c757d;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:600;">Cancelar</button>
                </div>
            </form>
        </div>`;
    document.body.appendChild(modal);

    modal.querySelector('.close-editar').onclick = () => modal.style.display = 'none';
    modal.querySelector('#btn-cerrar-editar').onclick = () => modal.style.display = 'none';
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

    const formEditar = modal.querySelector('#form-editar');
    formEditar.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(formEditar);

        fetch('../controller/editarPqr.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('PQRS actualizada exitosamente');
                    modal.style.display = 'none';
                    location.reload();
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Intente nuevamente'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error al procesar la solicitud');
            });
    });

    return modal;
}

// Ver detalles completos
function verDetallesCompletos(id) {
    const modal = ensureDetallesModal();
    const contenido = document.getElementById('contenido-detalles-completos');
    contenido.innerHTML = "<p>Cargando detalles...</p>";
    modal.style.display = 'block';

    fetch(`../controller/obtenerDetallesPqr.php?id=${encodeURIComponent(id)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                contenido.innerHTML = data.html;
            } else {
                contenido.innerHTML = `<p>${data.message || 'No fue posible cargar los detalles.'}</p>`;
            }
        })
        .catch(err => {
            console.error(err);
            contenido.innerHTML = "<p>Error al cargar detalles de la PQRS</p>";
        });
}

// Editar PQR
function editarPqr(id) {
    const modal = ensureEditarModal();

    fetch(`../controller/obtenerPqr.php?id=${encodeURIComponent(id)}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error al cargar datos: ' + (data.message || ''));
                return;
            }
            const pqr = data.data;
            document.getElementById('edit-id').value = pqr.id;
            document.getElementById('edit-tipo').value = pqr.tipo_pqr;
            document.getElementById('edit-asunto').value = pqr.asunto;
            document.getElementById('edit-mensaje').value = pqr.mensaje;

            const medios = (pqr.medio_respuesta || '').split(',');
            document.getElementById('edit-resp-correo').checked = medios.includes('correo');
            document.getElementById('edit-resp-sms').checked = medios.includes('sms');

            modal.style.display = 'block';
        })
        .catch(err => {
            console.error(err);
            alert('Error al cargar datos de la PQRS');
        });
}
