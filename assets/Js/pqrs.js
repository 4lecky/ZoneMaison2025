// assets/js/pqrs.js
// Versión unificada y corregida: FAQ global, sin duplicados de funciones ni listeners

console.log('PQRS JS cargado correctamente');

/* =======================
   FAQ (GLOBAL) - VERSIÓN ÚNICA
   ======================= */
function initFAQ() {
    console.log("Inicializando FAQ");

    const faqItems = document.querySelectorAll('.faq-item');

    if (faqItems.length === 0) {
        console.warn("No se encontraron .faq-item");
        return;
    }

    faqItems.forEach((item, index) => {
        const button = item.querySelector('.faq-question');
        
        if (!button) return;

        // Verificar si ya tiene listener para evitar duplicados
        if (button.hasAttribute('data-faq-initialized')) {
            return;
        }
        
        button.setAttribute('data-faq-initialized', 'true');
        
        button.addEventListener('click', (e) => {
            e.preventDefault();
            console.log(`Click en FAQ ${index + 1}`);

            // Simplemente alternar la clase active
            item.classList.toggle('active');
        });
    });
}

/* =======================
   MAIN: se ejecuta al cargar el DOM
   ======================= */
document.addEventListener("DOMContentLoaded", function () {
    // 1) Inicializar FAQ - SOLO UNA VEZ
    initFAQ();

    // ========================
    // MODAL CONSULTA PQR (el que pide la cédula)
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
        function restaurarBoton(boton, texto) { if (boton) { boton.disabled = false; boton.innerHTML = texto; actualizarEstadoBoton(); } }
        function mostrarMensajeExito(m) { removerMensajesTemporales(); const d = document.createElement('div'); d.className = 'alert alert-success mensaje-temporal'; d.innerHTML = `<i class="ri-check-circle-fill"></i><strong>${m}</strong>`; formularioPQRS.insertBefore(d, formularioPQRS.firstChild); d.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        function mostrarErrores(errs) { removerMensajesTemporales(); let h = '<div class="alert alert-danger mensaje-temporal"><i class="ri-error-warning-fill"></i><strong>Por favor, corrige los siguientes errores:</strong><ul>'; errs.forEach(e => h += `<li>${e}</li>`); h += '</ul></div>'; formularioPQRS.insertAdjacentHTML('afterbegin', h); const n = document.querySelector('.mensaje-temporal'); n && n.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        function removerMensajesTemporales() { document.querySelectorAll('.mensaje-temporal').forEach(m => m.remove()); }
        setTimeout(() => { const ok = document.getElementById('mensaje-exito-servidor'); const er = document.getElementById('mensaje-errores-servidor'); if (ok) { ok.style.opacity = '0'; setTimeout(() => ok.remove(), 500); } if (er) { er.style.opacity = '0'; setTimeout(() => er.remove(), 500); } }, 5000);
    } else {
        console.log('Formulario PQRS no encontrado en esta página');
    }

    // ========================
    // ELIMINAR (delegado)
    // ========================
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-eliminar");
        if (!btn) return;
        const id = btn.dataset.id || btn.getAttribute('data-id');
        if (!id) return;
        if (!confirm("¿Estás seguro de que deseas eliminar esta PQR?")) return;

        fetch("../controller/eliminarPqr.php?id=" + encodeURIComponent(id))
            .then(res => res.text())
            .then(() => { alert("PQR eliminada correctamente."); location.reload(); })
            .catch(() => alert("Error de red al intentar eliminar."));
    });

    // ========================
    // VER / EDITAR (delegado)
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
   Tabla de resultados (si se usa en la vista modal de consulta)
   ========================================================= */
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

/* =========================================================
   Modales utilitarios (se crean si no existen)
   ========================================================= */
function ensureDetallesModal() {
    let modal = document.getElementById('modalDetallesCompletos');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'modalDetallesCompletos';
    modal.className = 'modal';
    modal.style.display = 'none';
    modal.innerHTML = `
        <div class="modal-content" style="width:90%;max-height:80vh;overflow-y:auto;position:relative;">
            <span class="close-detalles" style="position:absolute;right:20px;top:15px;cursor:pointer;font-size:28px">&times;</span>
            <h2 style="margin-top:0">Detalles Completos de PQRS</h2>
            <div id="contenido-detalles-completos"></div>
        </div>`;
    document.body.appendChild(modal);

    modal.querySelector('.close-detalles').onclick = () => modal.style.display = 'none';
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });
    return modal;
}

function ensureEditarModal() {
    let modal = document.getElementById('modalEditar');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'modalEditar';
    modal.className = 'modal';
    modal.style.display = 'none';
    modal.innerHTML = `
        <div class="modal-content" style="width:90%;max-height:80vh;overflow-y:auto;position:relative;">
            <span class="close-editar" style="position:absolute;right:20px;top:15px;cursor:pointer;font-size:28px">&times;</span>
            <h2 style="margin-top:0">Editar PQRS</h2>
            <form id="form-editar" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <div>
                    <label for="edit-tipo">Tipo</label>
                    <select name="tipo_pqr" id="edit-tipo" required>
                        <option value="peticion">Petición</option>
                        <option value="queja">Queja</option>
                        <option value="reclamo">Reclamo</option>
                        <option value="sugerencia">Sugerencia</option>
                    </select>
                </div>
                <div>
                    <label for="edit-asunto">Asunto</label>
                    <input type="text" name="asunto" id="edit-asunto" required minlength="5" maxlength="255">
                </div>
                <div>
                    <label for="edit-mensaje">Descripción</label>
                    <textarea name="mensaje" id="edit-mensaje" required minlength="10" rows="4"></textarea>
                </div>
                <div>
                    <label>Medio de respuesta</label>
                    <label><input type="checkbox" name="medio_respuesta[]" value="correo" id="edit-resp-correo"> Correo</label>
                    <label><input type="checkbox" name="medio_respuesta[]" value="sms" id="edit-resp-sms"> SMS</label>
                </div>
                <div>
                    <label for="edit-archivos">Reemplazar archivos (opcional)</label>
                    <input type="file" name="archivos[]" id="edit-archivos" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>
                <div style="margin-top:12px;display:flex;gap:10px;justify-content:center">
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                    <button type="button" class="btn-secondary" id="btn-cerrar-editar">Cancelar</button>
                </div>
            </form>
        </div>`;
    document.body.appendChild(modal);

    modal.querySelector('.close-editar').onclick = () => modal.style.display = 'none';
    modal.querySelector('#btn-cerrar-editar').onclick = () => modal.style.display = 'none';
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

    // Bind submit aquí (una sola vez)
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

/* =========================================================
   Funciones para Ver / Editar
   ========================================================= */
function verDetallesCompletos(id) {
    const modal = ensureDetallesModal();
    const contenido = document.getElementById('contenido-detalles-completos');
    contenido.innerHTML = "<p>Cargando detalles...</p>";
    modal.style.display = 'flex';

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

            modal.style.display = 'flex';
        })
        .catch(err => {
            console.error(err);
            alert('Error al cargar datos de la PQRS');
        });
}
