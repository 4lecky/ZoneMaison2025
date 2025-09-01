document.addEventListener('DOMContentLoaded', () => {
  const hoy = new Date();
  const fechaInput = document.getElementById("fecha");
  const horaInput = document.getElementById("hora");

  const numeroDocInput = document.getElementById('numero_doc');
  const destinatarioInput = document.getElementById('paqu_Destinatario');
  const inputCedulaOculta = document.getElementById('cedula_oculta');

  // Buscar nombre desde backend usando AJAX
  numeroDocInput.addEventListener('input', function () {
    const cedula = this.value.trim();

    if (cedula.length >= 5) {
      fetch(`../controller/buscar_usuario.php?cedula=${encodeURIComponent(cedula)}`)
        .then(response => response.json())
        .then(data => {
          if (data.nombre) {
            destinatarioInput.value = data.nombre;
            if (inputCedulaOculta) inputCedulaOculta.value = cedula;
          } else {
            destinatarioInput.value = '';
            if (inputCedulaOculta) inputCedulaOculta.value = '';
          }
        })
        .catch(err => {
          console.error('Error al consultar el usuario:', err);
          destinatarioInput.value = '';
          if (inputCedulaOculta) inputCedulaOculta.value = '';
        });
    } else {
      destinatarioInput.value = '';
      if (inputCedulaOculta) inputCedulaOculta.value = '';
    }
  });

  if (fechaInput) fechaInput.value = hoy.toISOString().split("T")[0];
  if (horaInput) horaInput.value = hoy.toTimeString().slice(0, 5); // HH:MM

  const enviarBtn = document.querySelector('.Enviar');
  const formulario = document.querySelector('form');

  enviarBtn.addEventListener('click', function (e) {
    e.preventDefault(); // Evitar el envío del formulario

    const tipo_doc = document.querySelector('[name="tipo_doc"]').value;
    const numero_doc = numeroDocInput.value.trim();
    const destinatario = destinatarioInput.value;
    const asunto = document.querySelector('[name="asunto"]').value.trim();
    const fecha = fechaInput.value;
    const hora = horaInput.value;
    const descripcion = document.querySelector('[name="descripcion"]').value.trim();
    const estado = document.querySelector('[name="estado"]').value;

    // Validación básica
    if (!tipo_doc) return alert('Por favor seleccione el tipo de documento.');
    if (!numero_doc || isNaN(numero_doc)) return alert('Número de documento inválido.');
    if (!destinatario) return alert('Selecciona un destinatario válido.');
    if (asunto.length < 5) return alert('El asunto debe tener al menos 5 caracteres.');
    if (!fecha) return alert('Selecciona una fecha.');
    if (!hora) return alert('Selecciona una hora.');
    if (!descripcion) return alert('Incluye una descripción.');
    if (!estado) return alert('Selecciona el estado.');

    formulario.submit();
  });

});