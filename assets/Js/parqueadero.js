// Mueve el formulario de consulta según visibilidad de otros formularios
function actualizarFormularioConsulta() {
    const registroVisible = document.getElementById('formularioRegistro').style.display === 'block';
    const cobroVisible = document.getElementById('formularioCobro').style.display === 'block';
    const consulta = document.getElementById('formularioConsulta');
    const contenedorOriginal = document.querySelector('.contenedorConsulta');
    const zonaFinal = document.getElementById('zonaConsultaFinal');

    if (registroVisible || cobroVisible) {
        zonaFinal.appendChild(consulta);
        consulta.style.display = 'block';
    } else {
        contenedorOriginal.appendChild(consulta);
        consulta.style.display = 'block';
    }
}

// Cambia entre formularios y limpia sus datos
function mostrarFormulario(formularioId) {
    const formularios = ['formularioRegistro', 'formularioCobro'];

    formularios.forEach(id => {
        const seccion = document.getElementById(id);
        if (seccion) {
            seccion.style.display = 'none';

            const form = seccion.querySelector('form');
            if (form) {
                form.reset();
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

    const formularioMostrar = document.getElementById(formularioId);
    if (formularioMostrar) {
        formularioMostrar.style.display = 'block';
    }

    actualizarFormularioConsulta();
}

// Carga dinámica de la tabla de parqueaderos
async function cargarTablaParqueaderos() {
    const cuerpoTabla = document.getElementById('tablaParqueaderoCuerpo');
    const estadoVacio = document.getElementById('estadoVacioParqueadero');
    cuerpoTabla.innerHTML = '';

    try {
        const response = await fetch('/ZoneMaison2025/controller/obtenerRegistroParqueadero.php');
        const registros = await response.json();

        if (!registros || registros.length === 0 || registros.error) {
            estadoVacio.style.display = 'block';
            return;
        }

        estadoVacio.style.display = 'none';

        registros.forEach((reg, index) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${index + 1}</td>
                <td contenteditable="true" data-campo="parq_vehi_placa" data-id="${reg.parq_id}">${reg.parq_vehi_placa}</td>
                <td contenteditable="true" data-campo="parq_nombre_propietario_vehi" data-id="${reg.parq_id}">${reg.parq_nombre_propietario_vehi}</td>
                <td contenteditable="true" data-campo="parq_vehi_estadiIngreso" data-id="${reg.parq_id}">${reg.parq_vehi_estadiIngreso}</td>
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

// Guardar cambios al presionar Enter en una celda editable
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

// Eliminar registro
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

// Inicializa la tabla al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    cargarTablaParqueaderos();
});



