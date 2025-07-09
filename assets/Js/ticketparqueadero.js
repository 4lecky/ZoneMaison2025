

document.getElementById('enviarBtn').addEventListener('click', function (e) {
    e.preventDefault(); // Previene envío inmediato del formulario

    const formulario = document.querySelector('#formularioCobro form');

    // Captura de valores del formulario
    const nombre = formulario.querySelector('input[name="nombre_residente"]').value;
    const placa = formulario.querySelector('input[name="placa"]').value;
    const parqueadero = formulario.querySelector('input[name="parqueadero"]').value;
    const ingreso = formulario.querySelector('input[name="hora_ingreso"]').value;
    const salida = formulario.querySelector('input[name="hora_salida"]').value;

    // Preparamos los datos a enviar
    const datos = new URLSearchParams();
    datos.append('placa', placa);
    datos.append('tipo', 'carro'); // puedes cambiar si usas tipo de vehículo
    datos.append('hora_ingreso', ingreso);
    datos.append('hora_salida', salida);

    // ✅ Asegúrate que esta ruta sea válida desde donde se carga el archivo JS
    fetch('/ZoneMaison2025/controller/generarCostoParqueadero.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: datos
    })
    .then(res => res.json())
    .then(data => {
        // ✅ Asignar el costo recibido al input oculto
        const campoCosto = formulario.querySelector('#campoCosto');
        campoCosto.value = data.costo;

        // ✅ Mostrar el recibo con SweetAlert
        Swal.fire({
            title: '<strong>🧾 Recibo de Parqueadero</strong>',
            icon: 'info',
            html: `
                <div style="
                    font-family: 'Courier New', monospace;
                    border: 2px dashed #333;
                    padding: 20px;
                    background-color: #fff;
                    color: #000;
                    text-align: left;
                    max-width: 350px;
                    margin: auto;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                ">
                    <h3 style="text-align: center; margin-bottom: 10px;">PARKING RECEIPT</h3>
                    <hr style="border: none; border-top: 1px dashed #aaa;">
                    <p><strong>Nombre:</strong> ${nombre}</p>
                    <p><strong>Placa:</strong> ${placa}</p>
                    <p><strong>Parqueadero:</strong> ${parqueadero}</p>
                    <p><strong>Ingreso:</strong> ${ingreso}</p>
                    <p><strong>Salida:</strong> ${salida}</p>
                    <p><strong>Total Pagado:</strong> $${data.costo} COP</p>
                    <hr style="border: none; border-top: 1px dashed #aaa;">
                    <p style="text-align: center;">¡Gracias por su visita y conduzca seguro!</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                if (formulario.checkValidity()) {
                    formulario.action = '/ZoneMaison2025/controller/recibirDatosAlquiler.php'; // Ajusta si tu estructura es distinta
                    formulario.submit();
                } else {
                    formulario.reportValidity();
                }
            }
        });
    })
    .catch(error => {
        console.error("Error al calcular el costo:", error);
        Swal.fire("Error", "No se pudo calcular el costo. Intenta de nuevo.", "error");
    });
});







// // SweetAlert2 CDN

// src="https://cdn.jsdelivr.net/npm/sweetalert2@11">


// document.getElementById('enviarBtn').addEventListener('click', function (e) {
//   e.preventDefault(); // Previene envío inmediato del formulario

//   const formulario = document.querySelector('#formularioCobro form');

//   // Captura de valores del formulario
//   const nombre = document.querySelector('input[name="nombre_residente"]').value;
//   const placa = document.querySelector('input[name="placa"]').value;
//   const parqueadero = document.querySelector('input[name="parqueadero"]').value;
//   const ingreso = document.querySelector('input[name="hora_ingreso"]').value;
//   const salida = document.querySelector('input[name="hora_salida"]').value;
//   const tarifa = document.querySelector('input[name="tarifa_total"]')?.value || '0.00';

//   // SweetAlert con diseño estilo recibo
//   Swal.fire({
//     title: '<strong>🧾 Recibo de Parqueadero</strong>',
//     icon: 'info',
//     html: `
//       <div style="
//         font-family: 'Courier New', monospace;
//         border: 2px dashed #333;
//         padding: 20px;
//         background-color: #fff;
//         color: #000;
//         text-align: left;
//         max-width: 350px;
//         margin: auto;
//         box-shadow: 0 0 10px rgba(0,0,0,0.1);
//       ">
//         <h3 style="text-align: center; margin-bottom: 10px;">PARKING RECEIPT</h3>
//         <hr style="border: none; border-top: 1px dashed #aaa;">
//         <p><strong>Nombre:</strong> ${nombre}</p>
//         <p><strong>Placa:</strong> ${placa}</p>
//         <p><strong>Parqueadero:</strong> ${parqueadero}</p>
//         <p><strong>Ingreso:</strong> ${ingreso}</p>
//         <p><strong>Salida:</strong> ${salida}</p>
//         <p><strong>Total Pagado:</strong> $${tarifa}</p>
//         <hr style="border: none; border-top: 1px dashed #aaa;">
//         <p style="text-align: center;">¡Gracias por su visita y conduzca seguro!</p>
//       </div>
//     `,
//     showCancelButton: true,
//     confirmButtonText: 'Confirmar',
//     cancelButtonText: 'Cancelar',
//     confirmButtonColor: '#28a745',
//     cancelButtonColor: '#d33',
//   }).then((result) => {
//     if (result.isConfirmed) {
//       formulario.submit(); // Enviar el formulario si se confirma
//     }
//   });
// });









// document.getElementById('enviarBtn').addEventListener('click', function () {
//   const formulario = document.querySelector('#formularioCobro form');
//   const placa = document.querySelector('input[name="num_parqueadero"]').value;
//   const nombre = document.querySelector('input[name="nombre_residente"]').value;
//   const ingreso = document.querySelector('input[name="hora_ingreso"]').value;
//   const salida = document.querySelector('input[name="hora_salida"]').value;

//   Swal.fire({
//     title: 'Ticket de Parqueadero',
//     html: `
//       <div style="background: white; padding: 20px; font-family: monospace; border: 2px dashed #000; max-width: 300px; margin: auto;">
//         <div style="text-align: center; margin-bottom: 10px;">
//           <img src="https://cdn-icons-png.flaticon.com/512/61/61449.png" width="50" alt="Car icon" />
//           <h3 style="margin: 5px 0;">PARKING RECEIPT</h3>
//         </div>
//         <div style="font-size: 14px; text-align: left;">
//           <strong>Nombre:</strong> ${nombre}<br>
//           <strong>Parqueadero:</strong> ${placa}<br>
//           <strong>Hora Ingreso:</strong> ${ingreso}<br>
//           <strong>Hora Salida:</strong> ${salida}<br>
//           <strong>Valor Pagado:</strong> $5.000 COP
//         </div>
//         <hr style="margin: 10px 0;">
//         <div style="text-align: center; font-size: 12px;">¡Gracias y conduzca seguro!</div>
//       </div>
//     `,
//     showConfirmButton: true,
//     confirmButtonText: 'Aceptar',
//     icon: 'success'
//   }).then(() => {
//     formulario.submit();
//   });
// });