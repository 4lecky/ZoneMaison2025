<?php
// controllers/ReservaController.php
// Controlador oficial de Reservas para ZoneMaisons (BD: zonemaisons)

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/models/ReservaModel.php';
require_once dirname(__DIR__) . '/models/ZonaModel.php';

class ReservaController
{
    private ReservaModel $reservaModel;
    private ?ZonaModel $zonaModel = null;

    public function __construct(PDO $pdo)
    {
        try {
            $this->reservaModel = new ReservaModel($pdo);
            $this->zonaModel    = new ZonaModel($pdo);
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
        $_SESSION['mensaje']      = $message;
        $_SESSION['tipo_mensaje'] = $type; // success | error | warning | info
    }

    public function takeFlash(): array
    {
        $out   = [];
        $types = ['success', 'error', 'warning', 'info'];

        foreach ($types as $t) {
            $k = "{$t}_message";
            if (isset($_SESSION[$k])) {
                $out[$t] = $_SESSION[$k];
                unset($_SESSION[$k]);
            }
        }

        if (isset($_SESSION['mensaje'])) {
            $tipo       = $_SESSION['tipo_mensaje'] ?? 'info';
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
        return $this->reservaModel->obtenerTodas();
    }

    public function obtenerReservasPorEstado(string $estado): array
    {
        return $this->reservaModel->obtenerPorEstado($estado);
    }

    public function obtenerReservaPorId(int $id): ?array
    {
        return $this->reservaModel->obtenerPorId($id);
    }

    public function listarPorDocumento(string $cedula): array
    {
        return $this->reservaModel->obtenerPorDocumento($cedula);
    }

    /* ============================================================
     * Escrituras
     * ============================================================ */
    public function crearReserva(array $datos)
    {
        $zonaId = (int)$datos['zona_id'];

        if ($this->zonaModel) {
            $zona = $this->zonaModel->obtenerPorId($zonaId);
            if (!$zona || (isset($zona['activo']) && (int)$zona['activo'] !== 1)) {
                throw new \Exception("La zona seleccionada no es válida o no está activa");
            }
        }

        // Verificar disponibilidad
        $disp = $this->reservaModel->verificarDisponibilidad(
            $zonaId,
            (string)$datos['fecha_reserva'],
            (string)$datos['hora_inicio'],
            (string)$datos['hora_fin']
        );
        if (!$disp) {
            throw new \Exception("La zona no está disponible en esa fecha y horario");
        }

        return $this->reservaModel->crear($datos);
    }

    public function actualizarReserva(int $id, array $datos): bool
    {
        return $this->reservaModel->actualizar($id, $datos);
    }

    public function eliminarReserva(int $id): bool
    {
        return $this->reservaModel->eliminar($id);
    }

    public function cambiarEstadoReserva(int $id, string $estado): bool
    {
        return $this->reservaModel->cambiarEstado($id, $estado);
    }

    /* ============================================================
     * Estadísticas
     * ============================================================ */
    public function obtenerEstadisticas(): array
    {
        return [
            'total'       => $this->reservaModel->contarTotal(),
            'pendientes'  => $this->reservaModel->contarPorEstado('pendiente'),
            'confirmadas' => $this->reservaModel->contarPorEstado('confirmada'),
            'canceladas'  => $this->reservaModel->contarPorEstado('cancelada'),
            'completadas' => $this->reservaModel->contarPorEstado('completada'),
            'hoy'         => $this->reservaModel->contarPorFecha(date('Y-m-d')),
            'proximas'    => $this->reservaModel->contarProximas(),
        ];
    }
}
