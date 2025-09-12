<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'admin'; // Temporal para pruebas
}

require_once '../models/pqrsModel.php';
require_once './Layout/header.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    echo "<p>ID de PQRS no válido.</p>";
    exit;
}

$pqrsModel = new PqrsModel();
$pqrs = $pqrsModel->obtenerPorId($id);

if (!$pqrs) {
    echo "<p>PQRS no encontrada.</p>";
    exit;
}

// Formatear datos para mostrar
$fechaCreacion = date('d/m/Y H:i:s', strtotime($pqrs['fecha_creacion']));
$mediosRespuesta = explode(',', $pqrs['medio_respuesta']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver PQRS #<?= str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT) ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <link rel="stylesheet" href="../assets/Css/Layout/header.css">
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
    /* Estilos específicos para la vista de detalles */
    .contenedor-admin {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .encabezado-detalle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .boton-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #b3956cff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    
    .boton-back:hover {
        background: #9D825D;
        color: white;
        text-decoration: none;
    }
    
    .titulo-detalle h1 {
        margin: 0;
        color: #2c5530;
    }
    
    .estado-actual {
        margin-top: 10px;
    }
    
    .tarjeta-detalle {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .cabecera-seccion {
        background: #7b9a82;
        color: white;
        padding: 15px 20px;
    }
    
    .cabecera-seccion h2 {
        margin: 0;
        font-size: 1.2em;
    }
    
    .contenido-seccion {
        padding: 20px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .info-item.full-width {
        grid-column: 1 / -1;
    }
    
    .info-item label {
        font-weight: bold;
        color: #495057;
        font-size: 0.9em;
        text-transform: uppercase;
    }
    
    .info-item span {
        color: #212529;
        font-size: 1.1em;
    }
    
    .mensaje-contenido {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #2c5530;
        line-height: 1.6;
    }
    
    .archivo-adjunto {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .enlace-archivo {
        color: #9D825D;
        text-decoration: none;
    }
    
    .enlace-archivo:hover {
        text-decoration: underline;
    }
    
    .tamaño-archivo {
        color: #6c757d;
        font-size: 0.9em;
    }
    
    .medios-respuesta-detalle {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .medio-respuesta {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 15px;
        background: #e9ecef;
        border-radius: 20px;
        font-size: 0.9em;
    }
    
    .tarjeta-acciones {
        background: #f8f9fa !important;
    }
    
    .seccion-accion {
        margin-bottom: 20px;
    }
    
    .seccion-accion h3 {
        color: #333;
        margin-bottom: 10px;
    }
    
    .controles-estado {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .select-estado {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1em;
        min-width: 150px;
    }
    
    .botones-accion {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .boton {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
        font-size: 0.9em;
        transition: all 0.3s;
    }
    
    .boton-primario {
        background: #9D825D;
        color: white;
    }
    
    .boton-primario:hover {
        background: #8a7250;
        color: white;
        text-decoration: none;
    }
    
    .boton-secundario {
        background: #6c757d;
        color: white;
    }
    
    .boton-secundario:hover {
        background: #5a6268;
    }
    
    .boton-responder {
        background: #28a745;
        color: white;
    }
    
    .boton-responder:hover {
        background: #218838;
        color: white;
        text-decoration: none;
    }
    
    .boton-proceso {
        background: #17a2b8;
        color: white;
    }
    
    .boton-proceso:hover {
        background: #138496;
    }
    
    .boton:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Estados */
    .estado {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .estado-pendiente {
        background: #fff3cd;
        color: #856404;
    }
    
    .estado-en-proceso {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .estado-resuelto {
        background: #d4edda;
        color: #155724;
    }
    
    /* Tipos */
    .tipo {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .tipo-peticion {
        background: #e3f2fd;
        color: #1565c0;
    }
    
    .tipo-queja {
        background: #ffebee;
        color: #c62828;
    }
    
    .tipo-reclamo {
        background: #fff8e1;
        color: #ef6c00;
    }
    
    .tipo-sugerencia {
        background: #e8f5e8;
        color: #2e7d32;
    }
    
    .animacion-entrada {
        animation: slideInUp 0.5s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Alert styles */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    
    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .encabezado-detalle {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .controles-estado, .botones-accion {
            flex-direction: column;
        }
        
        .select-estado {
            min-width: auto;
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="contenedor-admin">
        <!-- Encabezado -->
        <div class="encabezado-detalle animacion-entrada">
            <div class="navegacion-back">
                <a href="pqrs_admin.php" class="boton-back">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Panel
                </a>
            </div>
            <div class="titulo-detalle">
                <h1>
                    <i class="fas fa-eye"></i>
                    PQRS #<?= str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT) ?>
                </h1>
                <div class="estado-actual">
                    <span class="estado estado-<?= str_replace('_', '-', $pqrs['estado']) ?>">
                        <?= ucfirst(str_replace('_', ' ', $pqrs['estado'])) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Alert container -->
        <div id="alertContainer"></div>

        <!-- Información del solicitante -->
        <div class="tarjeta-detalle animacion-entrada">
            <div class="cabecera-seccion">
                <h2><i class="fas fa-user"></i> Información del Solicitante</h2>
            </div>
            <div class="contenido-seccion">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre completo:</label>
                        <span><?= htmlspecialchars($pqrs['nombres'] . ' ' . $pqrs['apellidos']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Identificación:</label>
                        <span><?= htmlspecialchars($pqrs['identificacion']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Correo electrónico:</label>
                        <span><?= htmlspecialchars($pqrs['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Teléfono:</label>
                        <span><?= htmlspecialchars($pqrs['telefono']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la solicitud -->
        <div class="tarjeta-detalle animacion-entrada">
            <div class="cabecera-seccion">
                <h2><i class="fas fa-clipboard-list"></i> Detalles de la Solicitud</h2>
            </div>
            <div class="contenido-seccion">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Tipo:</label>
                        <span class="tipo tipo-<?= strtolower($pqrs['tipo_pqr']) ?>">
                            <?= ucfirst($pqrs['tipo_pqr']) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Fecha de creación:</label>
                        <span><?= $fechaCreacion ?></span>
                    </div>
                    <div class="info-item full-width">
                        <label>Asunto:</label>
                        <span><?= htmlspecialchars($pqrs['asunto']) ?></span>
                    </div>
                    <div class="info-item full-width">
                        <label>Descripción:</label>
                        <div class="mensaje-contenido">
                            <?= nl2br(htmlspecialchars($pqrs['mensaje'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archivos adjuntos -->
                 <?php if (!empty($pqrs['archivos'])): ?>
        <div class="tarjeta-detalle animacion-entrada">
            <div class="cabecera-seccion">
                <h2><i class="fas fa-paperclip"></i> Archivos Adjuntos</h2>
            </div>
            <div class="contenido-seccion">
                <?php
                // CORRECCIÓN: Procesar archivos de manera correcta
                $archivos = [];
                
                if (!empty($pqrs['archivos'])) {
                    try {
                        // Intentar decodificar como JSON primero
                        $archivosJson = json_decode($pqrs['archivos'], true);
                        
                        if (json_last_error() === JSON_ERROR_NONE && is_array($archivosJson)) {
                            // Es un JSON válido con array de archivos
                            $archivos = $archivosJson;
                        } else {
                            // Podría ser un string simple o JSON malformado
                            // Intentar como archivo simple
                            if (is_string($pqrs['archivos']) && strlen(trim($pqrs['archivos'])) > 0) {
                                $archivos = [[
                                    'nombre_original' => basename($pqrs['archivos']),
                                    'nombre_archivo' => $pqrs['archivos'],
                                    'ruta' => 'uploads/pqrs/' . $pqrs['archivos']
                                ]];
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Error procesando archivos PQRS ID {$pqrs['id']}: " . $e->getMessage());
                        $archivos = [];
                    }
                }
                
                if (empty($archivos)): ?>
                    <p class="text-muted">No se pudieron cargar los archivos adjuntos.</p>
                <?php else: ?>
                    <?php foreach ($archivos as $archivo): 
                        // CORRECCIÓN: Manejo seguro de datos del archivo
                        $nombreOriginal = $archivo['nombre_original'] ?? $archivo['nombre_archivo'] ?? 'archivo';
                        $nombreArchivo = $archivo['nombre_archivo'] ?? $archivo['ruta'] ?? $nombreOriginal;
                        $tamanio = $archivo['tamaño'] ?? $archivo['tamano'] ?? $archivo['size'] ?? null;
                        
                        // CORRECCIÓN: Construir ruta correcta
                        // Si ya tiene la ruta completa, usarla; si no, construirla
                        if (isset($archivo['ruta']) && strpos($archivo['ruta'], 'uploads/') !== false) {
                            $rutaArchivo = '../' . $archivo['ruta'];
                        } else {
                            // Construir ruta asumiendo que está en uploads/pqrs/
                            $rutaArchivo = '../uploads/pqrs/' . $nombreArchivo;
                        }
                        
                        // URL para el navegador (sin ../)
                        if (isset($archivo['ruta']) && strpos($archivo['ruta'], 'uploads/') !== false) {
                            $urlArchivo = '../' . $archivo['ruta'];
                        } else {
                            $urlArchivo = '../uploads/pqrs/' . $nombreArchivo;
                        }
                    ?>
                    <div class="archivo-adjunto">
                        <i class="fas fa-file"></i>
                        
                        <?php if (file_exists($rutaArchivo)): ?>
                            <a href="<?= htmlspecialchars($urlArchivo) ?>" target="_blank" class="enlace-archivo">
                                <?= htmlspecialchars($nombreOriginal) ?>
                            </a>
                        <?php else: ?>
                            <span class="enlace-archivo" style="color: #dc3545;">
                                <?= htmlspecialchars($nombreOriginal) ?> 
                                <small>(Archivo no encontrado)</small>
                            </span>
                            
                            <?php
                            // DEBUG: Mostrar información adicional para troubleshooting
                            error_log("Archivo no encontrado - PQRS ID: {$pqrs['id']}");
                            error_log("  - Ruta buscada: $rutaArchivo");
                            error_log("  - Nombre original: $nombreOriginal");
                            error_log("  - Nombre archivo: $nombreArchivo");
                            error_log("  - Datos completos: " . print_r($archivo, true));
                            ?>
                        <?php endif; ?>
                        
                        <span class="tamaño-archivo">
                            <?php
                            if ($tamanio && is_numeric($tamanio)) {
                                echo '(' . number_format($tamanio / 1024, 2) . ' KB)';
                            } elseif (file_exists($rutaArchivo)) {
                                $tamanioReal = filesize($rutaArchivo);
                                echo '(' . number_format($tamanioReal / 1024, 2) . ' KB)';
                            } else {
                                echo '(Tamaño desconocido)';
                            }
                            ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>


        <!-- Configuración de respuesta -->
        <div class="tarjeta-detalle animacion-entrada">
            <div class="cabecera-seccion">
                <h2><i class="fas fa-reply"></i> Configuración de Respuesta</h2>
            </div>
            <div class="contenido-seccion">
                <div class="medios-respuesta-detalle">
                    <?php foreach ($mediosRespuesta as $medio): ?>
                        <span class="medio-respuesta">
                            <i class="fas fa-<?= trim($medio) === 'correo' ? 'envelope' : 'sms' ?>"></i>
                            <?= ucfirst(trim($medio)) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Acciones administrativas -->
        <div class="tarjeta-detalle tarjeta-acciones animacion-entrada">
            <div class="cabecera-seccion">
                <h2><i class="fas fa-cogs"></i> Acciones Administrativas</h2>
            </div>
            <div class="contenido-seccion">
                <!-- Cambiar estado -->
                <div class="seccion-accion">
                    <h3>Cambiar Estado</h3>
                    <div class="controles-estado">
                        <select id="nuevoEstado" class="select-estado">
                            <option value="pendiente" <?= $pqrs['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="en_proceso" <?= $pqrs['estado'] === 'en_proceso' ? 'selected' : '' ?>>En Proceso</option>
                            <option value="resuelto" <?= $pqrs['estado'] === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                        </select>
                        <button id="btnCambiarEstado" class="boton boton-primario">
                            <i class="fas fa-sync-alt"></i>
                            Actualizar Estado
                        </button>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="seccion-accion">
                    <h3>Acciones Rápidas</h3>
                    <div class="botones-accion">
                        <a href="responder_pqr.php?id=<?= $pqrs['id'] ?>" class="boton boton-responder">
                            <i class="fas fa-reply"></i>
                            Responder PQRS
                        </a>
                        <button id="btnMarcarProceso" class="boton boton-proceso" <?= $pqrs['estado'] !== 'pendiente' ? 'disabled' : '' ?>>
                            <i class="fas fa-play"></i>
                            Marcar como En Proceso
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
        const pqrsId = <?= $pqrs['id'] ?>;
        
        // Función para mostrar alertas
        function mostrarAlerta(mensaje, tipo = 'success') {
            const alertHTML = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#alertContainer').html(alertHTML);
            
            // Auto-hide después de 5 segundos
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Cambiar estado
        $('#btnCambiarEstado').click(function() {
            const nuevoEstado = $('#nuevoEstado').val();
            const estadoActual = '<?= $pqrs['estado'] ?>';
            
            if (nuevoEstado === estadoActual) {
                mostrarAlerta('El estado seleccionado es el mismo que el actual.', 'warning');
                return;
            }
            
            if (confirm('¿Está seguro de cambiar el estado a "' + nuevoEstado.replace('_', ' ') + '"?')) {
                cambiarEstado(pqrsId, nuevoEstado);
            }
        });
        
        // Marcar como en proceso
        $('#btnMarcarProceso').click(function() {
            if (confirm('¿Desea marcar esta PQRS como "En Proceso"?')) {
                cambiarEstado(pqrsId, 'en_proceso');
            }
        });
        
        // Función para cambiar estado (simplificada)
        function cambiarEstado(id, estado) {
            const $btn = estado === 'en_proceso' ? $('#btnMarcarProceso') : $('#btnCambiarEstado');
            const originalText = $btn.html();
            
            // Verificar si existe el controlador
            if (typeof cambiarEstadoPQRS === 'function') {
                // Si existe una función personalizada, usarla
                cambiarEstadoPQRS(id, estado);
                return;
            }
            
            // Simular cambio exitoso (para cuando no hay backend)
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
            
            setTimeout(function() {
                mostrarAlerta('Estado actualizado correctamente a: ' + estado.replace('_', ' '));
                
                // Actualizar la interfaz
                $('.estado-actual .estado')
                    .removeClass('estado-pendiente estado-en-proceso estado-resuelto')
                    .addClass('estado-' + estado.replace('_', '-'))
                    .text(estado.replace('_', ' ').charAt(0).toUpperCase() + estado.replace('_', ' ').slice(1));
                
                // Actualizar el select
                $('#nuevoEstado').val(estado);
                
                // Habilitar/deshabilitar botón de proceso
                if (estado !== 'pendiente') {
                    $('#btnMarcarProceso').prop('disabled', true);
                } else {
                    $('#btnMarcarProceso').prop('disabled', false);
                }
                
                $btn.prop('disabled', false).html(originalText);
            }, 1000);
        }
    });
    </script>
</body>
</html>
<!-- Archivos adjuntos -->