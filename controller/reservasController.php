public function getReservasPorZona() {
    try {
        // Configurar cabeceras para JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        
        // Verificar si se envió el parámetro zona_id
        if (isset($_GET['zona_id']) && !empty($_GET['zona_id'])) {
            $zona_id = (int)$_GET['zona_id']; // Convertir a entero para seguridad
            
            // Usar el modelo para obtener las reservas
            require_once "../models/reservasModel.php";
            $modeloReservas = new ReservasModel();
            
            $reservas = $modeloReservas->obtenerReservasPorZona($zona_id);
            
            // Formatear datos para el calendario JavaScript
            $reservasFormateadas = [];
            
            foreach ($reservas as $reserva) {
                $reservasFormateadas[] = [
                    'fecha' => $reserva['reserva_fecha'],
                    'hora_inicio' => substr($reserva['reserva_hora_inicio'], 0, 5), // Formato HH:MM
                    'hora_fin' => substr($reserva['reserva_hora_fin'], 0, 5),       // Formato HH:MM
                    'residente' => $reserva['reserva_nombre_residente'],
                    'apartamento' => $reserva['reserva_apartamento']
                ];
            }
            
            // Enviar respuesta JSON
            echo json_encode([
                'success' => true,
                'data' => $reservasFormateadas,
                'total' => count($reservasFormateadas)
            ]);
            
        } else {
            // Si no se envió zona_id, devolver array vacío
            echo json_encode([
                'success' => true,
                'data' => [],
                'total' => 0,
                'message' => 'No se especificó zona_id'
            ]);
        }
        
    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error de base de datos',
            'message' => $e->getMessage()
        ]);
        
    } catch (Exception $e) {
        // Manejo de otros errores
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error del servidor',
            'message' => $e->getMessage()
        ]);
    }
}