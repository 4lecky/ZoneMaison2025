document.addEventListener('DOMContentLoaded', () => {
  const formVisitante = document.getElementById('formVisitante') || document.querySelector('form'); 

  const btnRegistrar = document.getElementById('btnRegistrar');
  const btnLimpiar   = document.getElementById('btnLimpiar');
  const btnEditar    = document.getElementById('btnEditar');
  const btnConfirmar = document.querySelector('.boton.confirmar'); // 👈 para editar.php

  /* Expresiones regulares */
  const emailRegex    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const telefonoRegex = /^\d{7,10}$/;
  const dateRegex     = /^\d{4}-\d{2}-\d{2}$/;
  const horaRegex     = /^\d{2}:\d{2}$/;

  const camposTipo = {
    email: emailRegex,
    telefono: telefonoRegex,
    date: dateRegex,
    hora: horaRegex
  };

  function mostrarError(campo, mensaje) {
    campo.classList.add('campo-error');
    let existente = campo.parentElement.querySelector('.error-msg');
    if (existente) existente.remove();

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

  function validarFormulario(form) {
    const campos        = Array.from(form.querySelectorAll('input, select, textarea'));
    const camposError   = [];
    let primerError     = null;

    campos.forEach(campo => {
      limpiarError(campo);
      const tipoEsperado = campo.getAttribute('data-validate');
      const valor        = campo.value.trim();

      if (campo.tagName === 'SELECT' && (valor === '' || campo.selectedIndex === 0)) {
        camposError.push(campo);
        mostrarError(campo, 'Debes seleccionar una opción');
        if (!primerError) primerError = campo;
      }
      else if (valor === '') {
        camposError.push(campo);
        mostrarError(campo, 'Este campo es obligatorio');
        if (!primerError) primerError = campo;
      }
      else if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
        camposError.push(campo);
        mostrarError(campo, `Formato inválido (${tipoEsperado})`);
        if (!primerError) primerError = campo;
      }
    });

    if (primerError) primerError.focus();
    return camposError;
  }

  function manejarClick(accion) {
    if (accion === 'limpiar') {
      if (confirm('¿Seguro que deseas limpiar todos los campos?')) {
        formVisitante.reset();
        formVisitante.querySelectorAll('.error-msg').forEach(e => e.remove());
        formVisitante.querySelectorAll('.campo-error').forEach(c => c.classList.remove('campo-error'));
      }
      return;
    }

    const errores = validarFormulario(formVisitante);
    if (errores.length) return;

    if (accion === 'registrar') {
      if (confirm('Confirma que todos los datos estén correctos antes de registrar.')) {
        formVisitante.submit();
      }
    } 
    else if (accion === 'editar') {
      if (confirm('¿Seguro que quieres editar una visita?')) {
        formVisitante.submit();
      }
    } 
    else if (accion === 'confirmar') {   // 👈 nuevo para editar.php
      if (confirm('¿Deseas confirmar los cambios de esta visita?')) {
        formVisitante.submit();
      }
    }
  }

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

  if (btnConfirmar) {   // 👈 ahora el botón de editar.php funciona
    btnConfirmar.addEventListener('click', e => {
      e.preventDefault();
      manejarClick('confirmar');
    });
  }

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

setTimeout(() => {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
