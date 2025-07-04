document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulario_ingreso');
    if (form) {
        form.addEventListener('submit', function (e) {
            const email = this.Email.value;
            const password = this.Password.value;
    
            if (!email.includes('@')) {
                alert("Correo electrónico no válido.");
                e.preventDefault();
            }
    
            if (password.trim().length < 8) {
                alert("La contraseña debe tener al menos 8 caracteres.");
                e.preventDefault();
            }
        });
    }
    
    const togglePassword = document.getElementById('toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password-input');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('ri-eye-off-fill');
                eyeIcon.classList.add('ri-eye-fill');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('ri-eye-fill');
                eyeIcon.classList.add('ri-eye-off-fill');
            }
        });
    }
});