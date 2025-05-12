document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("visit-form");
  const registrarBtn = document.getElementById("Registrar");
  const limpiarBtn = document.getElementById("Limpiarbtn");

  registrarBtn.addEventListener("click", function (e) {
    e.preventDefault(); // Evita que se envíe el formulario

    let errores = [];

    const nombre = form.querySelector("input[placeholder='Nombre Completo']");
    const tipoDoc = form.querySelector("select");
    const numDoc = form.querySelector("input[placeholder='Número Documento']");
    const email = form.querySelector("input[placeholder='Email']");
    const torre = form.querySelector("input[placeholder='Num. Torre Visitada']");
    const apto = form.querySelector("input[placeholder='Num. Apto Visitado']");
    const fechas = form.querySelectorAll("input[type='date']");
    const fechaEntrada = fechas[0];
    const fechaSalida = fechas[1];

    const trim = (input) => input.value.trim();

    if (!trim(nombre)) errores.push("El nombre completo es obligatorio.");
    if (!tipoDoc.value || tipoDoc.value === "Tipo Doc.") errores.push("Selecciona un tipo de documento.");
    if (!trim(numDoc) || !/^\d+$/.test(trim(numDoc))) errores.push("El número de documento debe ser numérico.");
    if (!trim(email) || !/^\S+@\S+\.\S+$/.test(trim(email))) errores.push("El correo electrónico no es válido.");
    if (!trim(torre)) errores.push("El número de torre es obligatorio.");
    if (!trim(apto)) errores.push("El número de apartamento es obligatorio.");
    if (!fechaEntrada.value) errores.push("La fecha de entrada es obligatoria.");
    if (!fechaSalida.value) errores.push("La fecha de salida es obligatoria.");

    if (errores.length > 0) {
      alert("Por favor corrige los siguientes errores:\n\n" + errores.join("\n"));
    } else {
      alert("✅ Registro exitoso");
      // Puedes activar esto si quieres enviar el formulario
      // form.submit();
    }
  });

  limpiarBtn.addEventListener("click", function () {
    if (confirm("¿Estás seguro de que deseas limpiar el formulario?")) {
      form.reset();
    }
  });
});