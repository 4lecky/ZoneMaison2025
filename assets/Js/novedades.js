document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-vermas').forEach(btn => {
    const parent = btn.closest('.Descripcion');
    const p = parent ? parent.querySelector('.texto-muro') : null;

    if (!p) return;

    if (p.scrollHeight <= p.offsetHeight) {
      btn.style.display = 'none';
      return;
    }

    btn.addEventListener('click', () => {
      const expanded = !p.classList.toggle('texto-muro');
      btn.querySelector('.text').textContent = expanded ? 'Ver menos' : 'Ver más';
<<<<<<< HEAD
=======


document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-button');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('¿Estás seguro de que quieres editar esta publicación?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});

function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta publicación? Esta acción no se puede deshacer.')) {
        window.location.href = 'eliminar_publicacion.php?id=' + id;
    }
}
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
    });
  });
});
