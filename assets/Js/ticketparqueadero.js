document.getElementById('enviarBtn').addEventListener('click', function (e) {
    e.preventDefault(); // Previene envío inmediato del formulario

    const formulario = document.querySelector(".formulario-Cobro");

    // Obtenemos los valores clave del formulario
    const placa = formulario.querySelector('input[name="alqu_placa"]').value;
    const tipoDoc = formulario.querySelector('select[name="alqu_tipo_doc_vehi"]').value;
    const numDoc = formulario.querySelector('input[name="alqu_num_doc_vehi"]').value;
    const nombre = formulario.querySelector('input[name="alqu_nombre_propietario"]').value;
    const torre = formulario.querySelector('input[name="alqu_torre"]').value;
    const apartamento = formulario.querySelector('input[name="alqu_apartamento"]').value;
    const numParqueadero = formulario.querySelector('input[name="alqu_numeroParqueadero"]').value;
    const estadoSalida = formulario.querySelector('textarea[name="alqu_estadoSalida"]').value;
    const fechaSalida = formulario.querySelector('input[name="alqu_fecha_salida"]').value;
    const horaSalida = formulario.querySelector('input[name="alqu_hora_salida"]').value;

    if (!nombre || !fechaSalida || !horaSalida) {
        alert("Por favor, completa los campos obligatorios.");
        return;
    }

    // Concatenamos fecha y hora de salida
    const salida = `${fechaSalida} ${horaSalida}`;

    // Preparamos datos a enviar al PHP
    const datos = new URLSearchParams();
    datos.append('placa', placa);
    datos.append('hora_salida', salida);


    fetch('../controller/generarCostoParqueadero.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: datos
    })
    .then(res => res.text())  // <- primero como texto para ver qué devuelve
    .then(text => {
        console.log(text); // mira la respuesta real
        return JSON.parse(text); // luego parsea
    })

    .then(data => {
        if (data.error) {
            Swal.fire("Error", data.error, "error");
            return;
        }

        // Asignar costo recibido al input oculto
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
                    <p><strong>Parqueadero:</strong> ${numParqueadero}</p>
                    <p><strong>Ingreso:</strong> ${data.hora_ingreso}</p>
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
                    formulario.action = '../controller/recibirDatosAlquiler.php';
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
