document.addEventListener("DOMContentLoaded", () => {
    const parkingMap = document.getElementById("parkingMap");
    const totalSpaces = 90; // cantidad de parqueaderos
    const estados = ["available", "occupied", "maintenance"];

    for (let i = 1; i <= totalSpaces; i++) {
        const space = document.createElement("div");
        space.classList.add("parking-space", "available"); // por defecto disponible
        space.textContent = i;

        // Cambio de estado al hacer click
        space.addEventListener("click", () => {
            const currentState = estados.find(state => space.classList.contains(state));
            const nextIndex = (estados.indexOf(currentState) + 1) % estados.length;
            space.className = "parking-space " + estados[nextIndex];
        });

        parkingMap.appendChild(space);
    }
});
