// parqueadero.js (versión corregida)

// Mostrar / ocultar formularios (el CRUD queda siempre visible)
function mostrarFormulario(formularioId) {
  const formularios = ['formularioRegistro', 'formularioCobro', 'formularioPropietario'];

  // Ocultamos solo los formularios (no tocamos el CRUD)
  formularios.forEach(id => {
    const seccion = document.getElementById(id);
    if (seccion) {
      seccion.style.display = 'none';
    }
  });

  // Mostramos el solicitado (si existe)
  const target = document.getElementById(formularioId);
  if (target) {
    target.style.display = 'block';
  }
}

// ================= CARGA TABLA =================
async function cargarTablaParqueaderos() {
  const cuerpoTabla = document.getElementById('tablaParqueaderoCuerpo');
  const estadoVacio = document.getElementById('estadoVacioParqueadero');

  if (!cuerpoTabla) {
    console.warn('No se encontró #tablaParqueaderoCuerpo — la tabla no se puede cargar.');
    return;
  }

  cuerpoTabla.innerHTML = '';

  try {
    const response = await fetch('../controller/recibirDatosRegistroParqueadero.php');
    if (!response.ok) throw new Error('Error HTTP: ' + response.status);
    const registros = await response.json();

    if (!registros || registros.length === 0 || registros.error) {
      if (estadoVacio) estadoVacio.style.display = 'block';
      return;
    }

    if (estadoVacio) estadoVacio.style.display = 'none';

    registros.forEach((reg, index) => {
      const fila = document.createElement('tr');
      fila.innerHTML = `
        <td>${index + 1}</td>
        <td contenteditable="true" data-campo="parq_vehi_placa" data-id="${reg.parq_id}">${escapeHtml(reg.parq_vehi_placa)}</td>
        <td contenteditable="true" data-campo="parq_nombre_propietario_vehi" data-id="${reg.parq_id}">${escapeHtml(reg.parq_nombre_propietario_vehi)}</td>
        <td contenteditable="true" data-campo="parq_vehi_estadiIngreso" data-id="${reg.parq_id}">${escapeHtml(reg.parq_vehi_estadiIngreso)}</td>
        <td>
          <button type="button" onclick="eliminarRegistro(${reg.parq_id})" class="btn-eliminar">🗑️</button>
        </td>
      `;
      cuerpoTabla.appendChild(fila);
    });

  } catch (err) {
    console.error('Error al cargar la tabla:', err);
    if (estadoVacio) estadoVacio.style.display = 'block';
  }
}

// Evitar XSS simple al insertar texto en la tabla
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// ================= EDITAR CELDA (ENTER) =================
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
        body: `id=${encodeURIComponent(id)}&campo=${encodeURIComponent(campo)}&valor=${encodeURIComponent(nuevoValor)}`
      });
      const data = await res.json();
      if (!data.success) {
        // usa SweetAlert2 si lo tienes cargado
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo guardar la modificación.' });
        else alert('Error al guardar el cambio.');
      }
    } catch (error) {
      if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
      else alert('Error de conexión');
    }
  }
});

// ================= ELIMINAR =================
async function eliminarRegistro(id) {
  if (!confirm('¿Estás seguro de eliminar este registro?')) return;

  try {
    const res = await fetch('/ZoneMaison2025/controller/eliminarRegistroParqueadero.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${encodeURIComponent(id)}`
    });
    const data = await res.json();
    if (data.success) {
      if (window.Swal) Swal.fire({ icon: 'success', title: 'Registro eliminado' });
      cargarTablaParqueaderos();
    } else {
      if (window.Swal) Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: data.message || '' });
    }
  } catch (err) {
    if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar' });
    else alert('Error al eliminar');
  }
}

// ================= INICIALIZAR =================
document.addEventListener('DOMContentLoaded', function () {
  // Aseguramos que el CRUD esté visible (si así lo quieres)
  const crud = document.getElementById('crudConsultaParqueadero');
  if (crud) crud.style.display = 'block';

  // No movemos nodos del DOM para no romper selectores CSS
  cargarTablaParqueaderos();
});


document.addEventListener('DOMContentLoaded', function() {
  // Asegurar que el wrapper tenga la clase esperada
  const contWrapper = document.querySelector('.contenedorCrudConsulta');
  if (!contWrapper) {
    const possible = document.querySelector('#crudConsultaParqueadero');
    if (possible && possible.parentElement) {
      possible.parentElement.classList.add('contenedorCrudConsulta');
    }
  }

  // Asegurar que el formulario propietario tenga la estructura de clases
  const fp = document.getElementById('formularioPropietario');
  if (fp) {
    const inner = fp.querySelector('.formulario-container-Propietario');
    if (!inner) {
      const firstDiv = fp.querySelector('div');
      if (firstDiv) firstDiv.classList.add('formulario-container-Propietario');
    }
    const formClass = fp.querySelector('form');
    if (formClass && !formClass.classList.contains('formulario-Propietario')) {
      formClass.classList.add('formulario-Propietario');
    }
  }

  // Forzar recarga de estilos "repaint" (a veces ayuda)
  document.body.offsetHeight; 
});






