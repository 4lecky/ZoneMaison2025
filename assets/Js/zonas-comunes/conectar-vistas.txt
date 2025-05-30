/**
 * Zone Maisons - Sistema de Reserva de Áreas Comunes
 * 
 * Este archivo contiene las funciones necesarias para conectar las tres vistas:
 * - reserva1.php (vista principal con tarjetas)
 * - zona-comun1.php (detalle de área común)
 * - crear-zona-comun.php (formulario de creación)
 */

// Ejecutar código cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Detectar en qué página estamos
    const currentPage = window.location.pathname.split('/').pop();
    
    // Inicializar la vista correspondiente
    if (currentPage === 'reserva1.php') {
        initReservaPage();
    } else if (currentPage === 'crear-zona-comun.php') {
        initCrearZonaPage();
    } else if (currentPage === 'zona-comun1.php') {
        initZonaComunPage();
    }
});

/**
 * Inicializar la página principal con las tarjetas de áreas comunes
 */
function initReservaPage() {
    console.log('Inicializando página de reservas...');
    
    // Cargar las áreas comunes desde localStorage
    cargarAreasComunes();
    
    // Configurar el botón para ir a crear nueva área (si existe)
    const btnCrearArea = document.querySelector('.btn-crear-area');
    if (btnCrearArea) {
        btnCrearArea.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'crear-zona-comun.php';
        });
    }
}

/**
 * Cargar las áreas comunes desde el localStorage y mostrarlas en tarjetas
 */
function cargarAreasComunes() {
    // Obtener el contenedor donde se mostrarán las tarjetas
    const contenedorAreas = document.querySelector('.areas-container');
    if (!contenedorAreas) return;
    
    // Obtener áreas guardadas en localStorage (o un array vacío si no hay)
    const areasComunes = JSON.parse(localStorage.getItem('zoneMaisonsAreas') || '[]');
    
    // Si no hay áreas guardadas y es la primera vez, podemos cargar algunas por defecto
    if (areasComunes.length === 0) {
        // No hacemos nada aquí ya que asumimos que ya tienes tarjetas en el HTML
        console.log('No hay áreas comunes guardadas en localStorage');
    } else {
        // Mostrar las áreas guardadas (las creadas por el administrador)
        areasComunes.forEach(area => {
            // Crear una nueva tarjeta para esta área
            const nuevaTarjeta = crearTarjetaArea(area);
            
            // Añadir al contenedor
            contenedorAreas.appendChild(nuevaTarjeta);
        });
    }
    
    // Añadir evento de clic a todas las tarjetas (tanto las estáticas como las dinámicas)
    const todasLasTarjetas = document.querySelectorAll('.area-card');
    todasLasTarjetas.forEach(tarjeta => {
        tarjeta.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener el ID del área (del atributo data-id o del ID del elemento)
            const areaId = this.dataset.id || this.id;
            
            // Guardar el ID seleccionado en localStorage para recuperarlo en la otra página
            localStorage.setItem('areaSeleccionada', areaId);
            
            // Redirigir a la página de detalle
            window.location.href = 'zona-comun1.php';
        });
    });
}

/**
 * Crear un elemento HTML para una tarjeta de área común
 * @param {Object} area - Los datos del área común
 * @return {HTMLElement} - El elemento de la tarjeta
 */
function crearTarjetaArea(area) {
    // Crear contenedor principal de la tarjeta
    const tarjeta = document.createElement('div');
    tarjeta.className = 'area-card';
    tarjeta.dataset.id = area.id;
    
    // Crear la imagen (si existe)
    if (area.imagen) {
        const img = document.createElement('img');
        img.src = area.imagen;
        img.alt = area.nombre;
        tarjeta.appendChild(img);
    }
    
    // Crear el contenido de texto
    const contenido = document.createElement('div');
    contenido.className = 'area-content';
    
    // Título
    const titulo = document.createElement('h3');
    titulo.textContent = area.nombre;
    contenido.appendChild(titulo);
    
    // Añadir el contenido a la tarjeta
    tarjeta.appendChild(contenido);
    
    return tarjeta;
}

/**
 * Inicializar la página de creación de áreas comunes
 */
function initCrearZonaPage() {
    console.log('Inicializando página de creación de zona común...');
    
    // Obtener el formulario
    const formulario = document.getElementById('form-crear-zona');
    if (!formulario) return;
    
    // Añadir evento para manejar el envío del formulario
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Crear un objeto con los datos del formulario
        const nuevaArea = {
            id: 'area_' + Date.now(), // ID único basado en timestamp
            nombre: document.getElementById('nombre').value,
            descripcion: document.getElementById('descripcion').value,
            capacidad: document.getElementById('capacidad').value,
            categoria: document.getElementById('categoria').value,
            // Para la imagen, idealmente necesitaríamos un servicio de almacenamiento
            // Por ahora usamos una URL estática o un placeholder
            imagen: '/images/default-area.jpg'
        };
        
        // Guardar en localStorage
        guardarNuevaArea(nuevaArea);
        
        // Mostrar mensaje de éxito (puedes personalizar esto)
        alert('Área común creada con éxito');
        
        // Redirigir a la página principal
        window.location.href = 'reserva1.php';
    });
    
    // Si quieres manejar la carga de imágenes, necesitarías código adicional aquí
}

