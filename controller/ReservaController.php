<?php
require_once __DIR__ . '/../models/ReservaModel.php';

class ReservaController {
    private $model;

    public function __construct() {
        $this->model = new ReservaModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listarReservas() {
        return $this->model->obtenerTodas();
    }

    public function obtenerReserva($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID debe ser numérico");
        }
        return $this->model->obtenerPorId($id);
    }

    public function crearReserva($datos) {
        try {
            $required = ['zona_id', 'apartamento', 'nombre_residente', 'fecha_reserva', 'hora_inicio', 'hora_fin'];
            foreach ($required as $field) {
                if (empty($datos[$field])) {
                    throw new InvalidArgumentException("El campo $field es requerido");
                }
            }

            // Validación de fechas
            $fechaActual = new DateTime();
            $fechaReserva = new DateTime($datos['fecha_reserva']);
            
            if ($fechaReserva < $fechaActual) {
                throw new InvalidArgumentException("La fecha de reserva no puede ser anterior a hoy");
            }

            if ($datos['hora_inicio'] >= $datos['hora_fin']) {
                throw new InvalidArgumentException("La hora de fin debe ser posterior a la hora de inicio");
            }

            $resultado = $this->model->crear($datos);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "✅ Reserva creada exitosamente";
                return true;
            } else {
                throw new Exception("Error al guardar en la base de datos");
            }
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "❌ " . $e->getMessage();
            return false;
        }
    }

    public function actualizarReserva($id, $datos) {
        try {
            if (!is_numeric($id)) {
                throw new InvalidArgumentException("ID debe ser numérico");
            }
            
            $resultado = $this->model->actualizar($id, $datos);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "✅ Reserva actualizada exitosamente";
                return true;
            } else {
                throw new Exception("Error al actualizar la reserva");
            }
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "❌ " . $e->getMessage();
            return false;
        }
    }

    public function eliminarReserva($id) {
        try {
            if (!is_numeric($id)) {
                throw new InvalidArgumentException("ID debe ser numérico");
            }
            
            $resultado = $this->model->eliminar($id);
            
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "✅ Reserva eliminada exitosamente";
                return true;
            } else {
                throw new Exception("Error al eliminar la reserva");
            }
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "❌ " . $e->getMessage();
            return false;
        }
    }
}
?>