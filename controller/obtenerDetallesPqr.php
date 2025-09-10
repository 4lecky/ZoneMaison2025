<?php
// obtenerDetallesPqr.php - Controlador para obtener detalles y respuestas de PQRS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

require_once '../models/pqrsModel.php';

try {
    $id = (int)($_GET['id'] ?? 0);
    $tipo = $_GET['tipo'] ?? 'detalles'; // 'detalles' o 'respuesta'
    
    if ($id <= 0) {
        throw new Exception('ID de PQRS no válido');
    }
    
    $pqrsModel = new PqrsModel();
    $pqrs = $pqrsModel->obtenerPqrsCompleta($id);
    
    if (!$pqrs) {
        throw new Exception('PQRS no encontrada');
    }
    
    // Verificar permisos: solo el usuario dueño o admin puede ver
    $usuario = $_SESSION['usuario'];
    $esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    $esPropietario = $usuario['usuario_cc'] == $pqrs['usuario_cc'];
    
    if (!$esAdmin && !$esPropietario) {
        throw new Exception('No tiene permisos para ver esta PQRS');
    }
    
    if ($tipo === 'respuesta') {
        $html = generarHtmlRespuesta($pqrs);
    } else {
        $html = generarHtmlDetalles($pqrs);
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Generar HTML para mostrar respuesta de PQRS
 */
function generarHtmlRespuesta($pqrs) {
    if (empty($pqrs['respuesta'])) {
        return '<div class="alert alert-warning">
                    <i class="ri-information-line"></i>
                    Esta PQRS aún no ha sido respondida.
                </div>';
    }
    
    $numeroRadicado = str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT);
    $fechaRespuesta = $pqrs['fecha_respuesta'] ? date('d/m/Y H:i', strtotime($pqrs['fecha_respuesta'])) : 'N/A';
    $nombreAdmin = $pqrs['nombre_admin'] ?? 'Administración';
    
    $html = "
    <div class='respuesta-container'>
        <div class='respuesta-header'>
            <div class='respuesta-info'>
                <h3><i class='ri-mail-line'></i> Respuesta a su " . ucfirst($pqrs['tipo_pqr']) . "</h3>
                <div class='respuesta-meta'>
                    <span class='radicado'>Radicado: PQRS-" . date('Y') . "-$numeroRadicado</span>
                    <span class='fecha'>Respondida el: $fechaRespuesta</span>
                    <span class='respondido-por'>Por: $nombreAdmin</span>
                </div>
            </div>
            <div class='respuesta-estado'>
                <span class='estado-tag resuelto'>
                    <i class='ri-check-circle-fill'></i> Respondida
                </span>
            </div>
        </div>
        
        <div class='solicitud-resumen'>
            <h4><i class='ri-file-text-line'></i> Su solicitud original:</h4>
            <div class='solicitud-box'>
                <p><strong>Asunto:</strong> " . htmlspecialchars($pqrs['asunto']) . "</p>
                <div class='mensaje-original'>" . nl2br(htmlspecialchars($pqrs['mensaje'])) . "</div>
            </div>
        </div>
        
        <div class='respuesta-contenido'>
            <h4><i class='ri-chat-3-line'></i> Nuestra respuesta:</h4>
            <div class='respuesta-box'>
                " . nl2br(htmlspecialchars($pqrs['respuesta'])) . "
            </div>
        </div>
        
        <div class='respuesta-acciones'>
            <div class='notificacion-info'>
                <i class='ri-notification-3-line'></i>
                <small>Esta respuesta fue enviada a su correo electrónico";
    
    // Agregar info de SMS si aplica
    $medios = explode(',', $pqrs['medio_respuesta']);
    if (in_array('sms', array_map('trim', $medios))) {
        $html .= " y por SMS";
    }
    
    $html .= ".</small>
            </div>
        </div>
    </div>
    
    <style>
    .respuesta-container {
        font-family: Arial, sans-serif;
    }
    
    .respuesta-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 20px;
        background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
        border-radius: 10px;
        border-left: 4px solid #28a745;
    }
    
    .respuesta-info h3 {
        color: #28a745;
        margin: 0 0 10px 0;
        font-size: 1.3em;
    }
    
    .respuesta-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 0.9em;
        color: #666;
    }
    
    .respuesta-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .estado-tag {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .estado-tag.resuelto {
        background: #d4edda;
        color: #155724;
    }
    
    .solicitud-resumen {
        margin-bottom: 25px;
    }
    
    .solicitud-resumen h4 {
        color: #666;
        margin-bottom: 15px;
        font-size: 1.1em;
    }
    
    .solicitud-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #6c757d;
    }
    
    .mensaje-original {
        margin-top: 10px;
        font-style: italic;
        color: #555;
        max-height: 100px;
        overflow-y: auto;
    }
    
    .respuesta-contenido h4 {
        color: #28a745;
        margin-bottom: 15px;
        font-size: 1.2em;
    }
    
    .respuesta-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 2px solid #28a745;
        line-height: 1.6;
        font-size: 1.05em;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.1);
    }
    
    .respuesta-acciones {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }
    
    .notificacion-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #666;
    }
    
    .notificacion-info i {
        color: #007bff;
    }
    </style>";
    
    return $html;
}

