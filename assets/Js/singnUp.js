
window.addEventListener("DOMContentLoaded",(event)  => {

    document.getElementById('formulario_ingresosRe').addEventListener('submit', function (e) {
        const inputs = this.querySelectorAll('input, select');
        for (let input of inputs) {
            if (!input.value.trim()) {
                alert(`Por favor completa el campo: ${input.name}`);
                input.focus();
                e.preventDefault();
                return false;
            }
        }
    
        const Email = this.Email.value;
        let expresionRegularEmail = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g;
    
        if (expresionRegularEmail.test(Email)==false) {
            alert("Por favor ingresa un correo válido.");
            e.preventDefault();
        }
    
        const Password = this.Password.value;
        let expresionRegularPassword = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/gm;
    
        if (expresionRegularPassword.test(Password)==false) {
            alert("La contraseña debe tener al entre 8 y 10 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula. Puede tener otros símbolos");
            e.preventDefault();
        }
    });
});
    