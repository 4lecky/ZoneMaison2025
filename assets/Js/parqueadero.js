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

async function cargarTablaParqueaderos() {
    const cuerpoTabla = document.getElementById('tablaParqueaderoCuerpo');
    const estadoVacio = document.getElementById('estadoVacioParqueadero');
    cuerpoTabla.innerHTML = ''; // Limpia antes de cargar

    try {
        const response = await fetch('/ZoneMaison2025/controller/obtenerRegistroParqueadero.php');
        const registros = await response.json();

        if (registros.length === 0 || registros.error) {
            estadoVacio.style.display = 'block';
            return;
        }

        estadoVacio.style.display = 'none';

        registros.forEach((reg, index) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${index + 1}</td>
                <td contenteditable="true" data-campo="placa" data-id="${reg.parq_id}">${reg.parq_vehi_placa}</td>
                <td contenteditable="true" data-campo="propietario" data-id="${reg.parq_id}">${reg.parq_nombre_propietario}</td>
                <td contenteditable="true" data-campo="parqueadero" data-id="${reg.parq_id}">${reg.parq_num_parqueadero}</td>
                <td contenteditable="true" data-campo="estado" data-id="${reg.parq_id}">${reg.parq_vehi_estadiIngreso}</td>
                <td>
                    <button onclick="eliminarRegistro(${reg.parq_id})" class="btn-eliminar">🗑️</button>
                </td>
            `;
            cuerpoTabla.appendChild(fila);
        });

    } catch (error) {
        console.error("Error al cargar la tabla:", error);
    }
}




// Al editar celda y presionar Enter, se guarda el cambio
document.addEventListener('keydown', async function (e) {
    if (e.target.matches('[contenteditable="true"]') && e.key === 'Enter') {
        e.preventDefault();

        const celda = e.target;
        const nuevoValor = celda.innerText.trim();
        const campo = celda.getAttribute('data-campo');
        const id = celda.getAttribute('data-id');

        try {
            const res = await fetch('/ZoneMaison2025/controller/editarRegistroParqueadero.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&campo=${campo}&valor=${encodeURIComponent(nuevoValor)}`
            });

            const data = await res.json();

            if (!data.success) {
                alert('❌ Error al guardar el cambio');
            }
        } catch (error) {
            alert('❌ Error de conexión');
        }
    }
});

async function eliminarRegistro(id) {
    const confirmacion = confirm("¿Estás seguro de eliminar este registro?");
    if (!confirmacion) return;

    try {
        const res = await fetch('/ZoneMaison2025/controller/eliminarRegistroParqueadero.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        });

        const data = await res.json();

        if (data.success) {
            alert('✅ Registro eliminado');
            cargarTablaParqueaderos();
        } else {
            alert('❌ No se pudo eliminar');
        }
    } catch (error) {
        alert('❌ Error al eliminar');
    }
}




document.addEventListener('DOMContentLoaded', function () {
    cargarTablaParqueaderos();
});



