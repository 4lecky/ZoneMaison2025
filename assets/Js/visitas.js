document.addEventListener('DOMContentLoaded', () => {
  const formVisitante = document.getElementById('formVisitante');
  const formVisita = document.getElementById('formVisita');

  const btnRegistrar = document.getElementById('btnRegistrar');
  const btnLimpiar = document.getElementById('btnLimpiar');
  const btnEditar = document.getElementById('btnEditar');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const telefonoRegex = /^\d+$/;
  const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;

  const camposTipo = {
      email: emailRegex,
      telefono: telefonoRegex,
      date: dateRegex
  };

  function validarFormulario(form) {
      const campos = Array.from(form.querySelectorAll('input, select, textarea'));
      const camposVacios = [];
      let primerCampoErroneo = null;

      campos.forEach(campo => {
          campo.classList.remove('campo-error');

          const tipoEsperado = campo.getAttribute('data-validate');
          const valor = campo.value.trim();

          if (valor === '') {
              camposVacios.push(campo);
              if (!primerCampoErroneo) primerCampoErroneo = campo;
          } else if (tipoEsperado && camposTipo[tipoEsperado] && !camposTipo[tipoEsperado].test(valor)) {
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
          const label = campos[0].placeholder || campos[0].name || "un campo";
          alert(`Falta llenar ${label} en ${nombreFormulario}`);
      } else {
          alert(`Faltan llenar más campos en ${nombreFormulario}`);
      }
  }

  function manejarClick(boton) {
      if (boton === 'limpiar') {
          const confirmacion = confirm("¿Seguro que deseas limpiar todos los formularios?");
          if (confirmacion) {
              formVisitante.reset();
              formVisita.reset();
          }
      }

      if (boton === 'registrar') {
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

          const confirmar = confirm("Confirma que todos los datos estén correctos");
          if (confirmar) {
              alert("Formulario registrado correctamente (aquí puedes hacer el submit)");
             // form.submit(); //si deseas enviarlo
          }
      }

      if (boton === 'editar') {
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

          const confirmar = confirm("¿Seguro que quieres editar una visita?");
          if (confirmar) {
              alert("Visita editada correctamente (aquí puedes hacer el submit)");
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