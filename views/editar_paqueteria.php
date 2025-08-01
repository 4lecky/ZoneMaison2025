<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "../config/db.php";
require_once "../controller/EditPaquController.php";
require_once "./Layout/header.php";

$controller = new EditPaquController($pdo);
$mensaje = '';
$publicacion = null; // Inicializar la variable

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $resultado = $controller->eliminar($_POST['id']);
    if ($resultado['success']) {
        header("Location: novedades.php?success=" . urlencode($resultado['mensaje']));
        exit();
    } else {
        $mensaje = $resultado['mensaje'];
    }
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $resultado = $controller->actualizar($_POST);
    $mensaje = $resultado['mensaje'];
    if ($resultado['success'] && isset($resultado['data'])) {
        $publicacion = $resultado['data'];
    } else {
        // Si hubo error, mantener los datos del formulario para que el usuario no los pierda
        $publicacion = [
            'paqu_Id' => $_POST['id'],
            'paqu_Descripcion' => $_POST['descripcion'],
            'paqu_FechaLlegada' => $_POST['fecha'],
            'paqu_Hora' => $_POST['hora']
        ];
    }
} elseif (isset($_GET['id'])) {
    $publicacion = $controller->editar(intval($_GET['id']));
    if (!$publicacion) {
        $mensaje = "Publicación no encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicación - ZONEMAISONS</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <style>
        :root {
            --primary-color: #7b9a82;
            --primary-dark: #5a7961;
            --primary-light: #a3bdaa;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --border-color: #ddd;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --shadow: 0 2px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        .principal-page {
            flex: 1;
            padding: 40px;
            max-width: 1100px;
            margin: auto;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .principal-page h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
        }

        /* ----- Fieldsets ----- */
        .principal-page fieldset {
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            background-color: #f9f9f9;
        }

        .principal-page legend {
            font-weight: bold;
            font-size: 18px;
            color: #444;
            padding: 0 10px;
        }

        /* ----- Etiquetas e Inputs ----- */
        .principal-page label {
            margin-top: 10px;
            font-weight: bold;
            display: block;
            font-size: 14px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(123, 154, 130, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .checkbox-container input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .checkbox-container label {
            margin: 0;
            font-weight: normal;
        }

        .tiempo-info {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #1976d2;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border-color: var(--success-color);
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
            border-color: var(--warning-color);
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .enviar {
            background-color: #7b9a82;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .enviar:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .cancelar {
            background-color: #f13a02;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .cancelar:hover {
            background-color: #d32f02;
            transform: translateY(-2px);
        }

        .eliminar {
            background-color: var(--danger-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .eliminar:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body p {
            margin: 10px 0;
            color: #555;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-modal-cancelar {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-modal-cancelar:hover {
            background-color: #5a6268;
        }

        .btn-modal-eliminar {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-modal-eliminar:hover {
            background-color: #c82333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .principal-page {
                padding: 20px;
                margin: 20px;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .enviar, .cancelar, .eliminar {
                width: 100%;
                text-align: center;
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .btn-modal-cancelar,
            .btn-modal-eliminar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main>
    <section class="principal-page">
        <h2>Editar Paquetería</h2>

        <?php if ($mensaje): ?>
            <div class="alert <?= (strpos($mensaje, 'Error') !== false || strpos($mensaje, 'no encontrada') !== false) ? 'alert-error' : 'alert-success' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($publicacion): ?>
            <form method="POST" enctype="multipart/form-data" class="editar-form">
                <fieldset>
                    <legend>Formulario de Paquetería</legend>

                    <input type="hidden" name="id" value="<?= htmlspecialchars($publicacion['paqu_Id']) ?>">

                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"
                                  rows="5" required maxlength="1000"><?= htmlspecialchars($publicacion['paqu_Descripcion']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="fecha">Fecha de Llegada *</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="fechaActual" onchange="toggleFechaActual()">
                            <label for="fechaActual">Usar fecha actual</label>
                        </div>
                        <input type="date" id="fecha" name="fecha" class="form-control"
                               value="<?= htmlspecialchars($publicacion['paqu_FechaLlegada']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="hora">Hora</label>
                        <div class="checkbox-container">
                            <input type="checkbox" id="horaActual" onchange="toggleHoraActual()" checked>
                            <label for="horaActual">Actualizar a hora actual al guardar</label>
                        </div>
                     
                        <input type="time" id="hora" name="hora" class="form-control"
                               value="<?= htmlspecialchars($publicacion['paqu_Hora']) ?>" disabled>
                    </div>

                    <div class="estado">
                        <label>Estado de la Publicación</label>
                        <div class="checkbox-container">
                            <input type="radio" id="estadoPendiente" name="estado" value="1"
                                   <?= $publicacion['paqu_estado'] == 1 ? 'checked' : '' ?>>
                            <label for="estadoPendiente">Pendiente</label>
                        </div>
                        <div class="checkbox-container">
                            <input type="radio" id="estadoEntregado" name="estado" value="0"
                                   <?= $publicacion['paqu_estado'] == 0 ? 'checked' : '' ?>>
                            <label for="estadoEntregado">Entregado</label>
                        </div>

                    </div>

                    <div class="btn-container">
                        <button type="submit" name="editar" class="enviar">Guardar Cambios</button>
                        <button type="button" class="eliminar" onclick="abrirModal()">Eliminar</button>
                        <a href="novedades.php" class="cancelar">Volver</a>
                    </div>
                </fieldset>
            </form>

            <!-- Modal de confirmación para eliminar -->
            <div id="modalEliminar" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Confirmar eliminación</h3>
                        <span class="close" onclick="cerrarModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar esta publicación?</p>
                        <p><strong>Esta acción no se puede deshacer.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-modal-cancelar" onclick="cerrarModal()">Cancelar</button>
                        <button class="btn-modal-eliminar" onclick="eliminarPublicacion()">Eliminar</button>
                    </div>
                </div>
            </div>

            <!-- Formulario oculto para eliminación -->
            <form id="formEliminar" method="POST" style="display: none;">
                <input type="hidden" name="id" value="<?= htmlspecialchars($publicacion['paqu_Id']) ?>">
                <input type="hidden" name="eliminar" value="1">
            </form>

         <?php else: ?>
            <fieldset>
                <div class="alert alert-error">
                    <p>No se pudo cargar la publicación. Verifique que el ID sea válido.</p>
                </div>
                <div class="btn-container">
                    <a href="novedades.php" class="cancelar">Volver</a>
                </div>
            </fieldset>
        <?php endif; ?>
    </section>
</main>

<script>
function toggleFechaActual() {
    const checkbox = document.getElementById('fechaActual');
    const fechaInput = document.getElementById('fecha');
    
    if (checkbox.checked) {
        const hoy = new Date();
        const fechaFormateada = hoy.toISOString().split('T')[0];
        fechaInput.value = fechaFormateada;
        fechaInput.disabled = true;
    } else {
        fechaInput.disabled = false;
    }
}

function toggleHoraActual() {
    const checkbox = document.getElementById('horaActual');
    const horaInput = document.getElementById('hora');
    
    if (checkbox.checked) {
        // Limpiar el campo de hora para que el servidor use la hora actual
        horaInput.value = '';
        horaInput.disabled = true;
    } else {
        horaInput.disabled = false;
        // Restaurar la hora original si hay una
        const horaOriginal = '<?= htmlspecialchars($publicacion['paqu_Hora'] ?? '') ?>';
        if (horaOriginal) {
            horaInput.value = horaOriginal;
        }
    }
}

function abrirModal() {
    document.getElementById('modalEliminar').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalEliminar').style.display = 'none';
}

function eliminarPublicacion() {
    document.getElementById('formEliminar').submit();
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modalEliminar');
    if (event.target === modal) {
        cerrarModal();
    }
}

// Cerrar modal con la tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        cerrarModal();
    }
});

// Inicializar el estado del campo de hora al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleHoraActual(); // Esto deshabilitará el campo de hora inicialmente
});
</script>

<?php require_once "./Layout/footer.php"; ?>

</body>
</html>