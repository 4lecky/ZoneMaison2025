<?php
require_once __DIR__ . '/../models/ZonaComun.php';

class ZonaController {
    private $zonaModel;

    public function __construct() {
        $this->zonaModel = new ZonaComun();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $zonas = $this->zonaModel->obtenerTodas();
        include __DIR__ . '/../views/zonas/index.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'capacidad' => $_POST['capacidad'],
                'hora_apertura' => $_POST['hora_apertura'] ?? '08:00:00',
                'hora_cierre' => $_POST['hora_cierre'] ?? '20:00:00',
                'duracion_maxima' => $_POST['duracion_maxima'] ?? 2,
                'estado' => $_POST['estado'] ?? 'activo',
                'imagen' => $_FILES['imagen']['name'] ?? null
            ];

            if ($this->zonaModel->crear($datos)) {
                $_SESSION['mensaje_exito'] = '✅ Zona común creada exitosamente';

                // Manejar la subida de imagen si existe
                if (!empty($_FILES['imagen']['tmp_name'])) {
                    $this->subirImagen($_FILES['imagen'], $this->zonaModel->obtenerUltimoId());
                }

                header('Location: index.php?controller=zona&action=index');
                exit;
            } else {
                $_SESSION['mensaje_error'] = '❌ Error al crear la zona común';
            }
        }

        include __DIR__ . '/../views/zonas/crear.php';
    }

    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'capacidad' => $_POST['capacidad'],
                'hora_apertura' => $_POST['hora_apertura'],
                'hora_cierre' => $_POST['hora_cierre'],
                'duracion_maxima' => $_POST['duracion_maxima'],
                'estado' => $_POST['estado']
            ];

            if (!empty($_FILES['imagen']['tmp_name'])) {
                $datos['imagen'] = $_FILES['imagen']['name'];
                $this->subirImagen($_FILES['imagen'], $id);
            }

            if ($this->zonaModel->actualizar($id, $datos)) {
                $_SESSION['mensaje_exito'] = '✅ Zona común actualizada exitosamente';
                header('Location: index.php?controller=zona&action=index');
                exit;
            } else {
                $_SESSION['mensaje_error'] = '❌ Error al actualizar la zona común';
            }
        }

        $zona = $this->zonaModel->obtenerPorId($id);
        include __DIR__ . '/../views/zonas/editar.php';
    }

    public function eliminar($id) {
        if ($this->zonaModel->tieneReservas($id)) {
            $_SESSION['mensaje_error'] = '❌ No se puede eliminar la zona porque tiene reservas asociadas';
        } else {
            if ($this->zonaModel->eliminar($id)) {
                $_SESSION['mensaje_exito'] = '✅ Zona común eliminada exitosamente';
            } else {
                $_SESSION['mensaje_error'] = '❌ Error al eliminar la zona común';
            }
        }
        header('Location: index.php?controller=zona&action=index');
        exit;
    }

    /**
     * Lista zonas comunes (todas, no solo activas)
     * @return array
     */
    public function listarZonasComunes() {
        $zonas = $this->zonaModel->obtenerTodas(); // muestra todas, no solo las activas

        if (!is_array($zonas)) {
            $zonas = [];
        }

        foreach ($zonas as &$zona) {
            $zona['hora_apertura'] = substr($zona['hora_apertura'] ?? '08:00:00', 0, 5);
            $zona['hora_cierre'] = substr($zona['hora_cierre'] ?? '20:00:00', 0, 5);
            $zona['capacidad'] = $zona['capacidad'] ?? 10;
            $zona['duracion_maxima'] = $zona['duracion_maxima'] ?? 2;
            $zona['imagen'] = $zona['imagen'] ?? 'default.jpg';
        }

        return $zonas;
    }

    /**
     * Sube una imagen al servidor
     * @param array $imagen
     * @param int $id
     */
    private function subirImagen($imagen, $id) {
        $directorio = __DIR__ . '/../assets/img/zonas/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreArchivo = "zona_{$id}.{$extension}";
        $rutaCompleta = $directorio . $nombreArchivo;

        if (move_uploaded_file($imagen['tmp_name'], $rutaCompleta)) {
            $this->zonaModel->actualizar($id, ['imagen' => $nombreArchivo]);
        }
    }
}
?>
