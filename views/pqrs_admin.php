<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar que sea administrador para acceder a esta vista
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!in_array($rol, ['Administrador'], true)) {
    header("Location: pqrs.php"); // Redirigir a la vista normal de PQRS
    exit();
}

require_once "../config/db.php";
require_once '../models/pqrsModel.php';
require_once './Layout/header.php';

$pqrsModel = new PqrsModel();

// Obtener todos los PQRS para el administrador
$registros = [];
try {
    $registros = $pqrsModel->obtenerTodos();
} catch (Exception $e) {
    error_log("Error al obtener PQRS: " . $e->getMessage());
    $registros = [];
}

// Estadísticas para el dashboard
$totalPqrs = count($registros);
$pendientes = count(array_filter($registros, fn($pqr) => $pqr['estado'] === 'pendiente'));
$enProceso = count(array_filter($registros, fn($pqr) => $pqr['estado'] === 'en_proceso'));
$resueltos = count(array_filter($registros, fn($pqr) => $pqr['estado'] === 'resuelto'));

// Recuperar mensaje de éxito si existe
$mensaje = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de PQRS - Administrador</title>
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
    .contenedor-admin {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .encabezado-admin {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #7b9a82 0%, #518056ff 100%);
        color: white;
        border-radius: 10px;
    }
    
    .tarjetas-estadisticas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .tarjeta {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .tarjeta:hover {
        transform: translateY(-5px);
    }
    
    .cabecera-tarjeta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .titulo-tarjeta {
        font-size: 14px;
        font-weight: 600;
        color: #666;
    }
    
    .icono-tarjeta {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .numero-tarjeta {
        font-size: 32px;
        font-weight: bold;
        margin: 0;
        color: #333;
    }
    
    .tarjeta-total .icono-tarjeta { background: #e3f2fd; color: #1976d2; }
    .tarjeta-pendiente .icono-tarjeta { background: #fff3e0; color: #f57c00; }
    .tarjeta-proceso .icono-tarjeta { background: #e8f5e8; color: #388e3c; }
    .tarjeta-resuelto .icono-tarjeta { background: #f3e5f5; color: #7b1fa2; }
    
    .contenedor-tabla {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .cabecera-tabla {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .cabecera-tabla h2 {
        color: #333;
        margin: 0;
        font-weight: 600;
    }
    
    .estado {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .estado-pendiente { background: #fff3cd; color: #856404; }
    .estado-en-proceso { background: #d4edda; color: #155724; }
    .estado-resuelto { background: #d1ecf1; color: #0c5460; }
    
    .tipo {
        padding: 4px 8px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .tipo-peticion { background: #e3f2fd; color: #1976d2; }
    .tipo-queja { background: #ffebee; color: #c62828; }
    .tipo-reclamo { background: #fff3e0; color: #ef6c00; }
    .tipo-sugerencia { background: #f3e5f5; color: #7b1fa2; }
    
    .boton {
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin: 2px;
    }
    
    .boton-ver {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .boton-responder {
        background: #e8f5e8;
        color: #388e3c;
    }
    
    .boton:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .animacion-entrada {
        animation: slideInUp 0.6s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dt-buttons {
        margin-bottom: 15px;
    }
    
    .dt-button {
        margin-right: 5px !important;
    }
    
    .alert {
        margin-bottom: 20px;
    }
    
    .sin-datos {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    
    .sin-datos i {
        font-size: 48px;
        margin-bottom: 10px;
        color: #ddd;
    }
    </style>
</head>
<body>
    <div class="contenedor-admin">
        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="encabezado-admin animacion-entrada">
            <h1>
                <i class="fas fa-clipboard-list"></i>
                Panel de Gestión PQRS
            </h1>
            <p>Administra y responde las peticiones, quejas, reclamos y sugerencias de los residentes</p>
        </div>

        <!-- Estadísticas -->
        <div class="tarjetas-estadisticas animacion-entrada">
            <div class="tarjeta tarjeta-total">
                <div class="cabecera-tarjeta">
                    <span class="titulo-tarjeta">Total PQRS</span>
                    <div class="icono-tarjeta">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <h3 class="numero-tarjeta"><?= $totalPqrs ?></h3>
            </div>

            <div class="tarjeta tarjeta-pendiente">
                <div class="cabecera-tarjeta">
                    <span class="titulo-tarjeta">Pendientes</span>
                    <div class="icono-tarjeta">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <h3 class="numero-tarjeta"><?= $pendientes ?></h3>
            </div>

            <div class="tarjeta tarjeta-proceso">
                <div class="cabecera-tarjeta">
                    <span class="titulo-tarjeta">En Proceso</span>
                    <div class="icono-tarjeta">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
                <h3 class="numero-tarjeta"><?= $enProceso ?></h3>
            </div>

            <div class="tarjeta tarjeta-resuelto">
                <div class="cabecera-tarjeta">
                    <span class="titulo-tarjeta">Resueltos</span>
                    <div class="icono-tarjeta">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <h3 class="numero-tarjeta"><?= $resueltos ?></h3>
            </div>
        </div>

        <!-- Tabla -->
        <div class="contenedor-tabla animacion-entrada">
            <div class="cabecera-tabla">
                <h2>
                    <i class="fas fa-table"></i>
                    Lista de PQRS
                </h2>
            </div>
            <div class="contenido-tabla">
                <?php if (empty($registros)): ?>
                    <div class="sin-datos">
                        <i class="fas fa-inbox"></i>
                        <h4>No hay PQRS registrados</h4>
                        <p>Aún no se han enviado peticiones, quejas, reclamos o sugerencias.</p>
                    </div>
                <?php else: ?>
                    <table class="tabla-pqrs table table-hover" id="tabla-pqrs">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Residente</th>
                                <th>Tipo</th>
                                <th>Medio</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $pqr): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            #<?= str_pad($pqr['id'], 4, '0', STR_PAD_LEFT) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                <?php 
                                                // Usar nombre completo si está disponible, sino usar nombres y apellidos
                                                $nombreCompleto = !empty($pqr['usu_nombre_completo']) 
                                                    ? $pqr['usu_nombre_completo'] 
                                                    : trim(($pqr['nombres'] ?? '') . ' ' . ($pqr['apellidos'] ?? ''));
                                                echo htmlspecialchars($nombreCompleto ?: 'Usuario');
                                                ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php if (!empty($pqr['usu_apartamento_residencia'])): ?>
                                                    <?= htmlspecialchars($pqr['usu_apartamento_residencia']) ?> - Torre <?= htmlspecialchars($pqr['usu_torre_residencia']) ?>
                                                <?php else: ?>
                                                    Residente
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="tipo tipo-<?= strtolower($pqr['tipo_pqr']) ?>">
                                            <?= ucfirst($pqr['tipo_pqr']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $medios = explode(',', $pqr['medio_respuesta']);
                                        foreach ($medios as $medio) {
                                            $medio = trim($medio);
                                            $icono = $medio === 'correo' ? 'envelope' : 'sms';
                                            echo '<i class="fas fa-' . $icono . '"></i> ' . ucfirst($medio) . '<br>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="estado estado-<?= str_replace('_', '-', $pqr['estado']) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $pqr['estado'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                <?= date('d/m/Y', strtotime($pqr['fecha_creacion'])) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($pqr['fecha_creacion'])) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="ver_pqr.php?id=<?= $pqr['id'] ?>" class="boton boton-ver">
                                                <i class="fas fa-eye"></i>
                                                Ver
                                            </a>
                                            <a href="responder_pqr.php?id=<?= $pqr['id'] ?>" class="boton boton-responder">
                                                <i class="fas fa-reply"></i>
                                                Responder
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (!empty($registros)): ?>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#tabla-pqrs').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [
                {
                    targets: [6], // Columna de acciones
                    orderable: false,
                    searchable: false
                }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6] // Excluir columna de acciones (ahora índice 7)
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5] // Excluir columna de acciones
                    }
                }
            ]
        });
    });
    </script>
    <?php endif; ?>

</body>
</html>

<?php require_once './Layout/footer.php'; ?>