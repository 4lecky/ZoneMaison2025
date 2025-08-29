<?php
// bootstrap.php – Inicializador global para ModuloReservas

// =======================
// 1. Configuración básica
// =======================

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================
// 2. Rutas base del sistema
// =======================

// Ruta raíz del proyecto (ajústala si tu index está en otro sitio)
define('PROJECT_ROOT', dirname(__DIR__, 3)); // .../ZoneMaison2025
define('VIEWS_ROOT', PROJECT_ROOT . '/views');
define('CONFIG_ROOT', PROJECT_ROOT . '/config');
define('MODELS_ROOT', PROJECT_ROOT . '/models');
define('CONTROLLERS_ROOT', PROJECT_ROOT . '/controller');

// =======================
// 3. Autoload de clases básicas
// =======================

// Cargar configuración DB
require_once CONFIG_ROOT . '/db.php';

// Cargar modelos y controladores que siempre se usan
require_once MODELS_ROOT . '/ZonaModel.php';
require_once MODELS_ROOT . '/ReservaModel.php';
require_once CONTROLLERS_ROOT . '/ZonaController.php';
require_once CONTROLLERS_ROOT . '/ReservaController.php';

// =======================
// 4. Helpers de layout
// =======================

if (!function_exists('layout')) {
    /**
     * Incluir un archivo de layout desde /views/Layout/
     * Ejemplo: layout('header.php'); layout('footer.php');
     */
    function layout(string $file) {
        $path = VIEWS_ROOT . '/Layout/' . ltrim($file, '/');
        if (file_exists($path)) {
            include $path;
        } else {
            trigger_error("⚠️ Layout no encontrado: $path", E_USER_WARNING);
        }
    }
}

// =======================
// 5. Flash messages helper
// =======================
if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('get_flash')) {
    function get_flash() {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

// =======================
// 6. Debug helper (solo dev)
// =======================
if (!function_exists('dd')) {
    function dd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
        exit;
    }
}
