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

// Consultar zonas activas y en mantenimiento
$sql = "SELECT * FROM tbl_zonas WHERE zona_estado IN ('activo', 'mantenimiento') ORDER BY zona_nombre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$zonas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Reservas</title>
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
                <!-- Columna Izquierda - Contenedor Blanco RESERVAR -->
                <div class="col-md-8">
                    <div class="contenedor-principal">
                        <div class="titulo-seccion">
                            <h3 class="mb-0">RESERVAR</h3>
                        </div>
                        
                        <!-- Botones de navegación alineados a la derecha -->
                        <div class="botones-navegacion">
                            <a href="../views/zonas.php" class="btn btn-custom">ZONAS COMUNES</a>
                            <a href="../views/misReservas.php" class="btn btn-custom">MIS RESERVAS</a>
                            <a href="../views/todasReservas.php" class="btn btn-custom">TODAS LAS RESERVAS</a>
                        </div>

                        <!-- Mensajes de respuesta -->
                        <?php if (isset($_SESSION['response'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['response_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['response']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['response'], $_SESSION['response_type']); ?>
                        <?php endif; ?>
                        
                        <!-- Formulario de Nueva Reserva -->
                        <div class="formulario-reserva">
                            <h5 class="mb-4 formulario-titulo">Nueva Reserva</h5>
                            
                            <form action="../controller/reservasController.php?action=crearReserva" method="POST" id="formReserva">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Documento</label>
                                        <select class="form-select" name="tipo_documento" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="Cedula de ciudadania">Cédula de Ciudadanía</option>
                                            <option value="Cedula de extranjeria">Cédula de Extranjería</option>
                                            <option value="Pasaporte">Pasaporte</option>
                                            <option value="Permiso especial de permanencia (PEP)">PEP</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="numero_documento" 
                                               placeholder="Ingrese el número de documento" required
                                               id="input-documento">
                                        <div class="form-text" id="info-usuario"></div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Zona Común <span class="text-danger">*</span></label>
                                        <select class="form-select" name="zona_id" required>
                                            <option value="">Seleccionar zona...</option>
                                            <?php if (!empty($zonas)): ?>
                                                <?php foreach ($zonas as $zona): ?>
                                                    <?php if ($zona['zona_estado'] === 'activo'): ?>
                                                        <option value="<?php echo $zona['zona_id']; ?>" 
                                                                data-apertura="<?php echo $zona['zona_hora_apertura']; ?>"
                                                                data-cierre="<?php echo $zona['zona_hora_cierre']; ?>"
                                                                data-duracion="<?php echo $zona['zona_duracion_maxima']; ?>">
                                                            <?php echo htmlspecialchars($zona['zona_nombre']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="fecha_reserva" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="hora_inicio" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="hora_fin" required>
                                    </div>
                                </div>

                                <!-- Campos ocultos que se llenarán automáticamente -->
                                <input type="hidden" name="apartamento" id="apartamento">
                                <input type="hidden" name="nombre_residente" id="nombre_residente">
                                
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-reservar" id="btn-confirmar">
                                        CONFIRMAR RESERVA
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="card shadow-sm mt-4">
                            <div class="card-header calendario-header">
                                <h5 class="mb-0">Calendario de Reservas</h5>
                            </div>
                            <div class="card-body">
                                <!-- Calendario integrado -->
                                <div class="calendario-content">
                                    <div class="zona-selector-calendario mb-3">
                                        <label for="zona-select-calendario" class="form-label">Seleccionar zona para ver reservas:</label>
                                        <select id="zona-select-calendario" class="form-select">
                                            <option value="">Seleccionar zona...</option>
                                            <?php if (!empty($zonas)): ?>
                                                <?php foreach ($zonas as $zona): ?>
                                                    <option value="<?php echo $zona['zona_id']; ?>">
                                                        <?php echo htmlspecialchars($zona['zona_nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="calendario-navegacion d-flex justify-content-between align-items-center mb-3">
                                        <button class="btn btn-outline-secondary btn-sm" id="mes-anterior">
                                            <i class="ri-arrow-left-line"></i> Anterior
                                        </button>
                                        <h6 class="mes-actual-titulo mb-0" id="mes-actual-titulo"></h6>
                                        <button class="btn btn-outline-secondary btn-sm" id="mes-siguiente">
                                            Siguiente <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>

                                    <div class="calendario-grid-container">
                                        <div class="calendario-grid" id="calendario-grid">
                                            <!-- El calendario se genera dinámicamente -->
                                        </div>
                                    </div>

                                    <div class="calendario-leyenda mt-3">
                                        <div class="d-flex gap-3 justify-content-center">
                                            <div class="leyenda-item d-flex align-items-center">
                                                <div class="leyenda-color leyenda-hoy"></div>
                                                <span class="ms-2 small">Hoy</span>
                                            </div>
                                            <div class="leyenda-item d-flex align-items-center">
                                                <div class="leyenda-color leyenda-reserva"></div>
                                                <span class="ms-2 small">Reservas</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Contenedor Blanco ZONAS DISPONIBLES -->
                <div class="col-md-4">
                    <div class="contenedor-principal">
                        <div class="titulo-seccion-zonas">
                            <h3 class="mb-0">ZONAS DISPONIBLES</h3>
                        </div>
                        
                        <?php if (empty($zonas)): ?>
                            <div class="alert alert-info text-center">
                                <i class="ri-information-line" style="font-size: 2rem; color: #17a2b8;"></i>
                                <h5 class="mt-2">No hay zonas disponibles</h5>
                                <p class="mb-0">Actualmente no existen zonas comunes configuradas.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($zonas as $zona): ?>
                                <div class="card mb-3 zona-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="zona-imagen-placeholder me-3">
                                                <?php if (!empty($zona['zona_imagen'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($zona['zona_imagen']); ?>" 
                                                        alt="<?php echo htmlspecialchars($zona['zona_nombre']); ?>" 
                                                        class="zona-imagen-real">
                                                <?php else: ?>
                                                    <i class="ri-community-line"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title zona-titulo">
                                                    <?php echo strtoupper(htmlspecialchars($zona['zona_nombre'])); ?>
                                                </h6>
                                                <p class="zona-descripcion"><?php echo htmlspecialchars($zona['zona_descripcion']); ?></p>
                                                
                                                <!-- Badge dinámico según estado -->
                                                <?php if ($zona['zona_estado'] === 'activo'): ?>
                                                    <span class="badge badge-disponible">DISPONIBLE</span>
                                                <?php else: ?>
                                                    <span class="badge badge-mantenimiento">MANTENIMIENTO</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <hr class="zona-divider">
                                        
                                        <!-- Datos dinámicos -->
                                        <div class="zona-detalles">
                                            <div class="detalle-item">
                                                <span class="detalle-label">Capacidad</span>
                                                <span class="detalle-valor"><?php echo $zona['zona_capacidad']; ?> personas</span>
                                            </div>
                                            <div class="detalle-item">
                                                <span class="detalle-label">Horario</span>
                                                <span class="detalle-valor"><?php echo substr($zona['zona_hora_apertura'], 0, 5) . ' - ' . substr($zona['zona_hora_cierre'], 0, 5); ?></span>
                                            </div>
                                            <div class="detalle-item">
                                                <span class="detalle-label">Duración máx.</span>
                                                <span class="detalle-valor"><?php echo $zona['zona_duracion_maxima']; ?> h</span>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mt-3">
                                            <button class="btn btn-terminos" onclick="mostrarTerminos(<?php echo $zona['zona_id']; ?>, '<?php echo addslashes($zona['zona_nombre']); ?>', `<?php echo addslashes(nl2br(htmlspecialchars($zona['zona_terminos_condiciones'] ?? 'No hay términos específicos para esta zona.'))); ?>`)">
                                                VER TÉRMINOS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para términos y condiciones -->
    <div class="modal fade" id="modalTerminos" tabindex="-1" aria-labelledby="modalTerminosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-terminos-header">
                    <h5 class="modal-title" id="modalTerminosLabel">Términos y Condiciones</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalTerminosContent">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./Layout/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variables globales del calendario
        let fechaActual = new Date();
        let usuarioEncontrado = false;
        const meses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        const diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

        // Función para cargar reservas desde el controlador
        async function cargarReservasZona(zonaId) {
            if (!zonaId) return [];
            
            try {
                const response = await fetch(`../controller/reservasController.php?action=getReservasPorZona&zona_id=${zonaId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    return result.data || [];
                } else {
                    console.error('Error del servidor:', result.error);
                    return [];
                }
                
            } catch (error) {
                console.error('Error cargando reservas:', error);
                return [];
            }
        }

        // Función actualizada para generar calendario
        async function generarCalendario() {
            const año = fechaActual.getFullYear();
            const mes = fechaActual.getMonth();
            
            // Actualizar título
            document.getElementById('mes-actual-titulo').textContent = `${meses[mes]} ${año}`;
            
            const primerDia = new Date(año, mes, 1);
            const ultimoDia = new Date(año, mes + 1, 0);
            const diasEnMes = ultimoDia.getDate();
            
            // Ajustar para que lunes sea el primer día
            let primerDiaSemana = primerDia.getDay();
            primerDiaSemana = primerDiaSemana === 0 ? 6 : primerDiaSemana - 1;
            
            const grid = document.getElementById('calendario-grid');
            
            // Mostrar indicador de carga
            grid.innerHTML = '<div style="grid-column: span 7; text-align: center; padding: 20px; color: #6c757d;">Cargando reservas...</div>';
            
            // Cargar reservas de la zona seleccionada
            const zonaSeleccionada = document.getElementById('zona-select-calendario').value;
            const reservasZona = await cargarReservasZona(zonaSeleccionada);
            
            // Limpiar grid y regenerar
            grid.innerHTML = '';
            
            // Headers de días
            diasSemana.forEach(dia => {
                const header = document.createElement('div');
                header.className = 'calendario-dia-header';
                header.textContent = dia;
                grid.appendChild(header);
            });
            
            // Días del mes anterior
            const mesAnterior = new Date(año, mes - 1, 0).getDate();
            for (let i = primerDiaSemana - 1; i >= 0; i--) {
                const dia = document.createElement('div');
                dia.className = 'calendario-dia calendario-otro-mes';
                dia.innerHTML = `<div class="calendario-numero">${mesAnterior - i}</div>`;
                grid.appendChild(dia);
            }
            
            // Días del mes actual
            const hoy = new Date();
            for (let dia = 1; dia <= diasEnMes; dia++) {
                const diaElement = document.createElement('div');
                diaElement.className = 'calendario-dia';
                
                // Marcar día actual
                if (año === hoy.getFullYear() && mes === hoy.getMonth() && dia === hoy.getDate()) {
                    diaElement.classList.add('calendario-hoy');
                }
                
                const fechaDia = `${año}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                
                let contenido = `<div class="calendario-numero">${dia}</div>`;
                
                // Agregar reservas reales de la base de datos
                if (reservasZona.length > 0) {
                    const reservasDia = reservasZona.filter(r => r.fecha === fechaDia);
                    reservasDia.forEach(reserva => {
                        const tooltipText = `${reserva.residente} (${reserva.apartamento}) - ${reserva.hora_inicio} a ${reserva.hora_fin}`;
                        contenido += `<div class="calendario-reserva" title="${tooltipText}">${reserva.hora_inicio}</div>`;
                    });
                }
                
                diaElement.innerHTML = contenido;
                grid.appendChild(diaElement);
            }
            
            // Días del siguiente mes
            const diasRestantes = 42 - (primerDiaSemana + diasEnMes);
            for (let dia = 1; dia <= diasRestantes; dia++) {
                const diaElement = document.createElement('div');
                diaElement.className = 'calendario-dia calendario-otro-mes';
                diaElement.innerHTML = `<div class="calendario-numero">${dia}</div>`;
                grid.appendChild(diaElement);
            }
        }

        // Búsqueda automática de usuario por cédula (MEJORADA)
        document.querySelector('input[name="numero_documento"]').addEventListener('input', async function() {
            const cedula = this.value.trim();
            const infoDiv = document.getElementById('info-usuario');
            const btnConfirmar = document.getElementById('btn-confirmar');
            
            if (cedula.length >= 7) { // Validar que tenga al menos 7 dígitos
                try {
                    // Mostrar indicador de carga
                    infoDiv.innerHTML = '<small class="text-info">Buscando usuario...</small>';
                    
                    const response = await fetch(`../controller/reservasController.php?action=buscarUsuario&cedula=${cedula}`);
                    
                    if (response.ok) {
                        const result = await response.json();
                        
                        if (result.success && result.data) {
                            // Llenar automáticamente los campos ocultos
                            document.getElementById('apartamento').value = result.data.apartamento;
                            document.getElementById('nombre_residente').value = result.data.nombre;
                            
                            // Mostrar información al usuario
                            this.style.borderColor = '#28a745';
                            infoDiv.innerHTML = `<small class="text-success">✓ Usuario encontrado: <strong>${result.data.nombre}</strong> - ${result.data.apartamento}</small>`;
                            usuarioEncontrado = true;
                            btnConfirmar.disabled = false;
                        } else {
                            // Usuario no encontrado
                            document.getElementById('apartamento').value = '';
                            document.getElementById('nombre_residente').value = '';
                            this.style.borderColor = '#dc3545';
                            infoDiv.innerHTML = '<small class="text-danger">✗ Usuario no encontrado en el sistema</small>';
                            usuarioEncontrado = false;
                            btnConfirmar.disabled = true;
                        }
                    }
                } catch (error) {
                    console.error('Error buscando usuario:', error);
                    this.style.borderColor = '#ffc107';
                    infoDiv.innerHTML = '<small class="text-warning">⚠ Error al buscar usuario</small>';
                    usuarioEncontrado = false;
                    btnConfirmar.disabled = true;
                }
            } else {
                // Limpiar campos si no hay suficientes dígitos
                document.getElementById('apartamento').value = '';
                document.getElementById('nombre_residente').value = '';
                this.style.borderColor = '';
                infoDiv.innerHTML = '';
                usuarioEncontrado = false;
                btnConfirmar.disabled = false;
            }
        });

        // Validación del formulario antes de enviar
        document.getElementById('formReserva').addEventListener('submit', function(e) {
            const cedula = document.querySelector('input[name="numero_documento"]').value.trim();
            
            if (cedula.length >= 7 && !usuarioEncontrado) {
                e.preventDefault();
                alert('Debe esperar a que se verifique el usuario antes de enviar el formulario.');
                return false;
            }
        });

        // Event listeners del calendario
        document.getElementById('mes-anterior').addEventListener('click', function() {
            fechaActual.setMonth(fechaActual.getMonth() - 1);
            generarCalendario();
        });

        document.getElementById('mes-siguiente').addEventListener('click', function() {
            fechaActual.setMonth(fechaActual.getMonth() + 1);
            generarCalendario();
        });

        document.getElementById('zona-select-calendario').addEventListener('change', function() {
            generarCalendario();
        });

        // Función para mostrar términos dinámicos
        function mostrarTerminos(zonaId, nombreZona, terminos) {
            document.getElementById('modalTerminosLabel').textContent = 'Términos y Condiciones - ' + nombreZona;
            document.getElementById('modalTerminosContent').innerHTML = terminos || '<p>No hay términos disponibles para esta zona.</p>';
            
            const modal = new bootstrap.Modal(document.getElementById('modalTerminos'));
            modal.show();
        }

        // Validación de horarios según zona seleccionada
        document.querySelector('select[name="zona_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const apertura = selectedOption.dataset.apertura;
                const cierre = selectedOption.dataset.cierre;
                
                const horaInicio = document.querySelector('input[name="hora_inicio"]');
                const horaFin = document.querySelector('input[name="hora_fin"]');
                
                horaInicio.min = apertura;
                horaInicio.max = cierre;
                horaFin.min = apertura;
                horaFin.max = cierre;
            }
        });

        // Inicializar calendario al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            generarCalendario();
        });
    </script>
</body>
</html>