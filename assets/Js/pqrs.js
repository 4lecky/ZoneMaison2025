document.addEventListener("DOMContentLoaded", function () {
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
            modal.style.display = "none";
        });
    }

    // Cerrar modal si clic en el fondo
    if (modal) {
        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    // Validaciones de formulario
    const formulario = document.querySelector(".formulario-pqr");

    if (formulario) {
        formulario.addEventListener("submit", function (e) {
            e.preventDefault(); // Evita el envío por defecto
            
            // Capturar campos
            const nombres = formulario.nombres.value.trim();
            const apellidos = formulario.apellidos.value.trim();
            const identificacion = formulario.identificacion.value.trim();
            const email = formulario.email.value.trim();
            const telefono = formulario.telefono.value.trim();
            const tipoPqr = formulario.tipo_pqr.value;
            const asunto = formulario.asunto.value.trim();
            const mensaje = formulario.mensaje.value.trim();
            
            // Cambio clave: seleccionar checkboxes con corchetes en el nombre
            const respuestaChecks = formulario.querySelectorAll('input[name="respuesta[]"]:checked');
            const archivos = formulario.archivos.files;

            // Validaciones
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
                alert("Por favor, completa todos los campos obligatorios.");
                return;
            }

            // Validación teléfono: exactamente 10 dígitos
            const telefonoRegex = /^\d{10}$/;
            if (!telefonoRegex.test(telefono)) {
                alert("El teléfono debe tener exactamente 10 dígitos numéricos.");
                return;
            }

            // Validación email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("El correo electrónico no tiene un formato válido.");
                return;
            }

            // Validar al menos un checkbox seleccionado
            if (respuestaChecks.length === 0) {
                alert("Selecciona al menos un medio para recibir la respuesta.");
                return;
            }

            // Validación de archivos: Permitir solo imágenes u otros tipos si lo deseas
            if (archivos.length > 0) {
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                for (let file of archivos) {
                    if (!allowedTypes.includes(file.type)) {
                        alert('Solo se permiten archivos de tipo imagen (JPEG, PNG) o PDF.');
                        return;
                    }
                }
            }

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