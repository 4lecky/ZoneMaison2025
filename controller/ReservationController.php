<?php
// app/Controllers/ReservationController.php

require_once '../Models/ReservationModel.php';
require_once '../config/database.php';

class ReservationController {
    private $reservationModel;
    
    public function __construct() {
        $database = new Database();
        $this->reservationModel = new ReservationModel($database->getConnection());
    }
    
    /**
     * Muestra la vista del salón comunal
     */
    public function showSalonComunal() {
        $pageTitle = "Salón Comunal - ZONEMAISONS";
        $pageDescription = "Reserva el salón comunal para tus eventos y reuniones";
        
        // Datos para la vista
        $viewData = [
            'pageTitle' => $pageTitle,
            'salonCapacity' => 55,
            'availableHours' => '8:00 AM - 10:00 PM',
            'maxDuration' => 5,
            'minAdvanceHours' => 48
        ];
        
        // Cargar la vista
        include '../Views/areas-comunes/salon-comunal.php';
    }
    
    /**
     * API para obtener reservas existentes
     */
    public function getReservations() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        
        try {
            // Obtener parámetros
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-01', strtotime('+2 months'));
            $areaId = $_GET['area_id'] ?? 1;
            
            // Validar fechas
            if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
                throw new Exception("Formato de fecha inválido");
            }
            
            $reservations = $this->reservationModel->getReservationsByDateRange($startDate, $endDate, $areaId);
            
            // Formatear respuesta para el frontend
            $formattedReservations = array_map(function($reservation) {
                return [
                    'id' => $reservation['rese_id'],
                    'date' => $reservation['rese_fecha_inicio'],
                    'start_time' => substr($reservation['rese_hora_inicio'], 0, 5),
                    'end_time' => substr($reservation['rese_hora_fin'], 0, 5),
                    'user_name' => $reservation['rese_usuario_nombre'],
                    'apartment' => $reservation['rese_numero_apartamento']
                ];
            }, $reservations);
            
            $this->sendJsonResponse([
                'success' => true,
                'data' => $formattedReservations
            ]);
            
        } catch(Exception $e) {
            $this->sendJsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * API para crear una nueva reserva
     */
    public function createReservation() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Verificar que sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            // Obtener datos JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON inválido");
            }
            
            // Validar y limpiar datos
            $reservationData = $this->validateAndCleanReservationData($input);
            
            // Crear reserva
            $result = $this->reservationModel->createReservation($reservationData);
            
            $this->sendJsonResponse($result);
            
        } catch(Exception $e) {
            $this->sendJsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * API para verificar disponibilidad de un horario específico
     */
    public function checkAvailability() {
        header('Content-Type: application/json');
        
        try {
            $date = $_GET['date'] ?? '';
            $startTime = $_GET['start_time'] ?? '';
            $endTime = $_GET['end_time'] ?? '';
            $areaId = $_GET['area_id'] ?? 1;
            
            if (empty($date) || empty($startTime) || empty($endTime)) {
                throw new Exception("Parámetros faltantes");
            }
            
            $isAvailable = $this->reservationModel->isTimeSlotAvailable($date, $startTime, $endTime, $areaId);
            
            $this->sendJsonResponse([
                'success' => true,
                'available' => $isAvailable
            ]);
            
        } catch(Exception $e) {
            $this->sendJsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * API para cancelar una reserva
     */
    public function cancelReservation() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: DELETE');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception("Método no permitido");
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $reservationId = $input['reservation_id'] ?? '';
            $userId = $input['user_id'] ?? '';
            
            if (empty($reservationId) || empty($userId)) {
                throw new Exception("Parámetros faltantes");
            }
            
            $result = $this->reservationModel->cancelReservation($reservationId, $userId);
            
            $this->sendJsonResponse($result);
            
        } catch(Exception $e) {
            $this->sendJsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Valida y limpia los datos de reserva
     */
    private function validateAndCleanReservationData($input) {
        $data = [];
        
        // Limpiar y validar nombre
        $data['nombre'] = trim($input['nombre'] ?? '');
        if (empty($data['nombre']) || strlen($data['nombre']) < 3) {
            throw new Exception("El nombre debe tener al menos 3 caracteres");
        }
        
        // Limpiar y validar apartamento
        $data['apartamento'] = trim($input['apartamento'] ?? '');
        if (empty($data['apartamento'])) {
            throw new Exception("El número de apartamento es requerido");
        }
        
        // Limpiar y validar documento
        $data['documento'] = trim($input['documento'] ?? '');
        if (empty($data['documento']) || !is_numeric($data['documento'])) {
            throw new Exception("El número de documento debe ser numérico");
        }
        
        // Validar fecha
        $data['fecha'] = $input['fecha'] ?? '';
        if (!$this->isValidDate($data['fecha'])) {
            throw new Exception("Formato de fecha inválido");
        }
        
        // Validar horarios
        $data['hora_inicio'] = $input['hora_inicio'] ?? '';
        $data['hora_fin'] = $input['hora_fin'] ?? '';
        
        if (!$this->isValidTime($data['hora_inicio']) || !$this->isValidTime($data['hora_fin'])) {
            throw new Exception("Formato de hora inválido");
        }
        
        // Área ID
        $data['area_id'] = intval($input['area_id'] ?? 1);
        
        return $data;
    }
    
    /**
     * Valida formato de fecha
     */
    private function isValidDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Valida formato de hora
     */
    private function isValidTime($time) {
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $time);
    }
    
    /**
     * Envía respuesta JSON
     */
    private function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Maneja las rutas de la API
     */
    public function handleApiRequest() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Rutas de la API
        switch ($requestUri) {
            case '/api/reservations':
                if ($requestMethod === 'GET') {
                    $this->getReservations();
                } elseif ($requestMethod === 'POST') {
                    $this->createReservation();
                } elseif ($requestMethod === 'DELETE') {
                    $this->cancelReservation();
                } else {
                    $this->sendJsonResponse(['error' => 'Método no permitido'], 405);
                }
                break;
                
            case '/api/availability':
                if ($requestMethod === 'GET') {
                    $this->checkAvailability();
                } else {
                    $this->sendJsonResponse(['error' => 'Método no permitido'], 405);
                }
                break;
                
            default:
                $this->sendJsonResponse(['error' => 'Endpoint no encontrado'], 404);
                break;
        }
    }
}