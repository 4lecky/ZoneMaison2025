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
    });
  });
});
