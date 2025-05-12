// Obtener la fecha actual y establecerla
const hoy = new Date();
document.getElementById("Fecha").value = hoy.toISOString().split("T")[0];

// Obtener la hora actual y establecerla
const hora = hoy.toTimeString().slice(0,5); // formato HH:MM
document.getElementById("Hora").value = hora;

document.addEventListener('DOMContentLoaded', () => {
    const enviarBtn = document.querySelector('.Enviar');
    const cancelarBtn = document.querySelector('.Cancelar');
  
    enviarBtn.addEventListener('click', function (e) {
      e.preventDefault();
  
      const destinatario = document.getElementById('filtrodestinatario').value;
      const asunto = document.getElementById('Asunto').value.trim();
      const fecha = document.getElementById('Fecha').value;
      const hora = document.getElementById('Hora').value;
      const descripcion = document.getElementById('Descripcion').value.trim();
  
      if (!destinatario || destinatario === 'Seleccione un Destinatario') {
        alert('Por favor selecciona un destinatario.');
        return;
      }
  
      if (asunto.length < 5) {
        alert('El asunto debe tener al menos 5 caracteres.');
        return;
      }
  
      if (!fecha) {
        alert('Por favor selecciona una fecha.');
        return;
      }
  
      if (!hora) {
        alert('Por favor selecciona una hora.');
        return;
      }
  
      if (!descripcion.includes('Atentamente')) {
        alert('Asegúrate de incluir un cierre con "Atentamente" en la descripción.');
        return;
      }
  
      alert('¡Formulario enviado correctamente!');
      // Aquí podrías enviar los datos al servidor
    });
  
    cancelarBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (confirm('¿Estás seguro de que quieres cancelar? Esto limpiará el formulario.')) {
        document.getElementById('filtrodestinatario').value = '';
        document.getElementById('Asunto').value = '';
        document.getElementById('Fecha').value = '';
        document.getElementById('Hora').value = '';
        document.getElementById('Descripcion').value = '';
        document.getElementById('archivo').value = '';
      }
    });
  });