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

// Consultar reservas del usuario actual
$usuario_id = $_SESSION['usuario']['id'];
$sql = "SELECT r.*, z.zona_nombre, z.zona_imagen 
        FROM tbl_reservas r 
        INNER JOIN tbl_zonas z ON r.zona_id = z.zona_id 
        WHERE r.usuario_id = ? 
        ORDER BY r.reserva_fecha DESC, r.reserva_hora_inicio DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$reservas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Mis Reservas</title>
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
                            <h3 class="mb-0">MIS RESERVAS</h3>
                        </div>
                        
                        <!-- Botón de nueva reserva alineado a la derecha -->
                        <div class="botones-navegacion">
                            <a href="../views/reservas.php" class="btn btn-custom">NUEVA RESERVA</a>
                        </div>

                        <?php if (empty($reservas)): ?>
                            <!-- Mensaje cuando no hay reservas -->
                            <div class="alert alert-info text-center">
                                <i class="ri-calendar-line" style="font-size: 3rem; color: #17a2b8;"></i>
                                <h4 class="mt-3">No tienes reservas</h4>
                                <p class="mb-3">Aún no has realizado ninguna reserva de zonas comunes.</p>
                                <a href="../views/reservas.php" class="btn btn-custom">
                                    <i class="ri-add-line me-1"></i>Hacer Primera Reserva
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Tabla de reservas -->
                            <table id="misReservasTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Zona</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Hora Inicio</th>
                                        <th scope="col">Hora Fin</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Apartamento</th>
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
                                            <td><?php echo htmlspecialchars($reserva['reserva_apartamento']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($reserva['reserva_fecha_creacion'])); ?></td>
                                            <td>
                                                <?php 
                                                $fechaReserva = new DateTime($reserva['reserva_fecha']);
                                                $hoy = new DateTime();
                                                $esVigente = ($fechaReserva >= $hoy && $reserva['reserva_estado'] === 'activa');
                                                ?>
                                                
                                                <?php if ($esVigente): ?>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="confirmarCancelacion(<?php echo $reserva['reserva_id']; ?>, '<?php echo htmlspecialchars($reserva['zona_nombre']); ?>', '<?php echo $reserva['reserva_fecha']; ?>')">
                                                        <i class="ri-close-line"></i> Cancelar
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted small">No disponible</span>
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

    <!-- Modal de confirmación para cancelar -->
    <div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-alert-line me-2"></i>Confirmar Cancelación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ri-alert-line me-2"></i>
                        <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                    </div>
                    <p>¿Estás seguro de que deseas cancelar la siguiente reserva?</p>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-1"><strong>Zona:</strong> <span id="modal-zona"></span></p>
                            <p class="mb-0"><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>No, mantener
                    </button>
                    <a href="#" id="btn-confirmar-cancelacion" class="btn btn-danger">
                        <i class="ri-close-line me-1"></i>Sí, Cancelar Reserva
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "./Layout/footer.php"; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable si hay reservas
            <?php if (!empty($reservas)): ?>
            $('#misReservasTable').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último", 
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
                "responsive": true,
                "pageLength": 10,
                "order": [[1, "desc"]], // Ordenar por fecha descendente
                "columnDefs": [
                    {
                        "targets": [7], // Columna de acciones
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
            <?php endif; ?>
        });

        // Función para confirmar cancelación
        function confirmarCancelacion(reservaId, zona, fecha) {
            document.getElementById('modal-zona').textContent = zona;
            document.getElementById('modal-fecha').textContent = new Date(fecha).toLocaleDateString('es-ES');
            document.getElementById('btn-confirmar-cancelacion').href = 
                `/index.php?controller=Reservas&action=eliminarReserva&id=${reservaId}`;
            
            new bootstrap.Modal(document.getElementById('modalConfirmarCancelacion')).show();
        }
    </script>
</body>
</html>