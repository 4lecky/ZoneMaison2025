document.getElementById('enviarBtn').addEventListener('click', function (e) {
    e.preventDefault(); // Previene envío inmediato del formulario

    const formulario = document.querySelector(".formulario-Cobro");

    // Construimos los datos a enviar con todo el formulario
    const datos = new FormData(formulario);

    fetch('../controller/recibirDatosAlquiler.php', {
        method: 'POST',
        body: datos
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            Swal.fire("Error", data.error, "error");
            return;
        }

        // Asignar precio recibido al input oculto
        formulario.querySelector('#campoCosto').value = data.calculo.costo_neto;

        // Extraer datos del JSON
        const alquiler = data.alquiler;
        const calculo = data.calculo;

        // Mostrar el recibo con SweetAlert
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
                    <p><strong>Nombre:</strong> ${alquiler.nombre}</p>
                    <p><strong>Placa:</strong> ${alquiler.placa}</p>
                    <p><strong>Parqueadero:</strong> ${alquiler.numParqueadero}</p>
                    <p><strong>Ingreso:</strong> ${alquiler.horaIngreso}</p>
                    <p><strong>Salida:</strong> ${alquiler.fechaSalida}</p>
                    <p><strong>Hora de Salida:</strong> ${alquiler.horaSalida}</p>
                    <p><strong>Horas de uso:</strong> ${calculo.horas}</p>
                    <p><strong>Total Pagado:</strong> $${calculo.precio} COP</p>
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
                // El registro ya se insertó en PHP, no necesitamos volver a enviar
                Swal.fire("✅ Guardado", "El registro fue insertado correctamente.", "success");
            }
        });
    })
    .catch(error => {
        console.error("Error al procesar el alquiler:", error);
        Swal.fire("Error", "No se pudo procesar el alquiler. Intenta de nuevo.", "error");
    });
});
