window.addEventListener("DOMContentLoaded",()  => {

    document.getElementById('form_update_password').addEventListener('submit', function (e) {
        const inputs = this.querySelectorAll('input');
        for (let input of inputs) {
            if (!input.value.trim()) {
                alert(`Por favor completa el campo: ${input.name}`);
                input.focus();
                e.preventDefault();
                return false;
            }
        }
    
        const password = this.password.value;
        let expresionRegularPassword = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,15}$/gm;
    
        if (expresionRegularPassword.test(password)==false) {
            alert("La contraseña debe tener al entre 8 y 10 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula. Puede tener otros símbolos");
            e.preventDefault();
        }
    });
});
    