/**
 * Guardar una nueva área común en localStorage
 * @param {Object} area - El objeto con los datos del área
 */
function guardarNuevaArea(area) {
    // Obtener áreas existentes
    const areasExistentes = JSON.parse(localStorage.getItem('zoneMaisonsAreas') || '[]');
    
    // Añadir la nueva área
    areasExistentes.push(area);
    
    // Guardar de vuelta en localStorage
    localStorage.setItem('zoneMaisonsAreas', JSON.stringify(areasExistentes));
}

/**
 * Inicializar la página de detalle de área común
 */
function initZonaComunPage() {
    console.log('Inicializando página de detalle de zona común...');
    
    // Obtener el ID del área seleccionada
    const areaId = localStorage.getItem('areaSeleccionada');
    
    if (!areaId) {
        console.error('No se encontró un ID de área seleccionada');
        return;
    }
    
    // Buscar los datos del área
    const areaSeleccionada = buscarAreaPorId(areaId);
    
    // Si encontramos los datos, mostrarlos en la página
    if (areaSeleccionada) {
        mostrarDetalleArea(areaSeleccionada);
    } else {
        console.log('No se encontró información para el área: ' + areaId);
        // Si el área no es dinámica, asumimos que ya está en el HTML estático
    }
    
    // Configurar el formulario de reserva
    configurarFormularioReserva(areaId);
}

/**
 * Buscar un área por su ID
 * @param {string} id - El ID del área a buscar
 * @return {Object|null} - El objeto del área o null si no se encuentra
 */
function buscarAreaPorId(id) {
    // Obtener todas las áreas de localStorage
    const todasLasAreas = JSON.parse(localStorage.getItem('zoneMaisonsAreas') || '[]');
    
    // Buscar el área con el ID especificado
    return todasLasAreas.find(area => area.id === id) || null;
}

/**
 * Mostrar los detalles de un área en la página
 * @param {Object} area - El objeto con los datos del área
 */
function mostrarDetalleArea(area) {
    // Estas son las posibles IDs de los elementos en tu HTML
    // Ajusta según la estructura real de tu página
    
    // Actualizar el título
    const titulo = document.getElementById('area-title');
    if (titulo) titulo.textContent = area.nombre;
    
    // Actualizar la descripción
    const descripcion = document.getElementById('area-description');
    if (descripcion) descripcion.textContent = area.descripcion;
    
    // Actualizar la capacidad
    const capacidad = document.getElementById('area-capacity');
    if (capacidad) capacidad.textContent = Capacidad máxima: ${area.capacidad} personas;
    
    // Actualizar la imagen si existe
    const imagen = document.querySelector('.area-image img');
    if (imagen && area.imagen) imagen.src = area.imagen;
}

/**
 * Configurar el formulario de reserva
 * @param {string} areaId - El ID del área que se está reservando
 */
function configurarFormularioReserva(areaId) {
    const formularioReserva = document.getElementById('form-reserva');
    if (!formularioReserva) return;
    
    // Añadir un campo oculto con el ID del área
    const inputAreaId = document.createElement('input');
    inputAreaId.type = 'hidden';
    inputAreaId.name = 'area_id';
    inputAreaId.value = areaId;
    formularioReserva.appendChild(inputAreaId);
    
    // Añadir evento al formulario
    formularioReserva.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Crear objeto con datos de la reserva
        const nuevaReserva = {
            areaId: areaId,
            fecha: document.getElementById('fecha').value,
            horario: document.getElementById('horario').value,
            nombre: document.getElementById('nombre').value,
            apartamento: document.getElementById('apartamento').value,
            timestamp: Date.now()
        };
        
        // Guardar la reserva
        guardarReserva(nuevaReserva);
        
        // Mostrar confirmación
        alert('¡Reserva realizada con éxito!');
        
        // Redirigir a página principal o mostrar página de confirmación
        window.location.href = 'reserva1.php';
    });
}

/**
 * Guardar una reserva en localStorage
 * @param {Object} reserva - El objeto con los datos de la reserva
 */
function guardarReserva(reserva) {
    // Obtener reservas existentes
    const reservasExistentes = JSON.parse(localStorage.getItem('zoneMaisonsReservas') || '[]');
    
    // Añadir la nueva reserva
    reservasExistentes.push(reserva);
    
    // Guardar de vuelta en localStorage
    localStorage.setItem('zoneMaisonsReservas', JSON.stringify(reservasExistentes));
}