// // Mueve el formulario de consulta según visibilidad de otros formularios
// function actualizarFormularioConsulta() {
//     const registroVisible = document.getElementById('formularioRegistro').style.display === 'block';
//     const cobroVisible = document.getElementById('formularioCobro').style.display === 'block';
//     const propietarioVisible = document.getElementById('formularioPropietario').style.display === 'block';
//     const consulta = document.getElementById('formularioConsulta');
//     const contenedorOriginal = document.querySelector('.contenedorConsulta');
//     const zonaFinal = document.getElementById('zonaConsultaFinal');

//     if (registroVisible || cobroVisible) {
//         zonaFinal.appendChild(consulta);
//         consulta.style.display = 'block';
//     } else {
//         contenedorOriginal.appendChild(consulta);
//         consulta.style.display = 'block';
//     }
// }

// // Cambia entre formularios y limpia sus datos
// function mostrarFormulario(formularioId) {
//     const formularios = ['formularioRegistro', 'formularioCobro', 'formularioPropietario'];

//     formularios.forEach(id => {
//         const seccion = document.getElementById(id);
//         if (seccion) {
//             seccion.style.display = 'none';

//             const form = seccion.querySelector('form');
//             if (form) {
//                 form.reset();
//                 const campos = form.querySelectorAll('input, textarea, select');
//                 campos.forEach(campo => {
//                     if (campo.type === 'checkbox' || campo.type === 'radio') {
//                         campo.checked = false;
//                     } else {
//                         campo.value = '';
//                     }
//                 });
//             }
//         }
//     });

//     const formularioMostrar = document.getElementById(formularioId);
//     if (formularioMostrar) {
//         formularioMostrar.style.display = 'block';
//     }

//     actualizarFormularioConsulta();
// }

// // Carga dinámica de la tabla de parqueaderos
// async function cargarTablaParqueaderos() {
//     const cuerpoTabla = document.getElementById('tablaParqueaderoCuerpo');
//     const estadoVacio = document.getElementById('estadoVacioParqueadero');
//     cuerpoTabla.innerHTML = '';

//     try {
//         const response = await fetch('/ZoneMaison2025/controller/obtenerRegistroParqueadero.php');
//         const registros = await response.json();

//         if (!registros || registros.length === 0 || registros.error) {
//             estadoVacio.style.display = 'block';
//             return;
//         }

//         estadoVacio.style.display = 'none';

//         registros.forEach((reg, index) => {
//             const fila = document.createElement('tr');
//             fila.innerHTML = `
//                 <td>${index + 1}</td>
//                 <td contenteditable="true" data-campo="parq_vehi_placa" data-id="${reg.parq_id}">${reg.parq_vehi_placa}</td>
//                 <td contenteditable="true" data-campo="parq_nombre_propietario_vehi" data-id="${reg.parq_id}">${reg.parq_nombre_propietario_vehi}</td>
//                 <td contenteditable="true" data-campo="parq_vehi_estadiIngreso" data-id="${reg.parq_id}">${reg.parq_vehi_estadiIngreso}</td>
//                 <td>
//                     <button onclick="eliminarRegistro(${reg.parq_id})" class="btn-eliminar">🗑️</button>
//                 </td>
//             `;
//             cuerpoTabla.appendChild(fila);
//         });

//     } catch (error) {
//         console.error("Error al cargar la tabla:", error);
//     }
// }

// // Guardar cambios al presionar Enter en una celda editable
// document.addEventListener('keydown', async function (e) {
//     if (e.target.matches('[contenteditable="true"]') && e.key === 'Enter') {
//         e.preventDefault();

//         const celda = e.target;
//         const nuevoValor = celda.innerText.trim();
//         const campo = celda.getAttribute('data-campo');
//         const id = celda.getAttribute('data-id');

//         try {
//             const res = await fetch('/ZoneMaison2025/controller/editarRegistroParqueadero.php', {
//                 method: 'POST',
//                 headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//                 body: `id=${id}&campo=${campo}&valor=${encodeURIComponent(nuevoValor)}`
//             });

//             const data = await res.json();

//             if (!data.success) {
//                 alert('❌ Error al guardar el cambio');
//             }
//         } catch (error) {
//             alert('❌ Error de conexión');
//         }
//     }
// });

// // Eliminar registro
// async function eliminarRegistro(id) {
//     const confirmacion = confirm("¿Estás seguro de eliminar este registro?");
//     if (!confirmacion) return;

//     try {
//         const res = await fetch('/ZoneMaison2025/controller/eliminarRegistroParqueadero.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//             body: `id=${id}`
//         });

//         const data = await res.json();

//         if (data.success) {
//             alert('✅ Registro eliminado');
//             cargarTablaParqueaderos();
//         } else {
//             alert('❌ No se pudo eliminar');
//         }
//     } catch (error) {
//         alert('❌ Error al eliminar');
//     }
// }

// // Inicializa la tabla al cargar la página
// document.addEventListener('DOMContentLoaded', function () {
//     cargarTablaParqueaderos();
// });



