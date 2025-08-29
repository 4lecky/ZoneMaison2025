<?php
// controllers/ReservaController.php
// Controlador oficial de Reservas para ZoneMaisons (BD: zonemaisons)

declare(strict_types=1);

// Asegurar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargas defensivas si no vienen por bootstrap/autoload
if (!function_exists('getConnection')) {
    $dbPath = dirname(__DIR__) . '/config/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

$rmPath = dirname(__DIR__) . '/models/ReservaModel.php';
if (file_exists($rmPath) && !class_exists('ReservaModel')) {
    require_once $rmPath;
}
$zmPath = dirname(__DIR__) . '/models/ZonaModel.php';
if (file_exists($zmPath) && !class_exists('ZonaModel')) {
    require_once $zmPath;
}

/**
 * ReservaController
 */
class ReservaController
{
    private ReservaModel $reservaModel;
    private ?ZonaModel $zonaModel = null;

    public function __construct()
    {
        try {
            $this->reservaModel = new ReservaModel();
            if (class_exists('ZonaModel')) {
                $this->zonaModel = new ZonaModel();
            }
        } catch (\Throwable $e) {
            error_log("ReservaController::__construct => " . $e->getMessage());
            throw new \Exception("No se pudo inicializar el controlador de reservas");
        }
    }

    /* ============================================================
     * Helpers de Flash
     * ============================================================ */
    public function setFlash(string $type, string $message): void
    {
        $_SESSION['mensaje'] = $message;
        $_SESSION['tipo_mensaje'] = $type; // success | error | warning | info
    }

    /** Devuelve y limpia todos los mensajes flash (array por tipo) */
    public function takeFlash(): array
    {
        $out = [];
        $types = ['success', 'error', 'warning', 'info'];

        foreach ($types as $t) {
            $k = "{$t}_message";
            if (isset($_SESSION[$k])) {
                $out[$t] = $_SESSION[$k];
                unset($_SESSION[$k]);
            }
        }

        if (isset($_SESSION['mensaje'])) {
            $tipo = $_SESSION['tipo_mensaje'] ?? 'info';
            $out[$tipo] = $_SESSION['mensaje'];
            unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        }
        return $out;
    }

    /* ============================================================
     * Lecturas
     * ============================================================ */
    public function obtenerTodasLasReservas(): array
    {
        try {
            return $this->reservaModel->obtenerTodas();
        } catch (\Throwable $e) {
            error_log("ReservaController::obtenerTodasLasReservas => " . $e->getMessage());
            throw new \Exception("Error al obtener las reservas");
        }
    }

    public function obtenerReservasPorEstado(string $estado): array
    {
        try {
            return $this->reservaModel->obtenerPorEstado($estado);
        } catch (\Throwable $e) {
            error_log("ReservaController::obtenerReservasPorEstado => " . $e->getMessage());
            throw new \Exception("Error al obtener reservas por estado");
        }
    }

    public function obtenerReservaPorId($id): ?array
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new \Exception("ID de reserva inválido");
            }
            return $this->reservaModel->obtenerPorId((int)$id);
        } catch (\Throwable $e) {
            error_log("ReservaController::obtenerReservaPorId => " . $e->getMessage());
            throw new \Exception("Error al obtener la reserva");
        }
    }

    /** Listar por documento (cédula). Usado en GestionarReserva.php */
    public function listarPorDocumento(string $cedula): array
    {
        try {
            $cedula = trim($cedula);
            if ($cedula === '' || !preg_match('/^\d{5,20}$/', $cedula)) {
                throw new \Exception("Documento inválido");
            }

            // Si el modelo expone obtenerPorDocumento, úsalo; si no, fallback por email/teléfono/apto
            if (method_exists($this->reservaModel, 'obtenerPorDocumento')) {
                return $this->reservaModel->obtenerPorDocumento($cedula);
            }

            // Fallback (si tu modelo no lo tiene, ajusta a tu esquema)
            // Aquí retornamos por coincidencia en campo 'cedula'
            if (method_exists($this->reservaModel, 'obtenerPorCampo')) {
                return $this->reservaModel->obtenerPorCampo('cedula', $cedula);
            }

            // Último recurso: no implementado
            throw new \Exception("El modelo no implementa búsqueda por documento");
        } catch (\Throwable $e) {
            error_log("ReservaController::listarPorDocumento => " . $e->getMessage());
            throw new \Exception("Error al listar reservas por documento");
        }
    }

    /* ============================================================
     * Escrituras
     * ============================================================ */
    public function crearReserva(array $datos)
    {
        try {
            $errores = $this->validarDatosReserva($datos, false);
            if (!empty($errores)) {
                throw new \Exception("Datos inválidos: " . implode(", ", $errores));
            }

            // Verificar zona (opcional pero recomendado)
            $zonaId = (int)$datos['zona_id'];
            if ($this->zonaModel) {
                $zona = $this->zonaModel->obtenerPorId($zonaId);
                if (!$zona || (isset($zona['activo']) && (int)$zona['activo'] !== 1)) {
                    throw new \Exception("La zona seleccionada no es válida o no está activa");
                }
            }

            // Disponibilidad
            $disp = $this->reservaModel->verificarDisponibilidad(
                $zonaId,
                (string)$datos['fecha_reserva'],
                (string)$datos['hora_inicio'],
                (string)$datos['hora_fin'],
                null
            );
            if (!$disp) {
                throw new \Exception("La zona no está disponible en esa fecha y horario");
            }

            // Crear
            return $this->reservaModel->crear($datos);
        } catch (\Throwable $e) {
            error_log("ReservaController::crearReserva => " . $e->getMessage());
            throw new \Exception("Error al crear la reserva: " . $e->getMessage());
        }
    }

    public function actualizarReserva($id, array $datos): bool
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new \Exception("ID de reserva inválido");
            }

            $errores = $this->validarDatosReserva($datos, true);
            if (!empty($errores)) {
                throw new \Exception("Datos inválidos: " . implode(", ", $errores));
            }

            $existente = $this->reservaModel->obtenerPorId((int)$id);
            if (!$existente) {
                throw new \Exception("La reserva no existe");
            }

            // Verificar zona
            $zonaId = (int)$datos['zona_id'];
            if ($this->zonaModel) {
                $zona = $this->zonaModel->obtenerPorId($zonaId);
                if (!$zona || (isset($zona['activo']) && (int)$zona['activo'] !== 1)) {
                    throw new \Exception("La zona seleccionada no es válida o no está activa");
                }
            }

            // Disponibilidad excluyéndose a sí misma
            $disp = $this->reservaModel->verificarDisponibilidad(
                $zonaId,
                (string)$datos['fecha_reserva'],
                (string)$datos['hora_inicio'],
                (string)$datos['hora_fin'],
                (int)$id
            );
            if (!$disp) {
                throw new \Exception("La zona no está disponible en esa fecha y horario");
            }

            return $this->reservaModel->actualizar((int)$id, $datos);
        } catch (\Throwable $e) {
            error_log("ReservaController::actualizarReserva => " . $e->getMessage());
            throw new \Exception("Error al actualizar la reserva: " . $e->getMessage());
        }
    }

    public function eliminarReserva($id): bool
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new \Exception("ID de reserva inválido");
            }
            return $this->reservaModel->eliminar((int)$id);
        } catch (\Throwable $e) {
            error_log("ReservaController::eliminarReserva => " . $e->getMessage());
            throw new \Exception("Error al eliminar la reserva: " . $e->getMessage());
        }
    }

    public function cambiarEstadoReserva($id, string $estado): bool
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new \Exception("ID de reserva inválido");
            }

            $validos = ['pendiente', 'confirmada', 'cancelada', 'completada'];
            if (!in_array($estado, $validos, true)) {
                throw new \Exception("Estado inválido");
            }

            return $this->reservaModel->cambiarEstado((int)$id, $estado);
        } catch (\Throwable $e) {
            error_log("ReservaController::cambiarEstadoReserva => " . $e->getMessage());
            throw new \Exception("Error al cambiar estado de la reserva: " . $e->getMessage());
        }
    }

    /* ============================================================
     * Estadísticas
     * ============================================================ */
    public function obtenerEstadisticas(): array
    {
        try {
            return [
                'total'       => $this->reservaModel->contarTotal(),
                'pendientes'  => $this->reservaModel->contarPorEstado('pendiente'),
                'confirmadas' => $this->reservaModel->contarPorEstado('confirmada'),
                'canceladas'  => $this->reservaModel->contarPorEstado('cancelada'),
                'completadas' => $this->reservaModel->contarPorEstado('completada'),
                'hoy'         => $this->reservaModel->contarPorFecha(date('Y-m-d')),
                'proximas'    => $this->reservaModel->contarProximas(),
            ];
        } catch (\Throwable $e) {
            error_log("ReservaController::obtenerEstadisticas => " . $e->getMessage());
            return [
                'total'       => 0,
                'pendientes'  => 0,
                'confirmadas' => 0,
                'canceladas'  => 0,
                'completadas' => 0,
                'hoy'         => 0,
                'proximas'    => 0,
            ];
        }
    }

    /* ============================================================
     * Manejo de solicitudes POST genéricas (CRUD)
     * ============================================================ */
    public function manejarSolicitud(): void
    {
        try {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                return;
            }

            $accion = $_POST['accion'] ?? '';

            switch ($accion) {
                case 'crear':
                    $nuevoId = $this->crearReserva($_POST);
                    if ($nuevoId) {
                        $this->setFlash('success', 'Reserva creada exitosamente');
                    }
                    break;

                case 'actualizar':
                    $id = (int)($_POST['id'] ?? 0);
                    $data = $_POST;
                    unset($data['accion'], $data['id']);
                    if ($this->actualizarReserva($id, $data)) {
                        $this->setFlash('success', 'Reserva actualizada exitosamente');
                    }
                    break;

                case 'eliminar':
                    $id = (int)($_POST['id'] ?? 0);
                    if ($this->eliminarReserva($id)) {
                        $this->setFlash('success', 'Reserva eliminada exitosamente');
                    }
                    break;

                case 'cambiar_estado':
                    $id     = (int)($_POST['id'] ?? 0);
                    $estado = (string)($_POST['estado'] ?? '');
                    if ($this->cambiarEstadoReserva($id, $estado)) {
                        $this->setFlash('success', 'Estado de reserva actualizado');
                    }
                    break;

                default:
                    throw new \Exception('Acción no válida');
            }
        } catch (\Throwable $e) {
            $this->setFlash('error', $e->getMessage());
        }
    }

    /* ============================================================
     * Validación
     * ============================================================ */
    /**
     * @param array $datos Data del formulario
     * @param bool  $esEdicion Si es edición (true) o creación (false) para reglas sutiles
     * @return array lista de errores
     */
    private function validarDatosReserva(array $datos, bool $esEdicion): array
    {
        $errores = [];

        // Nombre
        $nombre = trim((string)($datos['nombre_usuario'] ?? ''));
        if ($nombre === '' || mb_strlen($nombre) < 2) {
            $errores[] = "El nombre de usuario es requerido";
        }

        // Apartamento (texto simple)
        $apto = trim((string)($datos['apartamento'] ?? ''));
        if ($apto === '') {
            $errores[] = "El apartamento es requerido";
        }

        // Teléfono (básico)
        $tel = trim((string)($datos['telefono'] ?? ''));
        if ($tel === '' || mb_strlen(preg_replace('/\D/', '', $tel)) < 7) {
            $errores[] = "El teléfono es requerido";
        }

        // Email
        $email = trim((string)($datos['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Email válido es requerido";
        }

        // Cédula (si tu tabla la incluye; muchos de tus formularios lo usan)
        if (isset($datos['cedula'])) {
            $ced = trim((string)$datos['cedula']);
            if ($ced === '' || !preg_match('/^\d{5,20}$/', $ced)) {
                $errores[] = "La cédula debe tener entre 5 y 20 dígitos";
            }
        }

        // Zona
        if (!isset($datos['zona_id']) || !is_numeric($datos['zona_id'])) {
            $errores[] = "Debe seleccionar una zona válida";
        }

        // Fecha
        $fecha = $datos['fecha_reserva'] ?? '';
        if ($fecha === '') {
            $errores[] = "La fecha de reserva es requerida";
        } else {
            $hoy = date('Y-m-d');
            if ($fecha < $hoy) {
                $errores[] = "La fecha de reserva no puede ser anterior a hoy";
            }
        }

        // Horas
        $hi = $datos['hora_inicio'] ?? '';
        $hf = $datos['hora_fin'] ?? '';
        if ($hi === '' || $hf === '') {
            $errores[] = "Las horas de inicio y fin son requeridas";
        } elseif ($hi >= $hf) {
            $errores[] = "La hora final debe ser posterior a la inicial";
        }

        // Personas
        $num = $datos['numero_personas'] ?? null;
        if (!is_numeric($num) || (int)$num < 1) {
            $errores[] = "El número de personas debe ser mayor a 0";
        }

        // Estado (opcional en creación)
        if (isset($datos['estado'])) {
            $validos = ['pendiente', 'confirmada', 'cancelada', 'completada'];
            if (!in_array((string)$datos['estado'], $validos, true)) {
                $errores[] = "Estado inválido";
            }
        }

        return $errores;
    }
}
