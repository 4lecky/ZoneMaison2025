// Java para mostrar el formulario de consulta al principio debajo de las imagenes y botones y luego entre el formulario y el fotter

function actualizarFormularioConsulta() {
    const registroVisible = document.getElementById('formularioRegistro').style.display === 'block';
    const cobroVisible = document.getElementById('formularioCobro').style.display === 'block';
    const consulta = document.getElementById('formularioConsulta');
    //   Contenedor original (arriba del todo)
    const contenedorOriginal = document.querySelector('.contenedorConsulta');
    //   Zona donde queremos que se muestre si hay formularios visibles
    const zonaFinal = document.getElementById('zonaConsultaFinal');


    if (registroVisible || cobroVisible) {
        // Mover el formulario de consulta al final del body
        zonaFinal.appendChild(consulta);
        consulta.style.display = 'block';
    } else {
        // Volver a su lugar original arriba
        contenedorOriginal.appendChild(consulta);
        consulta.style.display = 'block'; // o flex si lo uso asi
    }
}

//   Java para cambiar entre formularios y borrar la informacion si se recraga la pagina

function mostrarFormulario(formularioId) {
    const formularios = ['formularioRegistro', 'formularioCobro'];

    formularios.forEach(id => {
        const seccion = document.getElementById(id);
        if (seccion) {
            // Oculta el formulario
            seccion.style.display = 'none';

            // Limpia TODOS los campos del formulario, incluso si estaba oculto
            const form = seccion.querySelector('form');
            if (form) {
                form.reset(); // Intenta hacer reset general
                // Limpieza manual por si reset falla
                const campos = form.querySelectorAll('input, textarea, select');
                campos.forEach(campo => {
                    if (campo.type === 'checkbox' || campo.type === 'radio') {
                        campo.checked = false;
                    } else {
                        campo.value = '';
                    }
                });
            }
        }
    });

    // Mostrar el formulario deseado
    const formularioMostrar = document.getElementById(formularioId);
    if (formularioMostrar) {
        formularioMostrar.style.display = 'block';
    }

    // Reubica el formulario de consulta si aplica
    actualizarFormularioConsulta();
}


