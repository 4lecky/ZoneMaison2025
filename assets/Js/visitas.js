document.addEventListener('DOMContentLoaded', () => {
  const formVisitante = document.getElementById('formVisitante');
  const formVisita = document.getElementById('formVisita');

  const btnRegistrar = document.getElementById('btnRegistrar');
  const btnLimpiar = document.getElementById('btnLimpiar');
  const btnEditar = document.getElementById('btnEditar');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const telefonoRegex = /^\d+$/;
  const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
  const horaRegex = /^\d{2}:\d{2}$/;

  const camposTipo = {
    email: emailRegex,
    telefono: telefonoRegex,
    date: dateRegex,
    hora: horaRegex
  };

  function validarFormulario(form) {
    const campos = Array.from(form.querySelectorAll('input, select, textarea'));
    const camposVacios = [];
    let primerCampoErroneo = null;

    campos.forEach(campo => {
      campo.classList.remove('campo-error');
      const tipoEsperado = campo.getAttribute('data-validate');
      const valor = campo.value.trim();

      // Validar select con opción deshabilitada
      if (campo.tagName === 'SELECT' && (valor === '' || campo.selectedIndex === 0)) {
        camposVacios.push(campo);
        if (!primerCampoErroneo) primerCampoErroneo = campo;
      } 
      // Validar campos vacíos
      else if (valor === '') {
        camposVacios.push(campo);
        if (!primerCampoErroneo) primerCampoErroneo = campo;
      } 
      // Validar formato incorrecto
      else if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
        camposVacios.push(campo);
        if (!primerCampoErroneo) primerCampoErroneo = campo;
      }
    });

    if (camposVacios.length > 0) {
      camposVacios.forEach(c => c.classList.add('campo-error'));
      if (primerCampoErroneo) primerCampoErroneo.focus();
    }

    return camposVacios;
  }

  function mostrarMensajeValidacion(campos, nombreFormulario) {
    if (campos.length === 1) {
      const campo = campos[0];
      const label = campo.labels?.[0]?.innerText || campo.placeholder || campo.name || "un campo";
      alert(`Falta llenar: ${label} en ${nombreFormulario}`);
    } else {
      alert(`Faltan llenar varios campos en ${nombreFormulario}`);
    }
  }

  function manejarClick(boton) {
    if (boton === 'btnLimpiar') {
      const confirmacion = confirm("¿Seguro que deseas limpiar todos los formularios?");
      if (confirmacion) {
        formVisitante.reset();
        formVisita.reset();
      }
    }

    if (boton === 'btnRegistrar' || boton === 'editar') {
      const erroresVisitante = validarFormulario(formVisitante);
      const erroresVisita = validarFormulario(formVisita);

      if (erroresVisitante.length > 0) {
        mostrarMensajeValidacion(erroresVisitante, "Datos del Visitante");
        return;
      }

      if (erroresVisita.length > 0) {
        mostrarMensajeValidacion(erroresVisita, "Datos de Visita");
        return;
      }

      const mensaje = boton === 'editar'
        ? "¿Seguro que quieres editar una visita?"
        : "Confirma que todos los datos estén correctos antes de registrar.";

      const confirmar = confirm(mensaje);
      if (confirmar) {
        alert(boton === 'editar' ? "Visita editada correctamente" : "Formulario registrado correctamente");
        // form.submit(); // Descomentar si quieres enviar el formulario
      }
    }
  }

  btnLimpiar.addEventListener('click', e => {
    e.preventDefault();
    manejarClick('btnLimpiar');
  });

  btnRegistrar.addEventListener('click', e => {
    e.preventDefault();
    manejarClick('btnRegistrar');
  });

  btnEditar.addEventListener('click', e => {
    e.preventDefault();
    manejarClick('editar');
  });
});

