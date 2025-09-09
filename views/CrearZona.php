<?php
session_start();

// TEMPORAL - Solo para desarrollo (ELIMINAR después)
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = [
        'id' => 1,
        'cedula' => '12345678',
        'nombre' => 'Admin Test',
        'email' => 'admin@zonemaisons.com',
        'rol' => 'Administrador'
    ];
}

require_once "../config/db.php";
require_once "./Layout/header.php";

// Verificar que sea administrador
$usuario = $_SESSION['usuario'];
if ($usuario['rol'] !== 'Administrador') {
    header('Location: ../views/reservas.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Crear Zona</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/reservas.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <main>
        <div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="contenedor-principal">
                        <!-- Título -->
                        <div class="titulo-seccion">
                            <h3 class="mb-0">CREAR ZONA</h3>
                        </div>
                        
                        <!-- Botones de navegación alineados a la derecha -->
                        <div class="botones-navegacion">
                            <a href="../views/reservas.php" class="btn btn-custom">RESERVAR</a>
                            <a href="../views/zonas.php" class="btn btn-custom">ZONAS COMUNES</a>
                        </div>

                        <!-- Mensajes de respuesta -->
                        <?php if (isset($_SESSION['response'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['response_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['response']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['response'], $_SESSION['response_type']); ?>
                        <?php endif; ?>

                        <!-- Formulario de Crear Zona -->
                        <div class="formulario-reserva">
                            <h5 class="mb-4 formulario-titulo">Nueva Zona Común</h5>
                            
                            <form action="../controller/reservasController.php?action=crearZona" method="POST" id="formCrearZona" enctype="multipart/form-data">
                                <!-- Información Básica -->
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Nombre de la Zona <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="zona_nombre" placeholder="Ej: Salón Social, Piscina, Gimnasio..." required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" name="zona_estado" required>
                                            <option value="activo" selected>Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                            <option value="mantenimiento">Mantenimiento</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="zona_descripcion" rows="3" 
                                              placeholder="Describe brevemente la zona común, sus características y uso..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Imagen de la Zona</label>
                                    <input type="file" class="form-control" name="zona_imagen" 
                                           accept="image/*">
                                    <div class="form-text">Opcional: Seleccione una imagen que represente la zona común (JPG, PNG, etc.).</div>
                                    <div id="imagen-preview" class="mt-2"></div>
                                </div>

                                <!-- Configuración de Uso -->
                                <hr class="my-4">
                                <h6 class="mb-3 text-primary">Configuración de Uso</h6>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Capacidad Máxima <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="zona_capacidad" 
                                               min="1" max="500" placeholder="Ej: 50" required>
                                        <div class="form-text">Número máximo de personas permitidas.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hora de Apertura <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="zona_hora_apertura" 
                                               value="08:00" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hora de Cierre <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="zona_hora_cierre" 
                                               value="20:00" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duración Máxima por Reserva <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="zona_duracion_maxima" 
                                                   min="1" max="24" value="2" required>
                                            <span class="input-group-text">horas</span>
                                        </div>
                                        <div class="form-text">Tiempo máximo que puede durar una reserva.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Horario Calculado</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded" id="horario-calculado">
                                            08:00 - 20:00 (12 horas disponibles)
                                        </div>
                                        <div class="form-text">Horario total de disponibilidad de la zona.</div>
                                    </div>
                                </div>

                                <!-- Términos y Condiciones -->
                                <hr class="my-4">
                                <h6 class="mb-3 text-primary">Términos y Condiciones</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Reglas y Condiciones de Uso</label>
                                    <textarea class="form-control" name="zona_terminos_condiciones" 
                                              rows="6" placeholder="Ingrese las reglas y condiciones específicas para el uso de esta zona común..."></textarea>
                                    <div class="form-text">
                                        Opcional: Especifique reglas de uso, horarios especiales, restricciones, etc.
                                    </div>
                                </div>

                                <!-- Plantillas de Términos -->
                                <div class="mb-3">
                                    <label class="form-label">Plantillas de Términos:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('salon')">
                                            Salón Social
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('piscina')">
                                            Piscina
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('gimnasio')">
                                            Gimnasio
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('cancha')">
                                            Cancha Deportiva
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('general')">
                                            General
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                                            <i class="ri-refresh-line me-1"></i>Limpiar Formulario
                                        </button>
                                    </div>
                                    <div>
                                        <a href="../views/zonas.php" class="btn btn-secondary me-2">
                                            <i class="ri-arrow-left-line me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-reservar">
                                            <i class="ri-save-line me-1"></i>CREAR ZONA
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "./Layout/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            calcularHorario();
            
            // Event listeners para horarios
            document.querySelector('input[name="zona_hora_apertura"]').addEventListener('change', calcularHorario);
            document.querySelector('input[name="zona_hora_cierre"]').addEventListener('change', calcularHorario);
            
            // Event listener para vista previa de imagen
            document.querySelector('input[name="zona_imagen"]').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagen-preview');
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Vista previa" 
                                 style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; object-fit: cover;">
                            <div class="form-text mt-1">Vista previa de la imagen seleccionada</div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
            
            // Validación del formulario
            document.getElementById('formCrearZona').addEventListener('submit', function(e) {
                if (!validarFormulario()) {
                    e.preventDefault();
                }
            });
        });

        function calcularHorario() {
            const horaApertura = document.querySelector('input[name="zona_hora_apertura"]').value;
            const horaCierre = document.querySelector('input[name="zona_hora_cierre"]').value;
            const horarioCalculado = document.getElementById('horario-calculado');

            if (horaApertura && horaCierre) {
                const inicio = new Date(`2000-01-01 ${horaApertura}`);
                const fin = new Date(`2000-01-01 ${horaCierre}`);
                
                if (fin > inicio) {
                    const horas = (fin - inicio) / (1000 * 60 * 60);
                    horarioCalculado.textContent = `${horaApertura} - ${horaCierre} (${horas} horas disponibles)`;
                    horarioCalculado.className = 'form-control-plaintext bg-light p-2 rounded';
                } else {
                    horarioCalculado.textContent = 'Horario inválido';
                    horarioCalculado.className = 'form-control-plaintext bg-danger text-white p-2 rounded';
                }
            }
        }

        function validarFormulario() {
            const horaApertura = document.querySelector('input[name="zona_hora_apertura"]').value;
            const horaCierre = document.querySelector('input[name="zona_hora_cierre"]').value;

            if (horaApertura && horaCierre && horaApertura >= horaCierre) {
                alert('La hora de apertura debe ser anterior a la hora de cierre');
                return false;
            }

            return true;
        }

        function aplicarPlantilla(tipo) {
            const textarea = document.querySelector('textarea[name="zona_terminos_condiciones"]');
            let terminos = '';

            switch(tipo) {
                case 'salon':
                    terminos = `TÉRMINOS Y CONDICIONES - SALÓN SOCIAL

1. RESERVAS:
• Las reservas deben realizarse con mínimo 24 horas de anticipación
• Máximo 8 horas de uso continuo por reserva
• Solo se permite una reserva por apartamento por mes

2. USO DEL ESPACIO:
• Capacidad máxima estrictamente respetada
• Prohibido el consumo de bebidas alcohólicas sin autorización
• Prohibido fumar en el interior del salón
• El ruido debe mantenerse en niveles apropiados

3. RESPONSABILIDADES:
• El reservante es responsable por cualquier daño ocasionado
• Debe entregar el espacio en las mismas condiciones que lo recibió
• Obligatorio realizar limpieza posterior al evento`;
                    break;

                case 'piscina':
                    terminos = `TÉRMINOS Y CONDICIONES - PISCINA

1. HORARIOS Y ACCESO:
• Uso exclusivo durante horarios establecidos
• Prohibido el acceso fuera del horario permitido
• Niños menores de 12 años deben estar acompañados por un adulto

2. NORMAS DE SEGURIDAD:
• Prohibido ingresar en estado de embriaguez
• No se permite correr alrededor de la piscina
• Uso obligatorio de traje de baño apropiado
• Ducha obligatoria antes de ingresar

3. HIGIENE Y SALUD:
• Prohibido el ingreso con heridas abiertas o enfermedades contagiosas
• No se permite el uso de jabones o champús en la piscina
• Prohibido escupir o hacer necesidades fisiológicas en el agua`;
                    break;

                case 'gimnasio':
                    terminos = `TÉRMINOS Y CONDICIONES - GIMNASIO

1. USO DEL EQUIPO:
• Limpiar el equipo después de cada uso
• Reportar inmediatamente cualquier daño o mal funcionamiento
• Prohibido modificar o ajustar los equipos sin autorización
• Uso de toalla obligatorio

2. VESTIMENTA APROPIADA:
• Ropa deportiva limpia y apropiada
• Calzado deportivo cerrado obligatorio
• Prohibido el uso de sandalias o pies descalzos

3. COMPORTAMIENTO:
• Mantener un ambiente de respeto y cordialidad
• Prohibido gritar o hacer ruido excesivo
• No se permite reservar equipos por períodos prolongados`;
                    break;

                case 'cancha':
                    terminos = `TÉRMINOS Y CONDICIONES - CANCHA DEPORTIVA

1. RESERVAS Y HORARIOS:
• Reserva mínima de 1 hora, máxima de 3 horas continuas
• Puntualidad obligatoria para inicio y finalización
• En caso de no presentarse, se pierde la reserva

2. USO DEPORTIVO:
• Solo actividades deportivas permitidas
• Calzado deportivo apropiado obligatorio
• Prohibido el uso de elementos que dañen la superficie
• Respetar las líneas y marcaciones de la cancha

3. CUIDADO DE LA INSTALACIÓN:
• Prohibido consumir alimentos o bebidas en la cancha
• No arrojar basura en el área de juego
• Reportar inmediatamente cualquier daño`;
                    break;

                case 'general':
                    terminos = `TÉRMINOS Y CONDICIONES GENERALES

1. RESERVAS:
• Las reservas deben realizarse a través del sistema oficial
• Confirmación requerida 24 horas antes del uso
• Cancelaciones deben notificarse con anticipación

2. USO RESPONSABLE:
• Respetar la capacidad máxima establecida
• Mantener el orden y la limpieza
• Prohibido el uso comercial sin autorización
• Cumplir estrictamente con los horarios

3. RESPONSABILIDADES:
• El usuario es responsable por su seguridad y la de sus invitados
• Obligación de reportar daños o incidentes inmediatamente
• Asumir costos por reparaciones necesarias debido a mal uso`;
                    break;
            }

            textarea.value = terminos;
        }

        function limpiarFormulario() {
            if (confirm('¿Está seguro de que desea limpiar todos los campos del formulario?')) {
                document.getElementById('formCrearZona').reset();
                document.getElementById('imagen-preview').innerHTML = '';
                calcularHorario();
            }
        }
    </script>
</body>
</html>