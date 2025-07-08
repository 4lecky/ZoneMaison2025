document.addEventListener("DOMContentLoaded", function () {
    // ========================
    // MODAL CONSULTA PQR
    // ========================
    const modal = document.getElementById("modal");
    const openBtn = document.getElementById("openModal");
    const closeBtn = document.querySelector(".close");
    const formConsulta = document.getElementById("pqr-form");
    const resultadoDiv = document.getElementById("resultado-pqr");

    if (openBtn && modal) {
        openBtn.addEventListener("click", () => {
            modal.style.display = "flex";
            if (resultadoDiv) resultadoDiv.innerHTML = "";
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    if (modal) {
        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    if (formConsulta) {
        formConsulta.addEventListener("submit", function (e) {
            e.preventDefault();
            const cedula = document.getElementById("cedula").value;

            resultadoDiv.innerHTML = "<p>Cargando...</p>";

            fetch("../controller/consultar_pqr.php?cedula=" + encodeURIComponent(cedula))
                .then(res => res.text())
                .then(html => {
                    resultadoDiv.innerHTML = html;
                })
                .catch(() => {
                    resultadoDiv.innerHTML = "<p>Error al consultar.</p>";
                });
        });
    }

    // ========================
    // VALIDACIÓN Y ENVÍO FORMULARIO PQR
    // ========================
    const formulario = document.querySelector(".formulario-pqr");

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

            // Validaciones
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
                alert("El correo electrónico no tiene un formato válido.");
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
                        alert('Solo se permiten archivos de tipo imagen (JPEG, PNG) o PDF.');
                        return;
                    }
                }
            }

            // Enviar con fetch
            const datos = new FormData(formulario);
            fetch("../controller/pqrsController.php", {
                method: "POST",
                body: datos
            })
            .then(res => res.text())
            .then(respuesta => {
                if (respuesta.trim() === "OK") {
                    const mensaje = document.getElementById("mensaje-exito");
                    if (mensaje) mensaje.style.display = "block";
                    formulario.reset();
                } else {
                    alert("Ocurrió un error al registrar: " + respuesta);
                }
            })
            .catch(error => {
                alert("Error de red al enviar el formulario.");
                console.error(error);
            });
        });
    }
    
    // DELEGACIÓN DE EVENTO PARA BOTÓN DE ELIMINAR DESDE LA TABLA
document.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("btn-eliminar")) {
        const id = e.target.dataset.id;
        if (confirm("¿Estás seguro de que deseas eliminar esta PQR?")) {
            fetch("../controller/pqrsController.php?eliminar=" + id)
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
});

