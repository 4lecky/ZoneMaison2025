//   Java para cambiar entre formularios

function mostrarFormulario(formularioId) {
    // Oculta todos los formularios
    document.getElementById('formularioRegistro').style.display = 'none';
    document.getElementById('formularioCobro').style.display = 'none';

    // Muestra el formulario deseado
    const formulario = document.getElementById(formularioId);
    if (formulario) {
        formulario.style.display = 'block';
    }
}
