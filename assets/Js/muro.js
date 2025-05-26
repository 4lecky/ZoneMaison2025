document.addEventListener('DOMContentLoaded', () => {
  // Referencias a elementos del DOM
  const form = document.querySelector('.muro');
  const enviarBtn = form.querySelector('.enviar');
  const cancelarBtn = form.querySelector('.cancelar');

  const hoy = new Date();
  document.getElementById("fecha").value = hoy.toISOString().split("T")[0];
  document.getElementById("hora").value = hoy.toTimeString().slice(0, 5); // HH:MM

  enviarBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const destinatario = document.getElementById('destinatario').value; // Asegúrate que exista el id="destinatario" en el select
    const asunto = document.getElementById('asunto').value.trim();
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    const descripcion = document.getElementById('descripcion').value.trim();

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
      alert('Asegúrate de incluir toda la información en la descripción.');
      return;
    }

    alert('¡Formulario enviado correctamente!');
    form.submit(); // Envía el formulario al PHP
  });

  cancelarBtn.addEventListener('click', function (e) {
    e.preventDefault();
    if (confirm('¿Estás seguro de que quieres cancelar? Esto limpiará el formulario.')) {
      form.reset(); // Limpia el formulario
    }
  });
});
