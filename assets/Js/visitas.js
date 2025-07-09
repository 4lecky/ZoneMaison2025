document.addEventListener('DOMContentLoaded', () => {
  const formVisitante = document.getElementById('formVisitante');

  const btnRegistrar = document.getElementById('btnRegistrar');
  const btnLimpiar   = document.getElementById('btnLimpiar');
  const btnEditar    = document.getElementById('btnEditar');  // puede crearse dinámicamente

  /* Expresiones regulares */
  const emailRegex    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const telefonoRegex = /^\d+$/;
  const dateRegex     = /^\d{4}-\d{2}-\d{2}$/;
  const horaRegex     = /^\d{2}:\d{2}$/;

  const camposTipo = {
    email: emailRegex,
    telefono: telefonoRegex,
    date: dateRegex,
    hora: horaRegex
  };

  /* ---------- Validación ---------- */
  function validarFormulario(form) {
    const campos        = Array.from(form.querySelectorAll('input, select, textarea'));
    const camposError   = [];
    let primerError     = null;

    campos.forEach(campo => {
      campo.classList.remove('campo-error');
      const tipoEsperado = campo.getAttribute('data-validate');
      const valor        = campo.value.trim();

      // 1. Select sin opción válida
      if (
        campo.tagName === 'SELECT' &&
        (valor === '' || campo.selectedIndex === 0)
      ) {
        camposError.push(campo);
        if (!primerError) primerError = campo;
      }
      // 2. Campo vacío
      else if (valor === '') {
        camposError.push(campo);
        if (!primerError) primerError = campo;
      }
      // 3. Formato incorrecto
      else if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
        camposError.push(campo);
        if (!primerError) primerError = campo;
      }
    });

    if (camposError.length) {
      camposError.forEach(c => c.classList.add('campo-error'));
      if (primerError) primerError.focus();
    }

    return camposError;
  }

  function mostrarMensajeValidacion(campos) {
    if (campos.length === 1) {
      const campo = campos[0];
      const label = campo.labels?.[0]?.innerText || campo.placeholder || campo.name || 'un campo';
      alert(`Falta llenar: ${label}`);
    } else {
      alert('Faltan llenar varios campos');
    }
  }

  /* ---------- Acciones de botones ---------- */
  function manejarClick(accion) {
    if (accion === 'limpiar') {
      if (confirm('¿Seguro que deseas limpiar todos los campos?')) {
        formVisitante.reset();
      }
      return;
    }

    // Registrar o editar: primero validar
    const errores = validarFormulario(formVisitante);
    if (errores.length) {
      mostrarMensajeValidacion(errores);
      return;
    }

    if (accion === 'registrar') {
      if (confirm('Confirma que todos los datos estén correctos antes de registrar.')) {
        formVisitante.submit();            // ← ¡envío real!
      }
    } else if (accion === 'editar') {
      if (confirm('¿Seguro que quieres editar una visita?')) {
        alert('Visita editada correctamente');
        // Aquí iría tu lógica de edición (AJAX, recarga de tabla, etc.)
      }
    }
  }

  /* ---------- Listeners ---------- */
  if (btnLimpiar) {
    btnLimpiar.addEventListener('click', e => {
      e.preventDefault();
      manejarClick('limpiar');
    });
  }

  if (btnRegistrar) {
    btnRegistrar.addEventListener('click', e => {
      e.preventDefault();
      manejarClick('registrar');
    });
  }

  if (btnEditar) {
    btnEditar.addEventListener('click', e => {
      e.preventDefault();
      manejarClick('editar');
    });
  }
});
