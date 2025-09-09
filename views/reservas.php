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
                        
                        <!-- Formulario de Nueva Reserva -->
                        <div class="formulario-reserva">
                            <h5 class="mb-4 formulario-titulo">Nueva Reserva</h5>
                            
                            <form action="/index.php?controller=Reservas&action=crearReserva" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Documento</label>
                                        <select class="form-select" name="tipo_documento" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="CC">Cédula de Ciudadanía</option>
                                            <option value="CE">Cédula de Extranjería</option>
                                            <option value="PP">Pasaporte</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Documento</label>
                                        <input type="text" class="form-control" name="numero_documento" placeholder="Ingrese el número" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Zona Común</label>
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
                                        <label class="form-label">Fecha</label>
                                        <input type="date" class="form-control" name="fecha_reserva" min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de Inicio</label>
                                        <input type="time" class="form-control" name="hora_inicio" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora de Fin</label>
                                        <input type="time" class="form-control" name="hora_fin" required>
                                    </div>
                                </div>

                                <!-- Campos ocultos que se llenarán automáticamente -->
                                <input type="hidden" name="apartamento" id="apartamento">
                                <input type="hidden" name="nombre_residente" id="nombre_residente">
                                
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-reservar">
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
                                <div class="demo-content">
                                    <div class="text-center">
                                        <i class="ri-calendar-2-line calendario-icono"></i>
                                        <p class="mt-2">Aquí irá el calendario interactivo...</p>
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
                                                    <img src="<?php echo htmlspecialchars($zona['zona_imagen']); ?>" 
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
        // Función para mostrar términos dinámicos
        function mostrarTerminos(zonaId, nombreZona, terminos) {
            document.getElementById('modalTerminosLabel').textContent = 'Términos y Condiciones - ' + nombreZona;
            document.getElementById('modalTerminosContent').innerHTML = terminos || '<p>No hay términos disponibles para esta zona.</p>';
            
            const modal = new bootstrap.Modal(document.getElementById('modalTerminos'));
            modal.show();
        }
                
        // Búsqueda de usuario por cédula (simulada)
        document.querySelector('input[name="numero_documento"]').addEventListener('input', function() {
            const cedula = this.value.trim();
            if (cedula.length >= 6) {
                // Aquí iría la búsqueda real del usuario
                // Por ahora simulamos
                document.getElementById('apartamento').value = 'Apt 101';
                document.getElementById('nombre_residente').value = 'Usuario Test';
            }
        });

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
    </script>
</body>
</html>