document.addEventListener('DOMContentLoaded', () => {
  const formVisitante = document.getElementById('formVisitante');

  const btnRegistrar = document.getElementById('btnRegistrar');
  const btnLimpiar   = document.getElementById('btnLimpiar');
  const btnEditar    = document.getElementById('btnEditar');  // puede crearse dinámicamente

  /* Expresiones regulares */
  const emailRegex    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const telefonoRegex = /^\d{7,10}$/;   // solo números de 7 a 10 dígitos
  const dateRegex     = /^\d{4}-\d{2}-\d{2}$/;
  const horaRegex     = /^\d{2}:\d{2}$/;

  const camposTipo = {
    email: emailRegex,
    telefono: telefonoRegex,
    date: dateRegex,
    hora: horaRegex
  };

  /* ---------- Funciones para ayudas contextuales ---------- */
  function mostrarError(campo, mensaje) {
    campo.classList.add('campo-error');

    // Si ya hay un mensaje, lo borra primero
    let existente = campo.parentElement.querySelector('.error-msg');
    if (existente) existente.remove();

    // Crear nuevo mensaje
    const span = document.createElement('span');
    span.className = 'error-msg';
    span.textContent = mensaje;
    campo.parentElement.appendChild(span);
  }

  function limpiarError(campo) {
    campo.classList.remove('campo-error');
    let existente = campo.parentElement.querySelector('.error-msg');
    if (existente) existente.remove();
  }

  /* ---------- Validación ---------- */
  function validarFormulario(form) {
    const campos        = Array.from(form.querySelectorAll('input, select, textarea'));
    const camposError   = [];
    let primerError     = null;

    campos.forEach(campo => {
      limpiarError(campo);  // limpiar mensajes previos
      const tipoEsperado = campo.getAttribute('data-validate');
      const valor        = campo.value.trim();

      // 1. Select sin opción válida
      if (campo.tagName === 'SELECT' && (valor === '' || campo.selectedIndex === 0)) {
        camposError.push(campo);
        mostrarError(campo, 'Debes seleccionar una opción');
        if (!primerError) primerError = campo;
      }
      // 2. Campo vacío
      else if (valor === '') {
        camposError.push(campo);
        mostrarError(campo, 'Este campo es obligatorio');
        if (!primerError) primerError = campo;
      }
      // 3. Formato incorrecto
      else if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
        camposError.push(campo);
        mostrarError(campo, `Formato inválido (${tipoEsperado})`);
        if (!primerError) primerError = campo;
      }
    });

    if (primerError) primerError.focus();
    return camposError;
  }

  /* ---------- Acciones de botones ---------- */
  function manejarClick(accion) {
    if (accion === 'limpiar') {
      if (confirm('¿Seguro que deseas limpiar todos los campos?')) {
        formVisitante.reset();
        // limpiar mensajes de error
        formVisitante.querySelectorAll('.error-msg').forEach(e => e.remove());
        formVisitante.querySelectorAll('.campo-error').forEach(c => c.classList.remove('campo-error'));
      }
      return;
    }

    // Registrar o editar: primero validar
    const errores = validarFormulario(formVisitante);
    if (errores.length) {
      return; // ya se mostraron los mensajes
    }

    if (accion === 'registrar') {
      if (confirm('Confirma que todos los datos estén correctos antes de registrar.')) {
        formVisitante.submit();            // ← ¡envío real!
      }
    } else if (accion === 'editar') {
      if (confirm('¿Seguro que quieres editar una visita?')) {
        alert('Visita editada correctamente');
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

  /* Validación en tiempo real (mientras escribe) */
  formVisitante.querySelectorAll('input, select, textarea').forEach(campo => {
    campo.addEventListener('input', () => {
      limpiarError(campo);
      const tipoEsperado = campo.getAttribute('data-validate');
      const valor = campo.value.trim();
      if (valor === '') return;
      if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
        mostrarError(campo, `Formato inválido (${tipoEsperado})`);
      }
    });
  });

});
