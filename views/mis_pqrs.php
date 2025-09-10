<?php
// Asegurarse de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once './Layout/header.php';
require_once '../models/pqrsModel.php';

// Debug de sesión
error_log("=== DEBUG MIS_PQRS ===");
error_log("SESSION existe: " . (isset($_SESSION) ? 'SÍ' : 'NO'));
error_log("SESSION['usuario'] existe: " . (isset($_SESSION['usuario']) ? 'SÍ' : 'NO'));

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    $_SESSION['error_mensaje'] = 'Debes iniciar sesión para ver tus PQRS.';
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$usuarioCc = $usuario['usuario_cc'];

// Inicializar modelo
try {
    $pqrsModel = new PqrsModel();
    $registros = $pqrsModel->obtenerPorUsuario($usuarioCc);
} catch (Exception $e) {
    error_log("Error obteniendo PQRS: " . $e->getMessage());
    $registros = [];
}

// Obtener mensajes de éxito si existen
$mensajeExito = $_SESSION['mensaje_pqrs'] ?? null;
unset($_SESSION['mensaje_pqrs']);

// Verificar si viene de un envío exitoso
$mostrarExito = isset($_GET['success']) && $_GET['success'] == '1';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis PQRS - Zona Maisons</title>
    
    <!-- CSS Propios -->
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <link rel="stylesheet" href="../assets/Css/Layout/header.css">
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>

