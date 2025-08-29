<?php
/**
 * controller/ZonaController.php
 * Controlador para Zonas Comunes
 *
 * Requisitos:
 *  - getConnection() definido en config/db.php (incluido por tu bootstrap)
 *  - models/ZonaModel.php disponible y cargable
 *  - Sesión iniciada (el bootstrap ya la inicia)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ========= RUTAS BÁSICAS (compatibles con tu estructura) ========= */
if (!defined('PROJECT_ROOT')) {
    // /controller -> subimos 1 nivel
    define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
}
if (!defined('MODELS_PATH')) {
    define('MODELS_PATH', PROJECT_ROOT . '/models');
}

/* ========= HELPERS DE FLASH (fallback si no existen) ========= */
if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void {
        $_SESSION['mensaje'] = $message;
        // Acepta: success, info, warning, danger (bootstrap) o 'error'
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
        // Traer conexión del bootstrap/config
        if (!function_exists('getConnection')) {
            // Intento de carga defensivo
            $dbPath = PROJECT_ROOT . '/config/db.php';
            if (file_exists($dbPath)) {
                require_once $dbPath;
            }
        }

        if (!function_exists('getConnection')) {
            throw new Exception('No se encontró la función getConnection(). Verifica config/db.php y bootstrap.php');
        }

        try {
            $this->pdo   = getConnection();
            $this->model = new ZonaModel($this->pdo);
        } catch (Throwable $e) {
            throw new Exception('No se pudo inicializar el controlador de zonas: ' . $e->getMessage());
        }
    }

    /* ==========================================================
     *                 CONSULTAS / LISTADOS
     * ========================================================== */

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

    /**
     * Estadísticas simples de zonas (totales, activas, inactivas, con reservas)
     */
    public function obtenerEstadisticas(): array
    {
        // Si el modelo tiene un método específico, úsalo.
        if (method_exists($this->model, 'obtenerEstadisticas')) {
            return $this->model->obtenerEstadisticas();
        }

        // Fallback mediante SQL directo
        $stats = [
            'total'      => 0,
            'activas'    => 0,
            'inactivas'  => 0,
            'con_reservas' => 0,
        ];

        try {
            $stats['total'] = (int)$this->pdo->query("SELECT COUNT(*) FROM zonas_comunes")->fetchColumn();
            $stats['activas'] = (int)$this->pdo->query("SELECT COUNT(*) FROM zonas_comunes WHERE activo = 1")->fetchColumn();
            $stats['inactivas'] = (int)$this->pdo->query("SELECT COUNT(*) FROM zonas_comunes WHERE activo = 0")->fetchColumn();
            $stats['con_reservas'] = (int)$this->pdo->query("
                SELECT COUNT(DISTINCT z.id)
                FROM zonas_comunes z
                JOIN reservas r ON r.zona_id = z.id
            ")->fetchColumn();
        } catch (Throwable $e) {
            // No reventamos la UI por estadísticas
        }

        return $stats;
    }

    /* ==========================================================
     *                          CRUD
     * ========================================================== */

    public function crearZona(array $data): int
    {
        $payload = $this->validarYNormalizar($data, false);
        return $this->model->crear($payload);
    }

    public function actualizarZona(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }
        $payload = $this->validarYNormalizar($data, true);
        return $this->model->actualizar($id, $payload);
    }

    public function cambiarEstado(int $id, int $activo): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }
        return $this->model->cambiarEstado($id, $activo ? 1 : 0);
    }

    public function eliminarZona(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }
        return $this->model->eliminar($id);
    }

    /* ==========================================================
     *                 MANEJO DE FORMULARIOS POST
     * ========================================================== */
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

    /* ==========================================================
     *           VALIDACIÓN / NORMALIZACIÓN DE INPUT
     * ========================================================== */

    /**
     * Valida y normaliza el payload para crear/actualizar.
     * Admite columnas opcionales si existen físicamente en la tabla:
     *  - hora_apertura, hora_cierre, duracion_maxima, terminos, imagen
     */
    private function validarYNormalizar(array $data, bool $parcial): array
    {
        // Campos base (siempre presentes en tu esquema principal)
        $nombre           = isset($data['nombre']) ? trim((string)$data['nombre']) : null;
        $descripcion      = array_key_exists('descripcion', $data) ? trim((string)$data['descripcion']) : null;
        $capacidad_maxima = array_key_exists('capacidad_maxima', $data) ? (int)$data['capacidad_maxima'] : null;
        $tarifa           = array_key_exists('tarifa', $data) ? (float)$data['tarifa'] : null;
        $activo           = array_key_exists('activo', $data) ? (int)(!!$data['activo']) : null;

        if (!$parcial) { // creación
            if ($nombre === null || $nombre === '') {
                throw new InvalidArgumentException('El nombre es obligatorio.');
            }
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

        // Columnas opcionales: solo si existen en la tabla
        $opcionales = [
            'hora_apertura'   => $data['hora_apertura']   ?? null,
            'hora_cierre'     => $data['hora_cierre']     ?? null,
            'duracion_maxima' => isset($data['duracion_maxima']) ? (int)$data['duracion_maxima'] : null,
            'terminos'        => $data['terminos']        ?? null, // en tu primer esquema se llamaba terminos_y_condiciones
            'imagen'          => $data['imagen']          ?? null,
        ];

        $columnas = $this->obtenerColumnas('zonas_comunes');
        foreach ($opcionales as $col => $val) {
            if ($val === null) continue;
            // Ajuste de nombre si tu tabla usa terminos_y_condiciones
            $map = ($col === 'terminos' && in_array('terminos_y_condiciones', $columnas, true))
                ? 'terminos_y_condiciones'
                : $col;
            if (in_array($map, $columnas, true)) {
                $payload[$map] = $val;
            }
        }

        return $payload;
    }

    /**
     * Retorna nombres de columnas de una tabla para manejar opcionales sin romper.
     */
    private function obtenerColumnas(string $tabla): array
    {
        static $cache = [];
        if (isset($cache[$tabla])) return $cache[$tabla];

        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM {$tabla}");
            $stmt->execute();
            $cols = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
                $cols[] = $col['Field'];
            }
            return $cache[$tabla] = $cols;
        } catch (Throwable $e) {
            return $cache[$tabla] = [];
        }
    }
}
