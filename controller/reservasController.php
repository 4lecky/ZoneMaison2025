<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/reservasModel.php';

$reservasModel = new ReservasModel($pdo);

// Verificar que se haya enviado una acción
if (!isset($_GET['action'])) {
    header('Location: ../views/reservas.php');
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'crearReserva':
        crearReserva();
        break;
    case 'crearZona':
        crearZona();
        break;
    case 'eliminarReserva':
        eliminarReserva();
        break;
    case 'getReservasPorZona':
        getReservasPorZona();
        break;
    case 'eliminarZona':
        eliminarZona();
        break;
    case 'actualizarZona':
        actualizarZona();
        break;
    case 'buscarUsuario':
        buscarUsuario();
        break;
    default:
        header('Location: ../views/reservas.php');
        exit;
}

function crearReserva() {
    global $reservasModel;
    
    try {
        // Validar que todos los campos requeridos estén presentes
        $camposRequeridos = ['zona_id', 'fecha_reserva', 'hora_inicio', 'hora_fin', 'numero_documento'];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                $_SESSION['response'] = "Error: El campo {$campo} es obligatorio.";
                $_SESSION['response_type'] = 'danger';
                header('Location: ../views/reservas.php');
                exit;
            }
        }
        
        // Obtener datos del formulario
        $zona_id = (int)$_POST['zona_id'];
        $fecha_reserva = $_POST['fecha_reserva'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $numero_documento = $_POST['numero_documento'];
        
        // Buscar el usuario en la base de datos usando el número de documento
        $usuario = $reservasModel->buscarUsuarioPorDocumento($numero_documento);
        
        if (!$usuario) {
            $_SESSION['response'] = "Error: No se encontró un usuario registrado con el documento: {$numero_documento}";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // Usar los datos del usuario encontrado
        $usuario_id = $usuario['usuario_cc']; // Usar el ID autoincremental
        $apartamento = $usuario['usu_apartamento_residencia'];
        $nombre_residente = $usuario['usu_nombre_completo'];
        
        // Validaciones de negocio
        
        // 1. Validar que la fecha no sea anterior a hoy
        if ($fecha_reserva < date('Y-m-d')) {
            $_SESSION['response'] = "Error: No se puede reservar para fechas pasadas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // 2. Validar que la hora de inicio sea anterior a la de fin
        if ($hora_inicio >= $hora_fin) {
            $_SESSION['response'] = "Error: La hora de inicio debe ser anterior a la hora de fin.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // 3. Obtener información de la zona para validaciones
        $zona = $reservasModel->obtenerZonaPorId($zona_id);
        if (!$zona) {
            $_SESSION['response'] = "Error: La zona seleccionada no existe.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // 4. Validar horarios permitidos de la zona
        if ($hora_inicio < $zona['zona_hora_apertura'] || $hora_fin > $zona['zona_hora_cierre']) {
            $_SESSION['response'] = "Error: El horario seleccionado está fuera del horario permitido para esta zona.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // 5. Validar duración máxima
        $duracion = (strtotime($hora_fin) - strtotime($hora_inicio)) / 3600; // en horas
        if ($duracion > $zona['zona_duracion_maxima']) {
            $_SESSION['response'] = "Error: La duración de la reserva excede el máximo permitido ({$zona['zona_duracion_maxima']} horas).";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // 6. Verificar conflictos de horario
        if ($reservasModel->verificarConflictoHorario($zona_id, $fecha_reserva, $hora_inicio, $hora_fin)) {
            $_SESSION['response'] = "Error: Ya existe una reserva para esta zona en el horario seleccionado.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/reservas.php');
            exit;
        }
        
        // Si todas las validaciones pasan, crear la reserva
        $reservaId = $reservasModel->crearReserva(
            $zona_id,
            $usuario_id,
            $apartamento,
            $nombre_residente,
            $fecha_reserva,
            $hora_inicio,
            $hora_fin,
            'activa', // estado por defecto
            null // observaciones
        );
        
        if ($reservaId) {
            $_SESSION['response'] = "¡Reserva creada exitosamente! Número de reserva: {$reservaId}";
            $_SESSION['response_type'] = 'success';
        } else {
            $_SESSION['response'] = "Error: No se pudo crear la reserva. Intente nuevamente.";
            $_SESSION['response_type'] = 'danger';
        }
        
    } catch (Exception $e) {
        $_SESSION['response'] = "Error interno: " . $e->getMessage();
        $_SESSION['response_type'] = 'danger';
    }
    
    header('Location: ../views/reservas.php');
    exit;
}

function crearZona() {
    global $reservasModel;
    
    try {
        // Verificar permisos de administrador
        if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
            $_SESSION['response'] = "Error: No tiene permisos para crear zonas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/crearZona.php');
            exit;
        }
        
        // Validar campos requeridos
        $camposRequeridos = ['zona_nombre', 'zona_capacidad', 'zona_hora_apertura', 'zona_hora_cierre', 'zona_duracion_maxima', 'zona_estado'];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                $_SESSION['response'] = "Error: El campo {$campo} es obligatorio.";
                $_SESSION['response_type'] = 'danger';
                header('Location: ../views/crearZona.php');
                exit;
            }
        }
        
        // Obtener datos del formulario
        $zona_nombre = trim($_POST['zona_nombre']);
        $zona_descripcion = trim($_POST['zona_descripcion'] ?? '');
        $zona_capacidad = (int)$_POST['zona_capacidad'];
        $zona_estado = $_POST['zona_estado'];
        $zona_hora_apertura = $_POST['zona_hora_apertura'];
        $zona_hora_cierre = $_POST['zona_hora_cierre'];
        $zona_duracion_maxima = (int)$_POST['zona_duracion_maxima'];
        $zona_terminos_condiciones = trim($_POST['zona_terminos_condiciones'] ?? '');
        
        // Validaciones
        if ($zona_hora_apertura >= $zona_hora_cierre) {
            $_SESSION['response'] = "Error: La hora de apertura debe ser anterior a la hora de cierre.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/crearZona.php');
            exit;
        }
        
        if ($zona_capacidad < 1 || $zona_capacidad > 500) {
            $_SESSION['response'] = "Error: La capacidad debe estar entre 1 y 500 personas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/crearZona.php');
            exit;
        }
        
        // Manejo de imagen (opcional)
        $zona_imagen = null;
        if (isset($_FILES['zona_imagen']) && $_FILES['zona_imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio = '../uploads/zonas/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $extension = pathinfo($_FILES['zona_imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('zona_') . '.' . $extension;
            $rutaDestino = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['zona_imagen']['tmp_name'], $rutaDestino)) {
                $zona_imagen = 'uploads/zonas/' . $nombreArchivo;
            }
        }
        
        // Crear la zona
        $zonaId = $reservasModel->crearZona(
            $zona_nombre,
            $zona_descripcion,
            $zona_capacidad,
            $zona_estado,
            $zona_imagen,
            $zona_hora_apertura,
            $zona_hora_cierre,
            $zona_duracion_maxima,
            $zona_terminos_condiciones
        );
        
        if ($zonaId) {
            $_SESSION['response'] = "¡Zona creada exitosamente! ID: {$zonaId}";
            $_SESSION['response_type'] = 'success';
            header('Location: ../views/zonas.php');
        } else {
            $_SESSION['response'] = "Error: No se pudo crear la zona. Intente nuevamente.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/crearZona.php');
        }
        
    } catch (Exception $e) {
        $_SESSION['response'] = "Error interno: " . $e->getMessage();
        $_SESSION['response_type'] = 'danger';
        header('Location: ../views/crearZona.php');
    }
    
    exit;
}

function eliminarReserva() {
    global $reservasModel;
    
    try {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['response'] = "Error: ID de reserva no válido.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/misReservas.php');
            exit;
        }
        
        $reserva_id = (int)$_GET['id'];
        $usuario_id = $_SESSION['usuario']['id'] ?? 1;
        $rol_usuario = $_SESSION['usuario']['rol'] ?? 'Usuario';
        
        // Verificar que la reserva existe y pertenece al usuario (a menos que sea admin)
        $reserva = $reservasModel->obtenerReservaPorId($reserva_id);
        
        if (!$reserva) {
            $_SESSION['response'] = "Error: La reserva no existe.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/misReservas.php');
            exit;
        }
        
        // Solo el dueño de la reserva o un administrador pueden eliminarla
        if ($reserva['usuario_id'] != $usuario_id && $rol_usuario !== 'Administrador') {
            $_SESSION['response'] = "Error: No tiene permisos para eliminar esta reserva.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/misReservas.php');
            exit;
        }
        
        // No se puede cancelar una reserva que ya pasó
        $fechaReserva = new DateTime($reserva['reserva_fecha']);
        $hoy = new DateTime();
        
        if ($fechaReserva < $hoy && $rol_usuario !== 'Administrador') {
            $_SESSION['response'] = "Error: No se puede cancelar una reserva que ya pasó.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/misReservas.php');
            exit;
        }
        
        // Actualizar estado a 'cancelada' en lugar de eliminar
        if ($reservasModel->cancelarReserva($reserva_id)) {
            $_SESSION['response'] = "Reserva cancelada exitosamente.";
            $_SESSION['response_type'] = 'success';
        } else {
            $_SESSION['response'] = "Error: No se pudo cancelar la reserva.";
            $_SESSION['response_type'] = 'danger';
        }
        
    } catch (Exception $e) {
        $_SESSION['response'] = "Error interno: " . $e->getMessage();
        $_SESSION['response_type'] = 'danger';
    }
    
    // Determinar a dónde redirigir basado en el referer
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    if (strpos($referer, 'todasReservas.php') !== false && ($rol_usuario === 'Administrador' || $rol_usuario === 'Vigilante')) {
        header('Location: ../views/todasReservas.php');
    } else {
        header('Location: ../views/misReservas.php');
    }
    exit;
}

function getReservasPorZona() {
    global $reservasModel;
    
    try {
        // Configurar cabeceras para JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        
        // Verificar si se envió el parámetro zona_id
        if (isset($_GET['zona_id']) && !empty($_GET['zona_id'])) {
            $zona_id = (int)$_GET['zona_id'];
            
            $reservas = $reservasModel->obtenerReservasPorZona($zona_id);
            
            // Formatear datos para el calendario JavaScript
            $reservasFormateadas = [];
            
            foreach ($reservas as $reserva) {
                $reservasFormateadas[] = [
                    'fecha' => $reserva['reserva_fecha'],
                    'hora_inicio' => substr($reserva['reserva_hora_inicio'], 0, 5),
                    'hora_fin' => substr($reserva['reserva_hora_fin'], 0, 5),
                    'residente' => $reserva['reserva_nombre_residente'],
                    'apartamento' => $reserva['reserva_apartamento']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $reservasFormateadas,
                'total' => count($reservasFormateadas)
            ]);
            
        } else {
            echo json_encode([
                'success' => true,
                'data' => [],
                'total' => 0,
                'message' => 'No se especificó zona_id'
            ]);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error de base de datos',
            'message' => $e->getMessage()
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error del servidor',
            'message' => $e->getMessage()
        ]);
    }
}

function eliminarZona() {
    global $reservasModel;
    
    try {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['response'] = "Error: ID de zona no válido.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        $zona_id = (int)$_GET['id'];
        $rol_usuario = $_SESSION['usuario']['rol'] ?? 'Usuario';
        
        // Solo administradores pueden eliminar zonas
        if ($rol_usuario !== 'Administrador') {
            $_SESSION['response'] = "Error: No tiene permisos para eliminar zonas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        // Verificar que la zona existe
        $zona = $reservasModel->obtenerZonaPorId($zona_id);
        
        if (!$zona) {
            $_SESSION['response'] = "Error: La zona no existe.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        // Verificar si hay reservas activas para esta zona
        $reservasActivas = $reservasModel->verificarReservasActivasZona($zona_id);
        
        if ($reservasActivas > 0) {
            $_SESSION['response'] = "Error: No se puede eliminar la zona porque tiene {$reservasActivas} reserva(s) activa(s). Cancele primero las reservas.";
            $_SESSION['response_type'] = 'warning';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        // Eliminar imagen si existe
        if (!empty($zona['zona_imagen']) && file_exists('../' . $zona['zona_imagen'])) {
            unlink('../' . $zona['zona_imagen']);
        }
        
        // Eliminar la zona
        if ($reservasModel->eliminarZona($zona_id)) {
            $_SESSION['response'] = "Zona '{$zona['zona_nombre']}' eliminada exitosamente.";
            $_SESSION['response_type'] = 'success';
        } else {
            $_SESSION['response'] = "Error: No se pudo eliminar la zona.";
            $_SESSION['response_type'] = 'danger';
        }
        
    } catch (Exception $e) {
        $_SESSION['response'] = "Error interno: " . $e->getMessage();
        $_SESSION['response_type'] = 'danger';
    }
    
    header('Location: ../views/zonas.php');
    exit;
}

function actualizarZona() {
    global $reservasModel;
    
    try {
        // Verificar permisos de administrador
        if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
            $_SESSION['response'] = "Error: No tiene permisos para editar zonas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        // Validar que se haya enviado el ID de la zona
        if (!isset($_POST['zona_id']) || empty($_POST['zona_id'])) {
            $_SESSION['response'] = "Error: ID de zona no válido.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        $zona_id = (int)$_POST['zona_id'];
        
        // Verificar que la zona existe
        $zonaExistente = $reservasModel->obtenerZonaPorId($zona_id);
        if (!$zonaExistente) {
            $_SESSION['response'] = "Error: La zona no existe.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/zonas.php');
            exit;
        }
        
        // Validar campos requeridos
        $camposRequeridos = ['zona_nombre', 'zona_capacidad', 'zona_hora_apertura', 'zona_hora_cierre', 'zona_duracion_maxima', 'zona_estado'];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                $_SESSION['response'] = "Error: El campo {$campo} es obligatorio.";
                $_SESSION['response_type'] = 'danger';
                header('Location: ../views/editarZona.php?id=' . $zona_id);
                exit;
            }
        }
        
        // Obtener datos del formulario
        $zona_nombre = trim($_POST['zona_nombre']);
        $zona_descripcion = trim($_POST['zona_descripcion'] ?? '');
        $zona_capacidad = (int)$_POST['zona_capacidad'];
        $zona_estado = $_POST['zona_estado'];
        $zona_hora_apertura = $_POST['zona_hora_apertura'];
        $zona_hora_cierre = $_POST['zona_hora_cierre'];
        $zona_duracion_maxima = (int)$_POST['zona_duracion_maxima'];
        $zona_terminos_condiciones = trim($_POST['zona_terminos_condiciones'] ?? '');
        
        // Validaciones
        if ($zona_hora_apertura >= $zona_hora_cierre) {
            $_SESSION['response'] = "Error: La hora de apertura debe ser anterior a la hora de cierre.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/editarZona.php?id=' . $zona_id);
            exit;
        }
        
        if ($zona_capacidad < 1 || $zona_capacidad > 500) {
            $_SESSION['response'] = "Error: La capacidad debe estar entre 1 y 500 personas.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/editarZona.php?id=' . $zona_id);
            exit;
        }
        
        // Manejo de imagen (opcional)
        $zona_imagen = $zonaExistente['zona_imagen']; // Mantener imagen actual por defecto
        
        if (isset($_FILES['zona_imagen']) && $_FILES['zona_imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio = '../uploads/zonas/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $extension = pathinfo($_FILES['zona_imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('zona_') . '.' . $extension;
            $rutaDestino = $directorio . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['zona_imagen']['tmp_name'], $rutaDestino)) {
                // Eliminar imagen anterior si existe
                if (!empty($zonaExistente['zona_imagen']) && file_exists('../' . $zonaExistente['zona_imagen'])) {
                    unlink('../' . $zonaExistente['zona_imagen']);
                }
                $zona_imagen = 'uploads/zonas/' . $nombreArchivo;
            }
        }
        
        // Actualizar la zona
        $actualizado = $reservasModel->actualizarZona(
            $zona_id,
            $zona_nombre,
            $zona_descripcion,
            $zona_capacidad,
            $zona_estado,
            $zona_imagen,
            $zona_hora_apertura,
            $zona_hora_cierre,
            $zona_duracion_maxima,
            $zona_terminos_condiciones
        );
        
        if ($actualizado) {
            $_SESSION['response'] = "Zona '{$zona_nombre}' actualizada exitosamente.";
            $_SESSION['response_type'] = 'success';
            header('Location: ../views/zonas.php');
        } else {
            $_SESSION['response'] = "Error: No se pudo actualizar la zona. Intente nuevamente.";
            $_SESSION['response_type'] = 'danger';
            header('Location: ../views/editarZona.php?id=' . $zona_id);
        }
        
    } catch (Exception $e) {
        $_SESSION['response'] = "Error interno: " . $e->getMessage();
        $_SESSION['response_type'] = 'danger';
        header('Location: ../views/editarZona.php?id=' . $zona_id);
    }
    
    exit;
}

function buscarUsuario() {
    global $reservasModel;
    
    header('Content-Type: application/json');
    
    try {
        $cedula = $_GET['cedula'] ?? '';
        
        if (empty($cedula)) {
            echo json_encode(['success' => false, 'message' => 'Cédula requerida']);
            return;
        }
        
        $usuario = $reservasModel->buscarUsuarioPorDocumento($cedula);
        
        if ($usuario) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'nombre' => $usuario['usu_nombre_completo'],
                    'apartamento' => $usuario['usu_apartamento_residencia'],
                    'telefono' => $usuario['usu_telefono']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Usuario no encontrado'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error del servidor'
        ]);
    }
}
?>