document.getElementById('enviarBtn').addEventListener('click', function (e) {
    e.preventDefault(); // Previene envío inmediato del formulario

    const formulario = document.querySelector('#formularioCobro form');

    // Captura de valores del formulario
    const nombre = formulario.querySelector('input[name="nombre_residente"]').value;
    const placa = formulario.querySelector('input[name="placa"]').value;
    const parqueadero = formulario.querySelector('input[name="num_parqueadero"]').value;
    const tipoVehiculo = formulario.querySelector('select[name="tipo"]').value; // Nombre corregido
    const ingresoFecha = formulario.querySelector('input[name="fecha_ingreso"]').value;
    const salidaFecha = formulario.querySelector('input[name="fecha_salida"]').value;
    const ingresoHora = formulario.querySelector('input[name="hora_ingreso"]').value;
    const salidaHora = formulario.querySelector('input[name="hora_salida"]').value;

    // Validación rápida antes de enviar
    if (!placa || !tipoVehiculo || !ingresoFecha || !salidaFecha || !ingresoHora || !salidaHora) {
        Swal.fire("Campos incompletos", "Por favor completa todos los campos requeridos.", "warning");
        return;
    }

    // Concatenamos fecha y hora en formato válido
    const ingreso = `${ingresoFecha} ${ingresoHora}`;
    const salida = `${salidaFecha} ${salidaHora}`;

    // Preparamos los datos a enviar al controlador PHP
    const datos = new URLSearchParams();
    datos.append('placa', placa);
    datos.append('tipo', tipoVehiculo);
    datos.append('hora_ingreso', ingreso);
    datos.append('hora_salida', salida);

    fetch('/ZoneMaison2025/controller/generarCostoParqueadero.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: datos
    })
    .then(res => res.json())
    .then(data => {
        if (!data.costo_neto) {
            Swal.fire("Error", "El cálculo del costo no devolvió un valor válido.", "error");
            return;
        }

        // Asignar el costo recibido al input oculto
        formulario.querySelector('#campoCosto').value = data.costo_neto;

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
                    <p><strong>Nombre:</strong> ${nombre}</p>
                    <p><strong>Placa:</strong> ${placa}</p>
                    <p><strong>Tipo:</strong> ${tipoVehiculo}</p>
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
                    formulario.action = '/ZoneMaison2025/controller/recibirDatosAlquiler.php';
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
