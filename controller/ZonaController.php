<?php
/**
 * controller/ZonaController.php
 * Controlador para Zonas Comunes usando PDO directo desde config/db.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ========= RUTAS ========= */
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', realpath(__DIR__ . '/..')); // sube a la raíz del proyecto
}
if (!defined('MODELS_PATH')) {
    define('MODELS_PATH', PROJECT_ROOT . '/models');
}

/* ========= INCLUIR CONEXIÓN ========= */
$dbPath = PROJECT_ROOT . '/config/db.php';
if (!file_exists($dbPath)) {
    throw new Exception("Archivo de conexión no encontrado en: {$dbPath}");
}
$pdo = require $dbPath; // ← aquí recibimos el return $pdo;

/* ========= HELPERS DE FLASH ========= */
if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void {
        $_SESSION['mensaje'] = $message;
        $_SESSION['tipo_mensaje'] = ($type === 'error') ? 'danger' : $type;
    }
}
if (!function_exists('take_flash')) {
    function take_flash(): array {
        $msg  = $_SESSION['mensaje']      ?? null;
        $type = $_SESSION['tipo_mensaje'] ?? null;
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        return [$type, $msg];
    }
}

/* ========= CARGA DEL MODELO ========= */
$zonaModelPath = MODELS_PATH . '/ZonaModel.php';
if (!file_exists($zonaModelPath)) {
    throw new Exception("ZonaModel.php no encontrado en: {$zonaModelPath}");
}
require_once $zonaModelPath;

class ZonaController
{
    /** @var PDO */
    private $pdo;

    /** @var ZonaModel */
    private $model;

    public function __construct()
    {
        global $pdo; // ← reutilizamos el $pdo del require

        if (!$pdo instanceof PDO) {
            throw new Exception('No se obtuvo una conexión PDO válida desde config/db.php');
        }

        $this->pdo   = $pdo;
        $this->model = new ZonaModel($this->pdo);
    }

    /* ==========================
     *         LISTADOS
     * ========================== */
    public function obtenerTodasLasZonas(): array
    {
        return $this->model->listarTodas();
    }

    public function obtenerZonasActivas(): array
    {
        return $this->model->listarActivas();
    }

    public function obtenerZonaPorId(int $id): ?array
    {
        if ($id <= 0) return null;
        return $this->model->buscarPorId($id);
    }

    public function obtenerEstadisticas(): array
    {
        if (method_exists($this->model, 'obtenerEstadisticas')) {
            return $this->model->obtenerEstadisticas();
        }

        return [
            'total'        => 0,
            'activas'      => 0,
            'inactivas'    => 0,
            'con_reservas' => 0,
        ];
    }

    /* ==========================
     *          CRUD
     * ========================== */
    public function crearZona(array $data): int
    {
        $payload = $this->validarYNormalizar($data, false);
        return $this->model->crear($payload);
    }

    public function actualizarZona(int $id, array $data): bool
    {
        if ($id <= 0) throw new InvalidArgumentException('ID inválido.');
        $payload = $this->validarYNormalizar($data, true);
        return $this->model->actualizar($id, $payload);
    }

    public function cambiarEstado(int $id, int $activo): bool
    {
        if ($id <= 0) throw new InvalidArgumentException('ID inválido.');
        return $this->model->cambiarEstado($id, $activo ? 1 : 0);
    }

    public function eliminarZona(int $id): bool
    {
        if ($id <= 0) throw new InvalidArgumentException('ID inválido.');
        return $this->model->eliminar($id);
    }

    /* ==========================
     *       MANEJO POST
     * ========================== */
    public function manejarSolicitud(): void
    {
        $accion = $_POST['accion'] ?? '';

        try {
            switch ($accion) {
                case 'crear':
                    $id = $this->crearZona($_POST);
                    set_flash('success', 'Zona creada (#' . $id . ').');
                    break;

                case 'actualizar':
                    $id = (int)($_POST['id'] ?? 0);
                    $ok = $this->actualizarZona($id, $_POST);
                    set_flash($ok ? 'success' : 'danger', $ok ? 'Zona actualizada.' : 'No se pudo actualizar la zona.');
                    break;

                case 'cambiar_estado':
                    $id     = (int)($_POST['id'] ?? 0);
                    $activo = (int)($_POST['activo'] ?? 0);
                    $ok     = $this->cambiarEstado($id, $activo);
                    set_flash($ok ? 'success' : 'danger', $ok ? 'Estado actualizado.' : 'No se pudo actualizar el estado.');
                    break;

                case 'eliminar':
                    $id = (int)($_POST['id'] ?? 0);
                    $ok = $this->eliminarZona($id);
                    set_flash($ok ? 'success' : 'danger', $ok ? 'Zona eliminada.' : 'No se pudo eliminar la zona.');
                    break;

                default:
                    set_flash('warning', 'Acción no reconocida.');
            }
        } catch (Throwable $e) {
            set_flash('danger', 'Error: ' . $e->getMessage());
        }
    }

    /* ==========================
     *   VALIDACIÓN INPUT
     * ========================== */
    private function validarYNormalizar(array $data, bool $parcial): array
    {
        $nombre           = isset($data['nombre']) ? trim((string)$data['nombre']) : null;
        $descripcion      = $data['descripcion']      ?? null;
        $capacidad_maxima = isset($data['capacidad_maxima']) ? (int)$data['capacidad_maxima'] : null;
        $tarifa           = isset($data['tarifa']) ? (float)$data['tarifa'] : null;
        $activo           = isset($data['activo']) ? (int)(!!$data['activo']) : null;

        if (!$parcial) {
            if (!$nombre) throw new InvalidArgumentException('El nombre es obligatorio.');
            if ($capacidad_maxima === null || $capacidad_maxima < 1) {
                throw new InvalidArgumentException('La capacidad debe ser mayor o igual a 1.');
            }
            if ($tarifa === null || $tarifa < 0) {
                throw new InvalidArgumentException('La tarifa no puede ser negativa.');
            }
            if ($activo === null) $activo = 1;
        }

        $payload = [];
        if ($nombre !== null)           $payload['nombre'] = $nombre;
        if ($descripcion !== null)      $payload['descripcion'] = $descripcion;
        if ($capacidad_maxima !== null) $payload['capacidad_maxima'] = $capacidad_maxima;
        if ($tarifa !== null)           $payload['tarifa'] = $tarifa;
        if ($activo !== null)           $payload['activo'] = $activo;

        return $payload;
    }
}
