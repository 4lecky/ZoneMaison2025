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

// Consultar TODAS las zonas comunes
$sql = "SELECT * FROM tbl_zonas ORDER BY zona_nombre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$zonas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Zonas Comunes</title>
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
                            <h3 class="mb-0">ZONAS COMUNES</h3>
                        </div>
                        
                        <!-- Botón de crear zona alineado a la derecha -->
                        <div class="botones-navegacion">
                            <a href="../views/CrearZona.php" class="btn btn-custom">CREAR ZONA</a>
                        </div>

                        <!-- Mensajes de respuesta -->
                        <?php if (isset($_SESSION['response'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['response_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['response']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['response'], $_SESSION['response_type']); ?>
                        <?php endif; ?>

                        <?php if (empty($zonas)): ?>
                            <!-- Mensaje cuando no hay zonas -->
                            <div class="alert alert-info text-center">
                                <i class="ri-community-line" style="font-size: 3rem; color: #17a2b8;"></i>
                                <h4 class="mt-3">No hay zonas comunes</h4>
                                <p class="mb-3">Aún no se han configurado zonas comunes en el sistema.</p>
                                <a href="../views/CrearZona.php" class="btn btn-custom">
                                    <i class="ri-add-line me-1"></i>Crear Primera Zona
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Estadísticas rápidas -->
                            <div class="row mb-4">
                                <?php
                                $totalZonas = count($zonas);
                                $zonasActivas = array_filter($zonas, function($z) { return $z['zona_estado'] === 'activo'; });
                                $zonasInactivas = array_filter($zonas, function($z) { return $z['zona_estado'] === 'inactivo'; });
                                $zonasMantenimiento = array_filter($zonas, function($z) { return $z['zona_estado'] === 'mantenimiento'; });
                                ?>
                                
                                <div class="col-md-3">
                                    <div class="card text-center border-primary">
                                        <div class="card-body">
                                            <i class="ri-community-line" style="font-size: 2rem; color: #0d6efd;"></i>
                                            <h4 class="mt-2"><?php echo $totalZonas; ?></h4>
                                            <p class="text-muted mb-0">Total Zonas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-success">
                                        <div class="card-body">
                                            <i class="ri-check-double-line" style="font-size: 2rem; color: #198754;"></i>
                                            <h4 class="mt-2"><?php echo count($zonasActivas); ?></h4>
                                            <p class="text-muted mb-0">Activas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-warning">
                                        <div class="card-body">
                                            <i class="ri-tools-line" style="font-size: 2rem; color: #ffc107;"></i>
                                            <h4 class="mt-2"><?php echo count($zonasMantenimiento); ?></h4>
                                            <p class="text-muted mb-0">Mantenimiento</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center border-danger">
                                        <div class="card-body">
                                            <i class="ri-close-circle-line" style="font-size: 2rem; color: #dc3545;"></i>
                                            <h4 class="mt-2"><?php echo count($zonasInactivas); ?></h4>
                                            <p class="text-muted mb-0">Inactivas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de zonas -->
                            <table id="zonasTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Zona</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Capacidad</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Horario</th>
                                        <th scope="col">Duración Máx.</th>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($zonas as $zona): ?>
                                        <tr class="<?php 
                                            echo ($zona['zona_estado'] === 'activo') ? 'zona-activa' : 
                                                 (($zona['zona_estado'] === 'inactivo') ? 'zona-inactiva' : 'zona-mantenimiento'); 
                                        ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($zona['zona_imagen'])): ?>
                                                        <img src="<?php echo htmlspecialchars($zona['zona_imagen']); ?>" 
                                                             alt="<?php echo htmlspecialchars($zona['zona_nombre']); ?>" 
                                                             class="zona-mini me-2">
                                                    <?php else: ?>
                                                        <div class="zona-mini-placeholder me-2">
                                                            <i class="ri-community-line"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($zona['zona_nombre']); ?></strong>
                                                        <br><small class="text-muted">ID: <?php echo $zona['zona_id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars(substr($zona['zona_descripcion'], 0, 80)) . (strlen($zona['zona_descripcion']) > 80 ? '...' : ''); ?></td>
                                            <td class="text-center"><?php echo $zona['zona_capacidad']; ?> personas</td>
                                            <td>
                                                <span class="badge <?php 
                                                    switch($zona['zona_estado']) {
                                                        case 'activo': echo 'bg-success'; break;
                                                        case 'inactivo': echo 'bg-danger'; break;
                                                        case 'mantenimiento': echo 'bg-warning text-dark'; break;
                                                        default: echo 'bg-secondary';
                                                    } 
                                                ?>">
                                                    <?php echo ucfirst($zona['zona_estado']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php echo substr($zona['zona_hora_apertura'], 0, 5); ?> - 
                                                <?php echo substr($zona['zona_hora_cierre'], 0, 5); ?>
                                            </td>
                                            <td class="text-center"><?php echo $zona['zona_duracion_maxima']; ?> horas</td>
                                            <td class="text-center">
                                                <?php if (!empty($zona['zona_imagen'])): ?>
                                                    <i class="ri-image-line text-success" title="Con imagen"></i>
                                                <?php else: ?>
                                                    <i class="ri-image-line text-muted" title="Sin imagen"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info me-1" 
                                                        onclick="verDetalles(<?php echo htmlspecialchars(json_encode($zona)); ?>)" 
                                                        title="Ver detalles">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning me-1" 
                                                        onclick="editarZona(<?php echo $zona['zona_id']; ?>)" 
                                                        title="Editar zona">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="confirmarEliminacion(<?php echo $zona['zona_id']; ?>, '<?php echo htmlspecialchars($zona['zona_nombre']); ?>')" 
                                                        title="Eliminar zona">
                                                    <i class="ri-delete-bin-2-fill"></i>
                                                </button>
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

    <!-- Modal para ver detalles de zona -->
    <div class="modal fade" id="modalDetallesZona" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-information-line me-2"></i>Detalles de la Zona
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-zona-content">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-editar-desde-modal">
                        <i class="ri-edit-line me-1"></i>Editar Zona
                    </button>
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
                        <strong>¡Atención!</strong> Esta acción eliminará permanentemente la zona común y todas sus reservas asociadas.
                    </div>
                    <p>¿Estás seguro de que deseas eliminar la siguiente zona?</p>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0"><strong>Zona:</strong> <span id="modal-zona-nombre"></span></p>
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
    </div>

    <?php require_once "./Layout/footer.php"; ?>

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
            // Inicializar DataTable si hay zonas
            <?php if (!empty($zonas)): ?>
            $('#zonasTable').DataTable({
                "responsive": true,
                "pageLength": 15,
                "order": [[0, "asc"]], // Ordenar por nombre de zona
                "columnDefs": [
                    {
                        "targets": [7], // Columna de acciones
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
                        title: 'Zonas_Comunes_' + new Date().toLocaleDateString('es-ES').replace(/\//g, '-'),
                        exportOptions: {
                            columns: ':not(:last-child)' // Excluir columna de acciones
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ri-file-pdf-line me-1"></i>PDF',
                        className: 'btn btn-danger btn-sm me-1',
                        title: 'Zonas Comunes - ZoneMaisons',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
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
                }
            });
            <?php endif; ?>
        });

        // Función para ver detalles de zona
        function verDetalles(zona) {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        ${zona.zona_imagen ? 
                            `<img src="${zona.zona_imagen}" alt="${zona.zona_nombre}" class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">` : 
                            `<div style="width: 100%; height: 300px; background: linear-gradient(135deg, #8FBC8F, #98FB98); display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 0.375rem; color: white; margin-bottom: 1rem;">
                                <i class="ri-community-line" style="font-size: 3rem;"></i>
                                <p class="mt-2">Sin imagen</p>
                            </div>`
                        }
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-primary">${zona.zona_nombre}</h4>
                        <span class="badge ${getEstadoClass(zona.zona_estado)} mb-3">${zona.zona_estado.charAt(0).toUpperCase() + zona.zona_estado.slice(1)}</span>
                        
                        <div class="mb-3">
                            <h6><i class="ri-information-line me-2"></i>Información General</h6>
                            <p><strong>ID:</strong> #${zona.zona_id}</p>
                            <p><strong>Descripción:</strong> ${zona.zona_descripcion || 'Sin descripción'}</p>
                            <p><strong>Capacidad:</strong> ${zona.zona_capacidad} personas</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="ri-time-line me-2"></i>Horarios</h6>
                            <p><strong>Apertura:</strong> ${zona.zona_hora_apertura.substring(0,5)}</p>
                            <p><strong>Cierre:</strong> ${zona.zona_hora_cierre.substring(0,5)}</p>
                            <p><strong>Duración máxima:</strong> ${zona.zona_duracion_maxima} horas</p>
                        </div>
                    </div>
                </div>
                
                ${zona.zona_terminos_condiciones ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Términos y Condiciones</h6>
                                </div>
                                <div class="card-body">
                                    <pre style="white-space: pre-wrap; font-family: inherit;">${zona.zona_terminos_condiciones}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}
            `;
            
            document.getElementById('detalles-zona-content').innerHTML = content;
            document.getElementById('btn-editar-desde-modal').onclick = () => {
                bootstrap.Modal.getInstance(document.getElementById('modalDetallesZona')).hide();
                editarZona(zona.zona_id);
            };
            
            new bootstrap.Modal(document.getElementById('modalDetallesZona')).show();
        }

        // Función auxiliar para obtener clase CSS del estado
        function getEstadoClass(estado) {
            switch(estado) {
                case 'activo': return 'bg-success';
                case 'inactivo': return 'bg-danger';
                case 'mantenimiento': return 'bg-warning text-dark';
                default: return 'bg-secondary';
            }
        }

        // Función para editar zona
        function editarZona(zonaId) {
            window.location.href = `../views/editarZona.php?id=${zonaId}`;
        }

        // Función para confirmar eliminación - CORREGIDA
        function confirmarEliminacion(id, nombre) {
            document.getElementById('modal-zona-nombre').textContent = nombre;
            
            // CORRECCIÓN: Cambiar la URL para que apunte al controlador correcto
            document.getElementById('btn-confirmar-eliminacion').href = 
                `../controller/reservasController.php?action=eliminarZona&id=${id}`;
            
            new bootstrap.Modal(document.getElementById('modalConfirmarEliminacion')).show();
        }
    </script>

    <style>
        .zona-activa {
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .zona-inactiva {
            background-color: rgba(220, 53, 69, 0.05);
            opacity: 0.7;
        }
        
        .zona-mantenimiento {
            background-color: rgba(255, 193, 7, 0.05);
        }
    </style>
</body>
</html>