/**
 * Generar HTML para mostrar detalles completos de PQRS
 */
function generarHtmlDetalles($pqrs) {
    $numeroRadicado = str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT);
    $fechaCreacion = date('d/m/Y H:i', strtotime($pqrs['fecha_creacion']));
    $fechaRespuesta = $pqrs['fecha_respuesta'] ? date('d/m/Y H:i', strtotime($pqrs['fecha_respuesta'])) : null;
    
    // Procesar archivos adjuntos
    $archivos = '';
    if (!empty($pqrs['archivos'])) {
        $archivosData = json_decode($pqrs['archivos'], true);
        if ($archivosData && is_array($archivosData)) {
            $archivos = '<div class="archivos-adjuntos">
                        <h5><i class="ri-attachment-line"></i> Archivos adjuntos:</h5>
                        <ul class="lista-archivos">';
            foreach ($archivosData as $archivo) {
                $nombreArchivo = htmlspecialchars($archivo['nombre_original'] ?? 'Archivo');
                $rutaArchivo = htmlspecialchars($archivo['ruta'] ?? '');
                $archivos .= "<li>
                    <a href='../$rutaArchivo' target='_blank' class='enlace-archivo'>
                        <i class='ri-file-line'></i> $nombreArchivo
                    </a>
                </li>";
            }
            $archivos .= '</ul></div>';
        }
    }
    
    // Procesar medios de respuesta
    $medios = explode(',', $pqrs['medio_respuesta']);
    $mediosHtml = '';
    foreach ($medios as $medio) {
        $medio = trim($medio);
        $icono = $medio === 'correo' ? 'mail-line' : 'message-3-line';
        $mediosHtml .= "<span class='medio-tag'><i class='ri-$icono'></i> " . ucfirst($medio) . "</span>";
    }
    
    $html = "
    <div class='detalles-container'>
        <div class='detalles-header'>
            <div class='titulo-pqrs'>
                <h3>PQRS-" . date('Y') . "-$numeroRadicado</h3>
                <span class='estado-badge estado-" . $pqrs['estado'] . "'>
                    " . ucfirst(str_replace('_', ' ', $pqrs['estado'])) . "
                </span>
            </div>
            <div class='tipo-pqrs'>
                <span class='tipo-badge tipo-" . $pqrs['tipo_pqr'] . "'>
                    " . ucfirst($pqrs['tipo_pqr']) . "
                </span>
            </div>
        </div>
        
        <div class='detalles-grid'>
            <div class='seccion-usuario'>
                <h4><i class='ri-user-line'></i> Información del solicitante</h4>
                <div class='info-usuario-detalles'>
                    <p><strong>Nombre:</strong> " . htmlspecialchars($pqrs['nombres'] . ' ' . $pqrs['apellidos']) . "</p>
                    <p><strong>Identificación:</strong> " . htmlspecialchars($pqrs['identificacion']) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($pqrs['email']) . "</p>
                    <p><strong>Teléfono:</strong> " . htmlspecialchars($pqrs['telefono']) . "</p>
                    <p><strong>Medios de respuesta:</strong><br>$mediosHtml</p>
                </div>
            </div>
            
            <div class='seccion-fechas'>
                <h4><i class='ri-calendar-line'></i> Fechas importantes</h4>
                <div class='info-fechas'>
                    <p><strong>Creada:</strong> $fechaCreacion</p>";
    
    if ($fechaRespuesta) {
        $html .= "<p><strong>Respondida:</strong> $fechaRespuesta</p>";
    } else {
        $html .= "<p><strong>Respondida:</strong> <em>Pendiente</em></p>";
    }
    
    $html .= "
                </div>
            </div>
        </div>
        
        <div class='seccion-contenido'>
            <h4><i class='ri-file-text-line'></i> Contenido de la solicitud</h4>
            <div class='contenido-box'>
                <h5>Asunto:</h5>
                <p class='asunto-detalles'>" . htmlspecialchars($pqrs['asunto']) . "</p>
                
                <h5>Descripción:</h5>
                <div class='mensaje-detalles'>" . nl2br(htmlspecialchars($pqrs['mensaje'])) . "</div>
            </div>
        </div>
        
        $archivos";
    
    // Si tiene respuesta, mostrarla
    if (!empty($pqrs['respuesta'])) {
        $nombreAdmin = $pqrs['nombre_admin'] ?? 'Administración';
        $html .= "
        <div class='seccion-respuesta'>
            <h4><i class='ri-chat-3-line'></i> Respuesta administrativa</h4>
            <div class='respuesta-box-detalles'>
                <div class='respuesta-meta-admin'>
                    <span><strong>Respondida por:</strong> $nombreAdmin</span>
                    <span><strong>Fecha:</strong> $fechaRespuesta</span>
                </div>
                <div class='respuesta-contenido-admin'>
                    " . nl2br(htmlspecialchars($pqrs['respuesta'])) . "
                </div>
            </div>
        </div>";
    }
    
    $html .= "
    </div>
    
    <style>
    .detalles-container {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }
    
    .detalles-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        border-left: 4px solid #007bff;
    }
    
    .titulo-pqrs {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .titulo-pqrs h3 {
        color: #007bff;
        margin: 0;
        font-size: 1.4em;
    }
    
    .estado-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .estado-pendiente { background: #fff3cd; color: #856404; }
    .estado-en_proceso { background: #cce5ff; color: #004085; }
    .estado-resuelto { background: #d1edda; color: #155724; }
    
    .tipo-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .tipo-peticion { background: #e3f2fd; color: #1976d2; }
    .tipo-queja { background: #ffebee; color: #c62828; }
    .tipo-reclamo { background: #fff3e0; color: #ef6c00; }
    .tipo-sugerencia { background: #f3e5f5; color: #7b1fa2; }
    
    .detalles-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .seccion-usuario, .seccion-fechas {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .seccion-usuario h4, .seccion-fechas h4 {
        color: #495057;
        margin: 0 0 15px 0;
        font-size: 1.1em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-usuario-detalles p, .info-fechas p {
        margin: 8px 0;
        color: #333;
    }
    
    .medio-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        background: #e9ecef;
        border-radius: 15px;
        font-size: 0.85em;
        margin-right: 10px;
        margin-bottom: 5px;
    }
    
    .seccion-contenido {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-bottom: 20px;
    }
    
    .seccion-contenido h4 {
        color: #495057;
        margin: 0 0 15px 0;
        font-size: 1.1em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .contenido-box h5 {
        color: #6c757d;
        margin: 15px 0 8px 0;
        font-size: 1em;
    }
    
    .asunto-detalles {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        border-left: 3px solid #007bff;
        font-weight: 500;
    }
    
    .mensaje-detalles {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 3px solid #6c757d;
        white-space: pre-wrap;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .archivos-adjuntos {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-bottom: 20px;
    }
    
    .archivos-adjuntos h5 {
        color: #495057;
        margin: 0 0 15px 0;
        font-size: 1.1em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .lista-archivos {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .lista-archivos li {
        margin-bottom: 8px;
    }
    
    .enlace-archivo {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #007bff;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    
    .enlace-archivo:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }
    
    .seccion-respuesta {
        background: #e8f5e8;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #28a745;
        margin-bottom: 20px;
    }
    
    .seccion-respuesta h4 {
        color: #28a745;
        margin: 0 0 15px 0;
        font-size: 1.2em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .respuesta-box-detalles {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #28a745;
    }
    
    .respuesta-meta-admin {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        font-size: 0.9em;
        color: #666;
    }
    
    .respuesta-contenido-admin {
        color: #333;
        line-height: 1.6;
        white-space: pre-wrap;
    }
    
    @media (max-width: 768px) {
        .detalles-grid {
            grid-template-columns: 1fr;
        }
        
        .detalles-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .titulo-pqrs {
            flex-direction: column;
            gap: 10px;
        }
    }
    </style>";
    
    return $html;
}