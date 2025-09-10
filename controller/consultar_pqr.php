<?php
// consultar_pqr.php - Versión mejorada para usuarios logueados

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe iniciar sesión para consultar PQRS'
    ]);
    exit;
}

require_once '../models/pqrsModel.php';

$usuario = $_SESSION['usuario'];
$usuarioCc = $usuario['usuario_cc'];

try {
    $model = new PqrsModel();
    $resultados = $model->obtenerPorUsuario($usuarioCc);

    if (empty($resultados)) {
        echo "<div class='alert alert-info'>
                <i class='ri-information-line'></i>
                <strong>No tienes PQRS registradas</strong>
                <br>Puedes crear tu primera solicitud haciendo clic en 'Crear PQR'
              </div>";
        exit;
    }

    // Si se solicita JSON (para AJAX)
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        echo json_encode([
            'success' => true,
            'data' => $resultados,
            'total' => count($resultados)
        ]);
        exit;
    }

    // Mostrar tabla HTML
    ?>
    <table id="tabla-resultado" class="display">
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Tipo</th>
                <th>Asunto</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Respuesta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($resultados as $pqr): ?>
            <tr>
                <td>
                    <strong>PQRS-<?= date('Y') ?>-<?= str_pad($pqr['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                </td>
                <td>
                    <span class="badge badge-<?= $pqr['tipo_pqr'] ?>">
                        <?= ucfirst(htmlspecialchars($pqr['tipo_pqr'])) ?>
                    </span>
                </td>
                <td>
                    <div class="asunto-cell">
                        <strong><?= htmlspecialchars($pqr['asunto']) ?></strong>
                    </div>
                </td>
                <td>
                    <div class="mensaje-preview">
                        <?= htmlspecialchars(substr($pqr['mensaje'], 0, 100)) ?>
                        <?= strlen($pqr['mensaje']) > 100 ? '...' : '' ?>
                    </div>
                </td>
                <td>
                    <span class="estado estado-<?= $pqr['estado'] ?>">
                        <?php 
                        $estados = [
                            'pendiente' => 'Pendiente',
                            'en_proceso' => 'En Proceso',
                            'resuelto' => 'Resuelto'
                        ];
                        echo $estados[$pqr['estado']] ?? ucfirst($pqr['estado']);
                        ?>
                    </span>
                </td>
                <td>
                    <?= date('d/m/Y H:i', strtotime($pqr['fecha_creacion'])) ?>
                </td>
                <td>
                    <?php if (!empty($pqr['respuesta'])): ?>
                        <div class="respuesta-container">
                            <span class="badge badge-success">
                                <i class="ri-check-line"></i> Con respuesta
                            </span>
                            <div class="respuesta-preview">
                                <small>
                                    <?= htmlspecialchars(substr($pqr['respuesta'], 0, 80)) ?>
                                    <?= strlen($pqr['respuesta']) > 80 ? '...' : '' ?>
                                </small>
                                <?php if (!empty($pqr['fecha_respuesta'])): ?>
                                    <br><small class="text-muted">
                                        Respondida: <?= date('d/m/Y', strtotime($pqr['fecha_respuesta'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="badge badge-warning">
                            <i class="ri-time-line"></i> Sin respuesta
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="acciones-cell">
                        <!-- Ver detalles completos -->
                        <button class="btn-accion btn-ver" 
                                onclick="verDetallesCompletos(<?= $pqr['id'] ?>)" 
                                title="Ver detalles completos">
                            <i class="ri-eye-line"></i>
                        </button>
                        
                        <?php if ($pqr['estado'] === 'pendiente'): ?>
                            <!-- Editar solo si está pendiente -->
                            <button class="btn-accion btn-editar"
                                    onclick="editarPqr(<?= $pqr['id'] ?>)"
                                    title="Editar PQRS">
                                <i class="ri-edit-line"></i>
                            </button>
                            
                            <!-- Eliminar solo si está pendiente -->
                            <button class="btn-accion btn-eliminar" 
                                    onclick="eliminarPqr(<?= $pqr['id'] ?>)" 
                                    title="Eliminar PQRS">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        <?php else: ?>
                            <span class="text-muted" title="Solo se pueden editar PQRS pendientes">
                                <i class="ri-lock-line"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Estadísticas rápidas -->
    <div class="estadisticas-consulta">
        <h4>Resumen de sus PQRS</h4>
        <div class="stats-grid-small">
            <?php
            $total = count($resultados);
            $pendientes = count(array_filter($resultados, fn($p) => $p['estado'] === 'pendiente'));
            $proceso = count(array_filter($resultados, fn($p) => $p['estado'] === 'en_proceso'));
            $resueltas = count(array_filter($resultados, fn($p) => $p['estado'] === 'resuelto'));
            $conRespuesta = count(array_filter($resultados, fn($p) => !empty($p['respuesta'])));
            ?>
            
            <div class="stat-item">
                <span class="stat-number"><?= $total ?></span>
                <span class="stat-label">Total</span>
            </div>
            
            <div class="stat-item stat-pendiente">
                <span class="stat-number"><?= $pendientes ?></span>
                <span class="stat-label">Pendientes</span>
            </div>
            
            <div class="stat-item stat-proceso">
                <span class="stat-number"><?= $proceso ?></span>
                <span class="stat-label">En Proceso</span>
            </div>
            
            <div class="stat-item stat-resuelto">
                <span class="stat-number"><?= $resueltas ?></span>
                <span class="stat-label">Resueltas</span>
            </div>
            
            <div class="stat-item stat-respuesta">
                <span class="stat-number"><?= $conRespuesta ?></span>
                <span class="stat-label">Respondidas</span>
            </div>
        </div>
    </div>

    <!-- Modal de detalles completos -->
    <div id="modalDetallesCompletos" class="modal" style="display:none;">
        <div class="modal-content" style="width:90%;max-height:80vh;overflow-y:auto;">
            <span class="close-detalles">&times;</span>
            <h2>Detalles Completos de PQRS</h2>
            <div id="contenido-detalles-completos">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal de edición (simplificado para usuarios logueados) -->
    <div id="modalEditar" class="modal" style="display:none;">
        <div class="modal-content" style="width:90%;max-height:80vh;overflow-y:auto;">
            <span class="close-editar">&times;</span>
            <h2>Editar PQRS</h2>
            <div class="alert alert-info">
                <i class="ri-information-line"></i>
                Solo puedes editar PQRS que estén en estado "Pendiente"
            </div>
            <form id="form-editar" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <input type="hidden" name="action" value="editar">

                <!-- Información del usuario (solo lectura) -->
                <fieldset>
                    <legend>Información del Solicitante</legend>
                    <div class="info-readonly">
                        <strong>Nombre:</strong> <?= htmlspecialchars($usuario['usu_nombre_completo']) ?><br>
                        <strong>Cédula:</strong> <?= htmlspecialchars($usuario['usu_cedula']) ?><br>
                        <strong>Apartamento:</strong> <?= htmlspecialchars($usuario['usu_apartamento_residencia'] ?? 'N/A') ?> - Torre <?= htmlspecialchars($usuario['usu_torre_residencia'] ?? 'N/A') ?>
                    </div>
                </fieldset>

                <!-- Campos editables -->
                <fieldset>
                    <legend>Detalles de la Solicitud</legend>
                    
                    <label for="edit-tipo">Tipo de PQRS:</label>
                    <select name="tipo_pqr" id="edit-tipo" required>
                        <option value="peticion">Petición</option>
                        <option value="queja">Queja</option>
                        <option value="reclamo">Reclamo</option>
                        <option value="sugerencia">Sugerencia</option>
                    </select>

                    <label for="edit-asunto">Asunto:</label>
                    <input type="text" name="asunto" id="edit-asunto" required maxlength="255" minlength="5">

                    <label for="edit-mensaje">Descripción:</label>
                    <textarea name="mensaje" id="edit-mensaje" required minlength="10" rows="4"></textarea>

                    <label>¿Cómo quiere recibir respuesta?</label>
                    <div class="checkbox-group-edit">
                        <label>
                            <input type="checkbox" name="medio_respuesta[]" value="correo" id="edit-resp-correo"> 
                            Correo electrónico
                        </label>
                        <label>
                            <input type="checkbox" name="medio_respuesta[]" value="sms" id="edit-resp-sms"> 
                            SMS
                        </label>
                    </div>

                    <label for="edit-archivos">Reemplazar archivos (opcional):</label>
                    <input type="file" name="archivos[]" id="edit-archivos" multiple 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="file-help">Si adjunta nuevos archivos, reemplazarán los anteriores</small>
                </fieldset>

                <div class="form-actions-edit">
                    <button type="submit" class="btn-primary">
                        <i class="ri-save-line"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">
                        <i class="ri-close-line"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Estilos específicos para la consulta */
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.75em;
            font-weight: 500;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-peticion { background-color: #e3f2fd; color: #1976d2; }
        .badge-queja { background-color: #fff3e0; color: #f57c00; }
        .badge-reclamo { background-color: #ffebee; color: #d32f2f; }
        .badge-sugerencia { background-color: #f3e5f5; color: #7b1fa2; }
        .badge-success { background-color: #e8f5e8; color: #2e7d32; }
        .badge-warning { background-color: #fff8e1; color: #f9a825; }

        .estado {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
            text-transform: uppercase;
        }

        .estado-pendiente { background-color: #fff3cd; color: #856404; }
        .estado-en_proceso { background-color: #cce5ff; color: #004085; }
        .estado-resuelto { background-color: #d1edda; color: #155724; }

        .acciones-cell {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-accion {
            padding: 6px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn-ver { background-color: #e3f2fd; color: #1976d2; }
        .btn-ver:hover { background-color: #bbdefb; }

        .btn-editar { background-color: #fff3e0; color: #f57c00; }
        .btn-editar:hover { background-color: #ffe0b2; }

        .btn-eliminar { background-color: #ffebee; color: #d32f2f; }
        .btn-eliminar:hover { background-color: #ffcdd2; }

        .respuesta-container {
            max-width: 200px;
        }

        .respuesta-preview {
            margin-top: 5px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 0.85em;
        }

        .estadisticas-consulta {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .stats-grid-small {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
            border: 1px solid #dee2e6;
        }

        .stat-item.stat-pendiente { border-left: 4px solid #ffc107; }
        .stat-item.stat-proceso { border-left: 4px solid #007bff; }
        .stat-item.stat-resuelto { border-left: 4px solid #28a745; }
        .stat-item.stat-respuesta { border-left: 4px solid #17a2b8; }

        .stat-number {
            display: block;
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            display: block;
            font-size: 0.8em;
            color: #666;
            margin-top: 2px;
        }

        /* Modal styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            position: relative;
        }

        .close-detalles, .close-editar {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 15px;
        }

        .close-detalles:hover, .close-editar:hover {
            color: #000;
        }

        /* Form styles para edición */
        fieldset {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }

        legend {
            font-weight: bold;
            padding: 0 10px;
        }

        .info-readonly {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }

        .form-actions-edit {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-primary, .btn-secondary {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .checkbox-group-edit {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }

        .checkbox-group-edit label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-help {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 0.85em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid-small {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .acciones-cell {
                flex-direction: column;
                gap: 2px;
            }

            .form-actions-edit {
                flex-direction: column;
            }
        }
    </style>

    <script>
        // Función para ver detalles completos
        function verDetallesCompletos(id) {
            fetch(`../controller/obtenerDetallesPqr.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('contenido-detalles-completos').innerHTML = data.html;
                        document.getElementById('modalDetallesCompletos').style.display = 'block';
                    } else {
                        alert('Error al cargar detalles: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar detalles de la PQRS');
                });
        }

        // Función para editar PQRS
        function editarPqr(id) {
            // Obtener datos de la PQRS para pre-llenar el formulario
            fetch(`../controller/obtenerPqr.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const pqr = data.data;
                        
                        // Pre-llenar formulario
                        document.getElementById('edit-id').value = pqr.id;
                        document.getElementById('edit-tipo').value = pqr.tipo_pqr;
                        document.getElementById('edit-asunto').value = pqr.asunto;
                        document.getElementById('edit-mensaje').value = pqr.mensaje;
                        
                        // Marcar checkboxes de medio de respuesta
                        const medios = pqr.medio_respuesta.split(',');
                        document.getElementById('edit-resp-correo').checked = medios.includes('correo');
                        document.getElementById('edit-resp-sms').checked = medios.includes('sms');
                        
                        // Mostrar modal
                        document.getElementById('modalEditar').style.display = 'block';
                    } else {
                        alert('Error al cargar datos: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar datos de la PQRS');
                });
        }

        // Función para cerrar modal de edición
        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
        }

        // Manejar envío de formulario de edición
        document.getElementById('form-editar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../controller/editarPqr.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('PQRS actualizada exitosamente');
                    cerrarModalEditar();
                    location.reload(); // Recargar la tabla
                } else {
                    alert('Error al actualizar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });

        // Event listeners para cerrar modales
        document.addEventListener('DOMContentLoaded', function() {
            // Cerrar modal de detalles
            document.querySelector('.close-detalles').onclick = function() {
                document.getElementById('modalDetallesCompletos').style.display = 'none';
            }
            
            // Cerrar modal de edición
            document.querySelector('.close-editar').onclick = function() {
                cerrarModalEditar();
            }
            
            // Cerrar modales haciendo clic fuera
            window.onclick = function(event) {
                const modalDetalles = document.getElementById('modalDetallesCompletos');
                const modalEditar = document.getElementById('modalEditar');
                
                if (event.target == modalDetalles) {
                    modalDetalles.style.display = 'none';
                }
                if (event.target == modalEditar) {
                    modalEditar.style.display = 'none';
                }
            }
        });
    </script>

    <?php

} catch (Exception $e) {
    error_log("Error en consultar_pqr.php: " . $e->getMessage());
    echo "<div class='alert alert-danger'>
            <i class='ri-error-warning-line'></i>
            Error al cargar las PQRS. Intente nuevamente.
          </div>";
}
?>

