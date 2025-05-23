document.getElementById('formulario_ingreso').addEventListener('submit', function (e) {

    const email = this.Email.value;
    const password = this.Password.value;

    if (!email.includes('@')) {
        alert("Correo electrónico no válido.");
        e.preventDefault();
    }

    if (password.trim().length < 6) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        e.preventDefault();
    }
});