function abrirModal() {
    document.getElementById('modalEliminar').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalEliminar').style.display = 'none';
}

function eliminarPublicacion() {
    document.getElementById('formEliminar').submit();
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function (event) {
    const modal = document.getElementById('modalEliminar');
    if (event.target === modal) {
        cerrarModal();
    }
}

// Cerrar modal con la tecla Escape
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        cerrarModal();
    }
});
