document.addEventListener('DOMContentLoaded', () => {
  // Referencias a elementos del DOM
  const form = document.querySelector('.muro');
  const enviarBtn = form.querySelector('.enviar');
  const cancelarBtn = form.querySelector('.Cancelar');

  // Establecer fecha y hora actuales por defecto al cargar la página
  const ahora = new Date();
  document.getElementById('fechaEvento').value = ahora.toISOString().split('T')[0];
  document.getElementById('horaEvento').value = ahora.toTimeString().slice(0, 5);

  // Función para insertar fecha y hora en el textarea
  function insertarFechaHora() {
    const fecha = document.getElementById('fechaEvento').value;
    const hora = document.getElementById('horaEvento').value;
    const textarea = document.getElementById('descripcion');

    if (!fecha || !hora) {
      alert('Por favor, selecciona una fecha y una hora para el evento.');
      return;
    }

    try {
      // Convertir fecha a formato legible en español
      const fechaObj = new Date(fecha + 'T00:00:00');
      const fechaTexto = fechaObj.toLocaleDateString('es-CO', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });

      // Convertir hora a formato 12 horas
      const [horas, minutos] = hora.split(':');
      const fechaHora = new Date();
      fechaHora.setHours(parseInt(horas), parseInt(minutos));
      const horaTexto = fechaHora.toLocaleTimeString('es-CO', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });

      // Crear el texto con formato deseado
      const textoFechaHora = `Estimados residentes, Les informamos que el día ${fechaTexto} a las ${horaTexto} `;

      // Limpiar el textarea y agregar el nuevo texto
      const textoOriginal = textarea.value.replace(/^Estimados residentes,.*?\d{1,2}:\d{2}\s*(a\.m\.|p\.m\.)?\s*/i, '');
      textarea.value = textoFechaHora + textoOriginal;

      // Posicionar cursor al final del texto insertado
      const nuevaPos = textoFechaHora.length;
      textarea.setSelectionRange(nuevaPos, nuevaPos);
      textarea.focus();

    } catch (error) {
      alert('Error al formatear la fecha y hora. Por favor, verifica que sean válidas.');
      console.error('Error:', error);
    }
  }

  // Función para resetear el formulario
  function resetearFormulario() {
    // Restablecer el textarea al texto original
    const textarea = document.getElementById('descripcion');
    textarea.value = `Se llevara a cabo

Para cualquier pregunta o inconveniente, por favor, contacten a la administración.
Agradecemos su comprensión y cooperación.
Atentamente, [Nombre del Responsable].
Administración del Conjunto Residencial [Nombre conjunto residencial].`;

    // Restablecer fecha y hora a valores actuales
    const ahora = new Date();
    document.getElementById('fechaEvento').value = ahora.toISOString().split('T')[0];
    document.getElementById('horaEvento').value = ahora.toTimeString().slice(0, 5);
  }

  // Hacer las funciones globales para que puedan ser llamadas desde HTML
  window.insertarFechaHora = insertarFechaHora;
  window.resetearFormulario = resetearFormulario;

  // Event listener para el botón enviar
  enviarBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const destinatario = document.getElementById('destinatario').value;
    const asunto = document.getElementById('asunto').value.trim();
    const fecha = document.getElementById('fechaEvento').value;
    const hora = document.getElementById('horaEvento').value;
    const descripcion = document.getElementById('descripcion').value.trim();
    const imagen = document.querySelector('input[name="zone-images"]').files.length;

    // Validaciones
    if (!destinatario || destinatario === 'Seleccione un Destinatario' || destinatario === '') {
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

    if (imagen === 0) {
      alert('Por favor adjunta al menos una imagen.');
      return;
    }

    if (!descripcion.includes('Atentamente')) {
      alert('Asegúrate de incluir toda la información en la descripción.');
      return;
    }

    // Si todas las validaciones pasan
    alert('¡Formulario enviado correctamente!');
    form.submit(); // Envía el formulario al PHP
  });

  // Event listener para el botón cancelar
  if (cancelarBtn) {
    cancelarBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (confirm('¿Estás seguro de que quieres cancelar? Esto limpiará el formulario.')) {
        resetearFormulario();
        // También limpiar otros campos
        document.getElementById('destinatario').value = '';
        document.getElementById('asunto').value = '';
        document.querySelector('input[name="zone-images"]').value = '';
      }
    });
  }
});