<body>
    <!-- CONTENIDO PRINCIPAL -->
    <div class="principal-page">
        <div class="header-seccion">
            <div>
                <h2>Mis Solicitudes PQRS</h2>
                <p class="subtitle">Aquí puedes consultar el estado de todas tus solicitudes y ver las respuestas</p>
            </div>
            <div class="acciones-header">
                <a href="crear_pqr.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Nueva PQRS
                </a>
            </div>
        </div>

        <!-- Mostrar mensaje de éxito si existe -->
        <?php if ($mensajeExito || $mostrarExito): ?>
            <div class="alert alert-success" id="mensaje-exito">
                <i class="ri-check-circle-fill"></i>
                <strong>
                    <?= $mensajeExito ? htmlspecialchars($mensajeExito['texto']) : '¡PQRS enviada exitosamente! Puedes hacer seguimiento desde esta página.' ?>
                </strong>
            </div>
        <?php endif; ?>

        <!-- Información del usuario -->
        <div class="info-usuario">
            <div class="alert alert-info">
                <i class="ri-user-fill"></i>
                <strong>Usuario:</strong> <?= htmlspecialchars($usuario['usu_nombre_completo']) ?>
                <br><small>
                    <strong>Apartamento:</strong> <?= htmlspecialchars($usuario['usu_apartamento_residencia'] ?? 'N/A') ?> - Torre <?= htmlspecialchars($usuario['usu_torre_residencia'] ?? 'N/A') ?>
                </small>
            </div>
        </div>

        <!-- Tabla de PQRS -->
        <div class="tabla-container">
            <?php if (empty($registros)): ?>
                <div class="alert alert-info">
                    <i class="ri-information-line"></i>
                    <strong>No tienes PQRS registradas</strong>
                    <br>Puedes crear tu primera solicitud haciendo clic en "Nueva PQRS"
                </div>
            <?php else: ?>
                <table id="tabla-pqrs" class="display table-responsive">
                    <thead>
                        <tr>
                            <th>Radicado</th>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Respuesta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $pqr): 
                            $medioAttr   = htmlspecialchars($pqr['medio_respuesta'] ?? 'correo_sms', ENT_QUOTES, 'UTF-8');
                            $tipoAttr    = htmlspecialchars($pqr['tipo_pqr'] ?? '', ENT_QUOTES, 'UTF-8');
                            $asuntoAttr  = htmlspecialchars($pqr['asunto'] ?? '', ENT_QUOTES, 'UTF-8');
                            // Mensaje sin saltos de línea para atributo data-*
                            $mensajePlano = preg_replace('/\s+/', ' ', $pqr['mensaje'] ?? '');
                            $mensajeAttr  = htmlspecialchars($mensajePlano, ENT_QUOTES, 'UTF-8');
                            $tieneRespuesta = !empty($pqr['respuesta']);
                            $fechaRespuesta = $tieneRespuesta && !empty($pqr['fecha_respuesta']) ? date('d/m/Y H:i', strtotime($pqr['fecha_respuesta'])) : null;
                        ?>
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
                                        <small class="mensaje-preview">
                                            <?= htmlspecialchars(substr($pqr['mensaje'], 0, 100)) ?>...
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fecha-info">
                                        <div class="fecha-creacion">
                                            <strong><?= date('d/m/Y H:i', strtotime($pqr['fecha_creacion'])) ?></strong>
                                            <small>Enviada</small>
                                        </div>
                                        <?php if ($fechaRespuesta): ?>
                                            <div class="fecha-respuesta">
                                                <strong><?= $fechaRespuesta ?></strong>
                                                <small>Respondida</small>
                                            </div>
                                        <?php endif; ?>
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
                                    <?php if ($tieneRespuesta): ?>
                                        <br><small class="text-success">
                                            <i class="ri-mail-check-line"></i> Notificada
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tieneRespuesta): ?>
                                        <span class="badge badge-success">
                                            <i class="ri-check-line"></i> Con respuesta
                                        </span>
                                        <br>
                                        <button class="btn-respuesta" onclick="verRespuesta(<?= $pqr['id'] ?>)" title="Ver respuesta">
                                            <i class="ri-eye-line"></i> Ver respuesta
                                        </button>
                                    <?php else: ?>
                                        <span class="badge badge-warning">
                                            <i class="ri-time-line"></i> Sin respuesta
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="acciones-cell">
                                        <!-- Ver detalles -->
                                        <button class="btn-accion btn-ver" onclick="verDetalles(<?= (int)$pqr['id'] ?>)" title="Ver detalles">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        
                                        <!-- Editar solo si está pendiente (abre modal, NO cambia de página) -->
                                        <?php if ($pqr['estado'] === 'pendiente'): ?>
                                            <button 
                                                type="button"
                                                class="btn-accion btn-editar"
                                                title="Editar"
                                                data-id="<?= (int)$pqr['id'] ?>"
                                                data-tipo="<?= $tipoAttr ?>"
                                                data-asunto="<?= $asuntoAttr ?>"
                                                data-mensaje="<?= $mensajeAttr ?>"
                                                data-medio="<?= $medioAttr ?>"
                                                onclick="abrirModalEditar(this)">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            
                                            <!-- Eliminar solo si está pendiente -->
                                            <button class="btn-accion btn-eliminar" onclick="eliminarPqr(<?= (int)$pqr['id'] ?>)" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Estadísticas rápidas -->
        <?php if (!empty($registros)): ?>
            <div class="estadisticas-rapidas">
                <h3>Resumen de mis PQRS</h3>
                <div class="stats-grid">
                    <?php
                    $total = count($registros);
                    $pendientes = count(array_filter($registros, fn($p) => $p['estado'] === 'pendiente'));
                    $proceso = count(array_filter($registros, fn($p) => $p['estado'] === 'en_proceso'));
                    $resueltas = count(array_filter($registros, fn($p) => $p['estado'] === 'resuelto'));
                    $conRespuesta = count(array_filter($registros, fn($p) => !empty($p['respuesta'])));
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="stat-label">Total PQRS</div>
                    </div>
                    
                    <div class="stat-card pendiente">
                        <div class="stat-number"><?= $pendientes ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    
                    <div class="stat-card proceso">
                        <div class="stat-number"><?= $proceso ?></div>
                        <div class="stat-label">En Proceso</div>
                    </div>
                    
                    <div class="stat-card resuelto">
                        <div class="stat-number"><?= $resueltas ?></div>
                        <div class="stat-label">Resueltas</div>
                    </div>
                    
                    <div class="stat-card respuesta">
                        <div class="stat-number"><?= $conRespuesta ?></div>
                        <div class="stat-label">Con Respuesta</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para ver detalles -->
    <div id="modalDetalles" class="modal">
        <div class="modal-content">
            <span class="close close-detalles">&times;</span>
            <h2>Detalles de la PQRS</h2>
            <div id="contenidoDetalles"></div>
        </div>
    </div>

    <!-- Modal para ver RESPUESTA -->
    <div id="modalRespuesta" class="modal">
        <div class="modal-content modal-respuesta">
            <span class="close close-respuesta">&times;</span>
            <h2><i class="ri-mail-line"></i> Respuesta Recibida</h2>
            <div id="contenidoRespuesta"></div>
        </div>
    </div>

    <!-- Modal para EDITAR -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close close-editar">&times;</span>
            <h2>Editar PQRS <small id="editIdLabel" style="font-weight:normal;color:#777;"></small></h2>
            <form id="formEditarPqr" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_tipo">Tipo</label>
                        <select name="tipo_pqr" id="edit_tipo" required>
                            <option value="peticion">Petición</option>
                            <option value="queja">Queja</option>
                            <option value="reclamo">Reclamo</option>
                            <option value="sugerencia">Sugerencia</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_medio">Medio de respuesta</label>
                        <select name="medio_respuesta" id="edit_medio" required>
                            <option value="correo_sms">Correo y SMS</option>
                            <option value="correo">Correo</option>
                            <option value="sms">SMS</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_asunto">Asunto</label>
                    <input type="text" id="edit_asunto" name="asunto" minlength="5" required>
                </div>

                <div class="form-group">
                    <label for="edit_mensaje">Mensaje</label>
                    <textarea id="edit_mensaje" name="mensaje" rows="5" minlength="10" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_archivos">Archivos (opcional)</label>
                    <input type="file" id="edit_archivos" name="archivos[]" multiple>
                    <small class="help-text">Si adjuntas nuevos archivos, se reemplazarán los anteriores.</small>
                </div>

                <div class="acciones-form">
                    <button type="button" class="btn btn-light" id="btnCancelarEditar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarEditar">
                        <i class="ri-save-3-line"></i>&nbsp;Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        // Inicializar DataTable
        $(document).ready(function() {
            if ($('#tabla-pqrs').length) {
                $('#tabla-pqrs').DataTable({
                    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                    order: [[3, 'desc']],
                    pageLength: 10,
                    responsive: true,
                    columnDefs: [{ orderable: false, targets: [6] }]
                });
            }
            setTimeout(function(){ $('#mensaje-exito').fadeOut(); }, 5000);
        });

        // ----- Ver Respuesta -----
        function verRespuesta(id) {
            fetch(`../controller/obtenerDetallesPqr.php?id=${id}&tipo=respuesta`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('contenidoRespuesta').innerHTML = data.html;
                        document.getElementById('modalRespuesta').style.display = 'block';
                    } else {
                        alert('Error al cargar respuesta: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al cargar la respuesta de la PQRS');
                });
        }

        // ----- Detalles -----
        function verDetalles(id) {
            fetch(`../controller/obtenerDetallesPqr.php?id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('contenidoDetalles').innerHTML = data.html;
                        document.getElementById('modalDetalles').style.display = 'block';
                    } else {
                        alert('Error al cargar detalles: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al cargar detalles de la PQRS');
                });
        }

        // ----- EDITAR -----
        function abrirModalEditar(btn) {
            const d = btn.dataset;
            // Llenar form
            document.getElementById('edit_id').value = d.id;
            document.getElementById('editIdLabel').textContent = `#${String(d.id).padStart(4,'0')}`;
            document.getElementById('edit_tipo').value = d.tipo || 'peticion';
            document.getElementById('edit_asunto').value = d.asunto || '';
            document.getElementById('edit_mensaje').value = (d.mensaje || '').trim();
            document.getElementById('edit_medio').value = d.medio || 'correo_sms';
            // Limpiar input de archivos
            document.getElementById('edit_archivos').value = '';
            // Mostrar modal
            document.getElementById('modalEditar').style.display = 'block';
        }

        // Envío del formulario de edición por AJAX
        document.getElementById('formEditarPqr').addEventListener('submit', function(e){
            e.preventDefault();
            const form = e.target;
            const fd = new FormData(form);
            const btn = document.getElementById('btnGuardarEditar');
            btn.disabled = true; btn.innerHTML = 'Guardando...';

            fetch('../controller/editarPqr.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Cerrar modal, notificar y recargar para reflejar cambios en la tabla
                        document.getElementById('modalEditar').style.display = 'none';
                        alert('PQRS actualizada exitosamente');
                        location.reload();
                    } else {
                        alert('No se pudo actualizar: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de comunicación con el servidor');
                })
                .finally(() => {
                    btn.disabled = false; btn.innerHTML = '<i class="ri-save-3-line"></i>&nbsp;Guardar cambios';
                });
        });

        // Eliminar PQRS
        function eliminarPqr(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta PQRS? Esta acción no se puede deshacer.')) {
                window.location.href = `../controller/eliminarPqr.php?id=${id}`;
            }
        }

        // Cerrar modales
        document.addEventListener('DOMContentLoaded', function() {
            const modalDet = document.getElementById('modalDetalles');
            const modalEdt = document.getElementById('modalEditar');
            const modalResp = document.getElementById('modalRespuesta');

            document.querySelector('.close-detalles').onclick = () => modalDet.style.display = 'none';
            document.querySelector('.close-editar').onclick   = () => modalEdt.style.display = 'none';
            document.querySelector('.close-respuesta').onclick = () => modalResp.style.display = 'none';
            document.getElementById('btnCancelarEditar').onclick = () => modalEdt.style.display = 'none';

            window.onclick = function(event) {
                if (event.target === modalDet) modalDet.style.display = 'none';
                if (event.target === modalEdt) modalEdt.style.display = 'none';
                if (event.target === modalResp) modalResp.style.display = 'none';
            }
        });
    </script>

    <!-- Estilos CSS -->
    <style>
        .principal-page { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header-seccion { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; }
        .header-seccion h2 { margin: 0 0 5px 0; font-size: 22px; color: #333; }
        .subtitle { font-size: 14px; color: #666; margin: 0; }
        .acciones-header { margin-left: auto; }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: none; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .btn i { margin-right: 8px; }

        .btn.btn-light { background:#f1f3f5; color:#333; }
        .btn.btn-light:hover { background:#e9ecef; }

        .alert { padding: 15px; margin: 15px 0; border-radius: 8px; border: 1px solid transparent; display: flex; align-items: flex-start; flex-direction: column; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert i { margin-right: 10px; font-size: 1.2em; }

        .info-usuario { margin-bottom: 20px; }
        .tabla-container { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 30px; }
        .table-responsive { width: 100% !important; }

        .badge { display: inline-block; padding: 4px 8px; font-size: 0.75em; font-weight: 500; border-radius: 4px; text-transform: uppercase; }
        .badge-peticion { background-color: #e3f2fd; color: #1976d2; }
        .badge-queja { background-color: #fff3e0; color: #f57c00; }
        .badge-reclamo { background-color: #ffebee; color: #d32f2f; }
        .badge-sugerencia { background-color: #f3e5f5; color: #7b1fa2; }
        .badge-success { background-color: #e8f5e8; color: #2e7d32; }
        .badge-warning { background-color: #fff8e1; color: #f9a825; }

        .asunto-cell strong { display: block; margin-bottom: 4px; }
        .mensaje-preview { color: #666; font-style: italic; }

        /* NUEVA: Fechas de creación y respuesta */
        .fecha-info { display: flex; flex-direction: column; gap: 5px; }
        .fecha-creacion, .fecha-respuesta { display: flex; flex-direction: column; }
        .fecha-creacion strong, .fecha-respuesta strong { font-size: 12px; }
        .fecha-creacion small { color: #666; font-size: 10px; }
        .fecha-respuesta small { color: #28a745; font-size: 10px; font-weight: bold; }
        .fecha-respuesta strong { color: #28a745; }

        .estado { padding: 4px 12px; border-radius: 20px; font-size: 0.85em; font-weight: 500; text-transform: uppercase; }
        .estado-pendiente { background-color: #fff3cd; color: #856404; }
        .estado-en_proceso { background-color: #cce5ff; color: #004085; }
        .estado-resuelto { background-color: #d1edda; color: #155724; }

        .acciones-cell { display: flex; gap: 5px; }
        .btn-accion { padding: 6px 8px; border: none; border-radius: 4px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-ver { background-color: #e3f2fd; color: #1976d2; }
        .btn-ver:hover { background-color: #bbdefb; }
        .btn-editar { background-color: #fff3e0; color: #f57c00; }
        .btn-editar:hover { background-color: #ffe0b2; }
        .btn-eliminar { background-color: #ffebee; color: #d32f2f; }
        .btn-eliminar:hover { background-color: #ffcdd2; }

        /* NUEVO: Botón para ver respuesta */
        .btn-respuesta { 
            background: #e8f5e8; 
            color: #2e7d32; 
            border: none; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            cursor: pointer; 
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        .btn-respuesta:hover { 
            background: #c8e6c8; 
            transform: translateY(-1px);
        }

        .estadisticas-rapidas { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; margin-top: 20px; }
        .estadisticas-rapidas h3 { margin-top: 0; margin-bottom: 20px; color: #333; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; }
        .stat-card { text-align: center; padding: 15px; border-radius: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .stat-card.pendiente { background-color: #fff3cd; border-color: #ffeaa7; }
        .stat-card.proceso { background-color: #cce5ff; border-color: #b3d9ff; }
        .stat-card.resuelto { background-color: #d1edda; border-color: #a3d9a3; }
        .stat-card.respuesta { background-color: #e3f2fd; border-color: #bbdefb; }
        .stat-number { font-size: 2em; font-weight: bold; color: #333; }
        .stat-label { font-size: 0.9em; color: #666; margin-top: 5px; }

        /* Modales */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; }
        
        /* NUEVO: Modal específico para respuestas */
        .modal-respuesta { max-width: 900px; }
        .modal-respuesta h2 { color: #2e7d32; margin-bottom: 20px; }
        
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: #000; }

        /* Form modal editar */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 12px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 12px; }
        .form-group label { font-weight: 600; margin-bottom: 6px; color:#333; }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea { border: 1px solid #ddd; border-radius: 6px; padding: 10px; font: inherit; }
        .help-text { color:#6c757d; }
        .acciones-form { display:flex; gap:10px; justify-content:flex-end; margin-top: 10px; }

        /* Texto de éxito en notificaciones */
        .text-success { color: #28a745; }

        /* Responsividad */
        @media (max-width: 768px) {
            .header-seccion { flex-direction: column; align-items: flex-start; }
            .acciones-header { margin-left: 0; margin-top: 10px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .acciones-cell { flex-direction: column; gap: 2px; }
            .fecha-info { flex-direction: row; gap: 10px; }
        }

        /* Animaciones */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .alert { animation: fadeIn 0.3s ease-out; }

        /* DataTables custom styles */
        .dataTables_wrapper { margin-top: 20px; }
        .dataTables_filter input { border: 1px solid #ddd; border-radius: 4px; padding: 8px; }
        .dataTables_length select { border: 1px solid #ddd; border-radius: 4px; padding: 4px; }
    </style>

</body>

<?php
require_once './Layout/footer.php';
?>

</html>
