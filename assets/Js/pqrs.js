document.addEventListener("DOMContentLoaded", function () {
<<<<<<< HEAD
    // Modal
    let modal = document.getElementById("modal");
    let openModalBtn = document.getElementById("openModal");
    let closeModalBtn = document.querySelector(".close");

    // Verificar que el modal existe antes de agregar eventos
    if (openModalBtn && modal) {
        openModalBtn.addEventListener("click", function () {
            modal.style.display = "flex";
        });
    }

    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener("click", function () {
=======
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
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            modal.style.display = "none";
        });
    }

<<<<<<< HEAD
    // Cerrar modal si clic en el fondo
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
    if (modal) {
        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

<<<<<<< HEAD
    // Validaciones de formulario
=======
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
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
    const formulario = document.querySelector(".formulario-pqr");

    if (formulario) {
        formulario.addEventListener("submit", function (e) {
<<<<<<< HEAD
            e.preventDefault(); // Evita el envío por defecto
            
            // Capturar campos
=======
            e.preventDefault();

>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            const nombres = formulario.nombres.value.trim();
            const apellidos = formulario.apellidos.value.trim();
            const identificacion = formulario.identificacion.value.trim();
            const email = formulario.email.value.trim();
            const telefono = formulario.telefono.value.trim();
            const tipoPqr = formulario.tipo_pqr.value;
            const asunto = formulario.asunto.value.trim();
            const mensaje = formulario.mensaje.value.trim();
<<<<<<< HEAD
            
            // Cambio clave: seleccionar checkboxes con corchetes en el nombre
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            const respuestaChecks = formulario.querySelectorAll('input[name="respuesta[]"]:checked');
            const archivos = formulario.archivos.files;

            // Validaciones
<<<<<<< HEAD
            if (
                !nombres ||
                !apellidos ||
                !identificacion ||
                !email ||
                !telefono ||
                !tipoPqr ||
                !asunto ||
                !mensaje
            ) {
=======
            if (!nombres || !apellidos || !identificacion || !email || !telefono || !tipoPqr || !asunto || !mensaje) {
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
                alert("Por favor, completa todos los campos obligatorios.");
                return;
            }

<<<<<<< HEAD
            // Validación teléfono: exactamente 10 dígitos
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            const telefonoRegex = /^\d{10}$/;
            if (!telefonoRegex.test(telefono)) {
                alert("El teléfono debe tener exactamente 10 dígitos numéricos.");
                return;
            }

<<<<<<< HEAD
            // Validación email
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("El correo electrónico no tiene un formato válido.");
                return;
            }

<<<<<<< HEAD
            // Validar al menos un checkbox seleccionado
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            if (respuestaChecks.length === 0) {
                alert("Selecciona al menos un medio para recibir la respuesta.");
                return;
            }

<<<<<<< HEAD
            // Validación de archivos: Permitir solo imágenes u otros tipos si lo deseas
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            if (archivos.length > 0) {
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                for (let file of archivos) {
                    if (!allowedTypes.includes(file.type)) {
                        alert('Solo se permiten archivos de tipo imagen (JPEG, PNG) o PDF.');
                        return;
                    }
                }
            }

<<<<<<< HEAD
            // Si todo está bien
            alert("Formulario validado correctamente. Enviando...");

            // Mostrar el modal de éxito
            if (modal) {
                modal.style.display = "flex";
            }

            formulario.submit();
                    });
                }
        });




document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const faqItem = button.closest('.faq-item');
        faqItem.classList.toggle('active');
    });
});
=======
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

>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
