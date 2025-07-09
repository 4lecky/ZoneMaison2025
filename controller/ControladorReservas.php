<?php
require_once __DIR__ . '/../config/db.php'; // Incluir la conexión a la base de datos
require_once '../models/ModeloReserva.php';

class ControladorReservas {
    public function crearReserva() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_usuario = $_POST['nombre_usuario'];
            $numero_apartamento = $_POST['numero_apartamento'];
            $fecha_fin = $_POST['fecha_fin'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $area_id = $_POST['area_id'];
            $usuario_cc = $_POST['usuario_cc'];

            $modeloReserva = new ModeloReserva();
            $modeloReserva->crear($nombre_usuario, $numero_apartamento, $fecha_fin, $hora_inicio, $hora_fin, $area_id, $usuario_cc);

            // Redireccionar a la vista de confirmación o mostrar mensaje
            header('Location: /vistas/VistaReserva.php');
        }
    }
}