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

// Verificar que sea administrador o vigilante
$usuario = $_SESSION['usuario'];
if ($usuario['rol'] !== 'Administrador' && $usuario['rol'] !== 'Vigilante') {
    header('Location: ../views/reservas.php');
    exit;
}

// Consultar TODAS las reservas de TODOS los usuarios
$sql = "SELECT r.*, z.zona_nombre, z.zona_imagen, u.usu_nombre_completo, u.usu_telefono, u.usu_cedula
        FROM tbl_reservas r 
        INNER JOIN tbl_zonas z ON r.zona_id = z.zona_id 
        LEFT JOIN tbl_usuario u ON r.usuario_id = u.usuario_cc
        ORDER BY r.reserva_fecha DESC, r.reserva_hora_inicio DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$reservas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Todas las Reservas</title>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.3.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Bootstrap y otros CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/crud/tbl_crud.css">
    <link rel="stylesheet" href="../assets/Css/reservas.css" />
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
                            <h3 class="mb-0">TODAS LAS RESERVAS</h3>
                        </div>
                        
                        <!-- Botón de nueva reserva alineado a la derecha -->
                        <div class="botones-navegacion">
                            <a href="../views/reservas.php" class="btn btn-custom">NUEVA RESERVA</a>
                        </div>

                        <?php if (empty($reservas)): ?>
                            <!-- Mensaje cuando no hay reservas -->
                            <div class="alert alert-info text-center">
                                <i class="ri-calendar-line" style="font-size: 3rem; color: #17a2b8;"></i>
                                <h4 class="mt-3">No hay reservas en el sistema</h4>
                                <p class="mb-3">Aún no se han realizado reservas de zonas comunes.</p>
                                <a href="../views/reservas.php" class="btn btn-custom">
                                    <i class="ri-add-line me-1"></i>Crear Primera Reserva
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Estadísticas rápidas -->
                            <div class="row mb-4">
                                <?php
                                $totalReservas = count($reservas);
                                $reservasActivas = array_filter($reservas, function($r) { return $r['reserva_estado'] === 'activa'; });
                                $reservasCompletadas = array_filter($reservas, function($r) { return $r['reserva_estado'] === 'completada'; });
                                $reservasCanceladas = array_filter($reservas, function($r) { return $r['reserva_estado'] === 'cancelada'; });
                                ?>
                                
                                <div class="col-md-3">
                                    <div class="card text-center border-primary">
                                        <div class="card-body">
                                            <i class="ri-calendar-line" style="font-size: 2rem; color: #0d6efd;"></i>
                                            <h4 class="mt-2"><?php echo $totalReservas; ?></h4>
                                            <p class="text-muted mb-0">Total Reservas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-success">
                                        <div class="card-body">
                                            <i class="ri-calendar-check-line" style="font-size: 2rem; color: #198754;"></i>
                                            <h4 class="mt-2"><?php echo count($reservasActivas); ?></h4>
                                            <p class="text-muted mb-0">Activas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-info">
                                        <div class="card-body">
                                            <i class="ri-calendar-todo-line" style="font-size: 2rem; color: #0dcaf0;"></i>
                                            <h4 class="mt-2"><?php echo count($reservasCompletadas); ?></h4>
                                            <p class="text-muted mb-0">Completadas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-warning">
                                        <div class="card-body">
                                            <i class="ri-calendar-close-line" style="font-size: 2rem; color: #ffc107;"></i>
                                            <h4 class="mt-2"><?php echo count($reservasCanceladas); ?></h4>
                                            <p class="text-muted mb-0">Canceladas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de reservas -->
                            <table id="todasReservasTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Zona</th>
                                        <th scope="col">Residente</th>
                                        <th scope="col">Cédula</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Apartamento</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Hora Inicio</th>
                                        <th scope="col">Hora Fin</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Fecha Creación</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <tr class="<?php 
                                            echo ($reserva['reserva_estado'] === 'activa') ? 'reserva-activa' : 
                                                 (($reserva['reserva_estado'] === 'cancelada') ? 'reserva-cancelada' : 'reserva-completada'); 
                                        ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($reserva['zona_imagen'])): ?>
                                                        <img src="<?php echo htmlspecialchars($reserva['zona_imagen']); ?>" 
                                                             alt="<?php echo htmlspecialchars($reserva['zona_nombre']); ?>" 
                                                             class="zona-mini me-2">
                                                    <?php else: ?>
                                                        <div class="zona-mini-placeholder me-2">
                                                            <i class="ri-community-line"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($reserva['zona_nombre']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($reserva['usu_nombre_completo'] ?? $reserva['reserva_nombre_residente']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['usu_cedula'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['usu_telefono'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['reserva_apartamento']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($reserva['reserva_fecha'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($reserva['reserva_hora_inicio'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($reserva['reserva_hora_fin'])); ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                    switch($reserva['reserva_estado']) {
                                                        case 'activa': echo 'bg-success'; break;
                                                        case 'completada': echo 'bg-info'; break;
                                                        case 'cancelada': echo 'bg-warning text-dark'; break;
                                                        default: echo 'bg-secondary';
                                                    } 
                                                ?>">
                                                    <?php echo ucfirst($reserva['reserva_estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reserva['reserva_fecha_creacion'])); ?></td>
                                            <td>
                                                <?php if ($usuario['rol'] === 'Administrador'): ?>
                                                    <button class="btn btn-sm btn-info me-1" 
                                                            onclick="verDetalles(<?php echo htmlspecialchars(json_encode($reserva)); ?>)" 
                                                            title="Ver detalles">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <?php if ($reserva['reserva_estado'] === 'activa'): ?>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="confirmarEliminacion(<?php echo $reserva['reserva_id']; ?>, '<?php echo htmlspecialchars($reserva['zona_nombre']); ?>', '<?php echo $reserva['reserva_fecha']; ?>')" 
                                                                title="Eliminar reserva">
                                                            <i class="ri-delete-bin-2-fill"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Solo lectura</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para ver detalles de reserva -->
    <div class="modal fade" id="modalDetallesReserva" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-information-line me-2"></i>Detalles de la Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-reserva-content">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-delete-bin-line me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-alert-line me-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará permanentemente la reserva.
                    </div>
                    <p>¿Estás seguro de que deseas eliminar la siguiente reserva?</p>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-1"><strong>Zona:</strong> <span id="modal-zona"></span></p>
                            <p class="mb-0"><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <a href="#" id="btn-confirmar-eliminacion" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-1"></i>Sí, Eliminar
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable si hay reservas
            <?php if (!empty($reservas)): ?>
            $('#todasReservasTable').DataTable({
                "responsive": true,
                "pageLength": 25,
                "order": [[5, "desc"]], // Ordenar por fecha descendente
                "columnDefs": [
                    {
                        "targets": [10], // Columna de acciones
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: '<i class="ri-file-excel-line me-1"></i>Excel',
                        className: 'btn btn-success btn-sm me-1',
                        title: 'Reservas_' + new Date().toLocaleDateString('es-ES').replace(/\//g, '-'),
                        exportOptions: {
                            columns: ':not(:last-child)' // Excluir columna de acciones
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ri-file-pdf-line me-1"></i>PDF',
                        className: 'btn btn-danger btn-sm me-1',
                        title: 'Reservas_' + new Date().toLocaleDateString('es-ES').replace(/\//g, '-'),
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ]
            });
            <?php endif; ?>
        });

        // Función para ver detalles de reserva
        function verDetalles(reserva) {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="ri-community-line me-2"></i>Información de la Zona</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Zona:</strong> ${reserva.zona_nombre}</p>
                                <p><strong>Fecha:</strong> ${new Date(reserva.reserva_fecha).toLocaleDateString('es-ES')}</p>
                                <p><strong>Horario:</strong> ${reserva.reserva_hora_inicio.substring(0,5)} - ${reserva.reserva_hora_fin.substring(0,5)}</p>
                                <p><strong>Estado:</strong> 
                                    <span class="badge ${getEstadoClass(reserva.reserva_estado)}">${reserva.reserva_estado.charAt(0).toUpperCase() + reserva.reserva_estado.slice(1)}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="ri-user-line me-2"></i>Información del Residente</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Nombre:</strong> ${reserva.usu_nombre_completo || reserva.reserva_nombre_residente}</p>
                                <p><strong>Cédula:</strong> ${reserva.usu_cedula || 'N/A'}</p>
                                <p><strong>Teléfono:</strong> ${reserva.usu_telefono || 'N/A'}</p>
                                <p><strong>Apartamento:</strong> ${reserva.reserva_apartamento}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="ri-information-line me-2"></i>Detalles Adicionales</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>ID de Reserva:</strong> ${reserva.reserva_id}</p>
                                <p><strong>Fecha de Creación:</strong> ${new Date(reserva.reserva_fecha_creacion).toLocaleString('es-ES')}</p>
                                ${reserva.reserva_observaciones ? `<p><strong>Observaciones:</strong> ${reserva.reserva_observaciones}</p>` : '<p><em>Sin observaciones</em></p>'}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detalles-reserva-content').innerHTML = content;
            new bootstrap.Modal(document.getElementById('modalDetallesReserva')).show();
        }

        // Función auxiliar para obtener clase CSS del estado
        function getEstadoClass(estado) {
            switch(estado) {
                case 'activa': return 'bg-success';
                case 'completada': return 'bg-info';
                case 'cancelada': return 'bg-warning text-dark';
                default: return 'bg-secondary';
            }
        }

        // Función para confirmar eliminación
        function confirmarEliminacion(id, zona, fecha) {
            document.getElementById('modal-zona').textContent = zona;
            document.getElementById('modal-fecha').textContent = new Date(fecha).toLocaleDateString('es-ES');
            document.getElementById('btn-confirmar-eliminacion').href = 
                `/index.php?controller=Reservas&action=eliminarReserva&id=${id}`;
            
            new bootstrap.Modal(document.getElementById('modalConfirmarEliminacion')).show();
        }
    </script>
</body>
</html>