document.addEventListener("DOMContentLoaded", function () {
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
    // VALIDACIÓN Y ENVÍO FORMULARIO PRINCIPAL
    // ========================
    const formulario = document.getElementById("formPQRS");

    if (formulario) {
        formulario.addEventListener("submit", function (e) {
            e.preventDefault();

            const nombres = formulario.nombres.value.trim();
            const apellidos = formulario.apellidos.value.trim();
            const identificacion = formulario.identificacion.value.trim();
            const email = formulario.email.value.trim();
            const telefono = formulario.telefono.value.trim();
            const tipoPqr = formulario.tipo_pqr.value;
            const asunto = formulario.asunto.value.trim();
            const mensaje = formulario.mensaje.value.trim();
            const respuestaChecks = formulario.querySelectorAll('input[name="respuesta[]"]:checked');
            const archivos = formulario.archivos.files;

            if (!nombres || !apellidos || !identificacion || !email || !telefono || !tipoPqr || !asunto || !mensaje) {
                alert("Por favor, completa todos los campos obligatorios.");
                return;
            }

            const telefonoRegex = /^\d{10}$/;
            if (!telefonoRegex.test(telefono)) {
                alert("El teléfono debe tener exactamente 10 dígitos numéricos.");
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("El correo electrónico no tiene un formato válido. Debe incluir '@' y un dominio válido (ejemplo: usuario@dominio.com)");
                document.getElementById("email").focus();
                return;
            }

            if (respuestaChecks.length === 0) {
                alert("Selecciona al menos un medio para recibir la respuesta.");
                return;
            }

            if (archivos.length > 0) {
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                for (let file of archivos) {
                    if (!allowedTypes.includes(file.type)) {
                        alert('Solo se permiten archivos JPEG, PNG o PDF.');
                        return;
                    }
                }
            }

            const datos = new FormData(formulario);
            fetch("../controller/pqrsController.php", {
                method: "POST",
                body: datos
            })
            .then(res => res.text())
            .then(respuesta => {
                if (respuesta.trim() === "OK" || respuesta.includes("Actualizado")) {
                    const mensaje = document.getElementById("mensaje-exito");
                    if (mensaje) mensaje.style.display = "block";
                    formulario.reset();
                    const hiddenId = formulario.querySelector('input[name="id"]');
                    if (hiddenId) hiddenId.remove();
                } else {
                    alert("Ocurrió un error al registrar/actualizar: " + respuesta);
                }
            })
            .catch(error => {
                alert("Error de red al enviar el formulario.");
                console.error(error);
            });
        });
    }

    // ========================
    // ELIMINAR DESDE RESULTADOS
    // ========================
    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("btn-eliminar")) {
            const id = e.target.dataset.id;
            if (!id) return;
            if (confirm("¿Estás seguro de que deseas eliminar esta PQR?")) {
                fetch("../controller/pqrsController.php?eliminar=" + encodeURIComponent(id))
                    .then(res => res.text())
                    .then(respuesta => {
                        if (respuesta.trim() === "OK") {
                            const fila = e.target.closest("tr");
                            if (fila) fila.remove();
                            alert("PQR eliminada correctamente.");
                        } else {
                            alert("Error al eliminar: " + respuesta);
                        }
                    })
                    .catch(() => {
                        alert("Error de red al intentar eliminar.");
                    });
            }
        }
    });

    // ========================
    // PREGUNTAS FRECUENTES (FAQ)
    // ========================
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const faqItem = button.closest('.faq-item');
            faqItem.classList.toggle('active');
        });
    });
}); // FIN DOMContentLoaded

/* =========================================================
   FUNCIONES PARA RESULTADOS (tabla, editar)
   ========================================================= */
function inicializarTablaResultados() {
    const tableEl = document.getElementById("tabla-resultado");
    if (!tableEl) return;

    if (typeof $ !== "undefined" && $.fn.DataTable && $.fn.DataTable.isDataTable(tableEl)) {
        $(tableEl).DataTable().destroy();
    }

    if (typeof $ !== "undefined" && $.fn.DataTable) {
        $(tableEl).DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50]
        });
    }

    activarEditar();
}

function activarEditar() {
    const modalEditar = document.getElementById("modalEditar");
    const closeEditar = modalEditar ? modalEditar.querySelector(".close-editar") : null;
    const formEditar = document.getElementById("form-editar");
    if (!modalEditar || !formEditar) return;

    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("edit-id").value = this.dataset.id;
            document.getElementById("edit-nombres").value = this.dataset.nombres;
            document.getElementById("edit-apellidos").value = this.dataset.apellidos;
            document.getElementById("edit-identificacion").value = this.dataset.identificacion;
            document.getElementById("edit-email").value = this.dataset.email;
            document.getElementById("edit-telefono").value = this.dataset.telefono;
            document.getElementById("edit-tipo").value = this.dataset.tipo;
            document.getElementById("edit-asunto").value = this.dataset.asunto;
            document.getElementById("edit-mensaje").value = this.dataset.mensaje;

            const medios = (this.dataset.medio || "").split(",");
            const chkCorreo = document.getElementById("edit-resp-correo");
            const chkSms = document.getElementById("edit-resp-sms");
            if (chkCorreo) chkCorreo.checked = medios.includes("correo");
            if (chkSms) chkSms.checked = medios.includes("sms");

            modalEditar.style.display = "flex";
        });
    });

    if (closeEditar) {
        closeEditar.addEventListener("click", () => {
            modalEditar.style.display = "none";
        });
    }

    window.addEventListener("click", e => {
        if (e.target === modalEditar) modalEditar.style.display = "none";
    });

    formEditar.addEventListener("submit", function (e) {
        e.preventDefault();
        const datos = new FormData(formEditar);

        fetch("../controller/pqrsController.php", {
            method: "POST",
            body: datos
        })
        .then(res => res.text())
        .then(respuesta => {
            if (respuesta.includes("Actualizado") || respuesta.trim() === "OK") {
                alert("PQR actualizada correctamente.");
                modalEditar.style.display = "none";
                const formConsulta = document.getElementById("pqr-form");
                if (formConsulta) formConsulta.dispatchEvent(new Event("submit"));
            } else {
                alert("Error al actualizar: " + respuesta);
            }
        })
        .catch(() => alert("Error de red al actualizar."));
    });
}
