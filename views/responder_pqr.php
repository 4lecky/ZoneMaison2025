<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validación de acceso
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../models/pqrsModel.php';
require_once './Layout/header.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    echo "<p>ID de PQRS no válido.</p>";
    exit;
}

$pqrsModel = new PqrsModel();
$pqrs = $pqrsModel->obtenerPorId($id);

if (!$pqrs) {
    echo "<p>PQRS no encontrada.</p>";
    exit;
}

// Formatear datos
$fechaCreacion = date('d/m/Y H:i:s', strtotime($pqrs['fecha_creacion']));
$mediosRespuesta = explode(',', $pqrs['medio_respuesta']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Responder PQRS #<?= str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT) ?></title>
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <link rel="stylesheet" href="../assets/Css/Layout/header.css">
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>
    <div class="contenedor-admin">
        <!-- Encabezado -->
        <div class="encabezado-respuesta">
            <div class="navegacion-back">
                <a href="ver_pqr.php?id=<?= $pqrs['id'] ?>" class="boton-back">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Detalles
                </a>
            </div>
            <div class="titulo-respuesta">
                <h1>
                    <i class="fas fa-reply"></i>
                    Responder PQRS #<?= str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT) ?>
                </h1>
                <p class="subtitulo">Enviar respuesta a <?= htmlspecialchars($pqrs['nombres'] . ' ' . $pqrs['apellidos']) ?></p>
            </div>
        </div>

        <div class="contenedor-respuesta">
            <!-- Resumen de la solicitud -->
            <div class="tarjeta-resumen">
                <div class="cabecera-seccion">
                    <h2><i class="fas fa-info-circle"></i> Resumen de la Solicitud</h2>
                </div>
                <div class="contenido-resumen">
                    <div class="info-rapida">
                        <div class="item-rapido">
                            <span class="label">Tipo:</span>
                            <span class="tipo tipo-<?= strtolower($pqrs['tipo_pqr']) ?>">
                                <?= ucfirst($pqrs['tipo_pqr']) ?>
                            </span>
                        </div>
                        <div class="item-rapido">
                            <span class="label">Estado:</span>
                            <span class="estado estado-<?= str_replace('_', '-', $pqrs['estado']) ?>">
                                <?= ucfirst(str_replace('_', ' ', $pqrs['estado'])) ?>
                            </span>
                        </div>
                        <div class="item-rapido">
                            <span class="label">Fecha:</span>
                            <span><?= $fechaCreacion ?></span>
                        </div>
                    </div>
                    <div class="asunto-mensaje">
                        <h4>Asunto: <?= htmlspecialchars($pqrs['asunto']) ?></h4>
                        <div class="mensaje-original">
                            <?= nl2br(htmlspecialchars($pqrs['mensaje'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de respuesta -->
            <div class="tarjeta-formulario">
                <div class="cabecera-seccion">
                    <h2><i class="fas fa-edit"></i> Redactar Respuesta</h2>
                </div>
                <div class="contenido-formulario">
                    <form id="formRespuesta" class="formulario-respuesta" method="POST" action="../controller/AdministradorController.php" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="responder_pqrs_debug">
                        <input type="hidden" name="id" value="<?= $pqrs['id'] ?>">
                        
                        <!-- Información del destinatario -->
                        <div class="seccion-destinatario">
                            <h3><i class="fas fa-user"></i> Destinatario</h3>
                                                            <div class="info-destinatario">
                                    <p><strong><?= htmlspecialchars($pqrs['nombres'] . ' ' . $pqrs['apellidos']) ?></strong></p>
                                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($pqrs['email']) ?></p>
                                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($pqrs['telefono']) ?></p>
                                    <div class="medios-envio">
                                        <span class="label-medios">Se enviará por:</span>
                                        <?php foreach ($mediosRespuesta as $medio): ?>
                                            <span class="medio-seleccionado">
                                                <i class="fas fa-<?= trim($medio) === 'correo' ? 'envelope' : 'sms' ?>"></i>
                                                <?= ucfirst(trim($medio)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Plantillas rápidas -->
                        <div class="seccion-plantillas">
                            <h3><i class="fas fa-templates"></i> Plantillas Rápidas</h3>
                            <div class="plantillas-botones">
                                <button type="button" class="boton-plantilla" data-plantilla="recibido">
                                    <i class="fas fa-check"></i>
                                    Solicitud Recibida
                                </button>
                                <button type="button" class="boton-plantilla" data-plantilla="proceso">
                                    <i class="fas fa-cog"></i>
                                    En Proceso
                                </button>
                                <button type="button" class="boton-plantilla" data-plantilla="resuelto">
                                    <i class="fas fa-check-circle"></i>
                                    Solicitud Resuelta
                                </button>
                                <button type="button" class="boton-plantilla" data-plantilla="info_adicional">
                                    <i class="fas fa-question-circle"></i>
                                    Solicitar Información
                                </button>
                            </div>
                        </div>

                        <!-- Editor de respuesta -->
                        <div class="seccion-respuesta">
                            <h3><i class="fas fa-edit"></i> Su Respuesta</h3>
                            <div class="editor-container">
                                <textarea name="respuesta" id="textoRespuesta" class="editor-respuesta" 
                                          placeholder="Escriba aquí su respuesta detallada..." required></textarea>
                                <div class="contador-caracteres">
                                    <span id="contadorCaracteres">0</span> / 2000 caracteres
                                </div>
                            </div>
                        </div>

                         <div class="seccion-adjuntos">
                            <h3><i class="fas fa-paperclip"></i> Adjuntar Archivos</h3>
                        <div class="adjuntos-container">
                            <div class="drag-drop-area" id="dragDropArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                            <p>Arrastra archivos aquí o <span class="link-upload">haz clic para seleccionar</span></p>
                            <input type="file" id="inputArchivos" name="adjuntos[]" multiple 
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt,.xlsx,.xls" style="display: none;">
                        <small>Formatos permitidos: PDF, Word, Excel, Imágenes, Texto (Máx. 10MB por archivo)</small>
                        </div>
        
                        <div id="listaArchivos" class="lista-archivos" style="display: none;">
                            <h4>Archivos seleccionados:</h4>
                        <div id="archivosSeleccionados"></div>
                    </div>
                </div>
            </div>                                                                   

                        <!-- Opciones adicionales -->
                        <div class="seccion-opciones">
                            <h3><i class="fas fa-cogs"></i> Opciones Adicionales</h3>
                            <div class="opciones-grid">
                                <div class="opcion-item">
                                    <input type="checkbox" id="marcarResuelto" name="marcar_resuelto" value="1">
                                    <label for="marcarResuelto">
                                        <i class="fas fa-check-double"></i>
                                        Marcar como resuelto después de enviar
                                    </label>
                                </div>
                                <div class="opcion-item">
                                    <input type="checkbox" id="enviarCopia" name="enviar_copia" value="1" checked>
                                    <label for="enviarCopia">
                                        <i class="fas fa-copy"></i>
                                        Enviar copia al administrador
                                    </label>
                                </div>
                                <div class="opcion-item">
                                    <input type="checkbox" id="programarSeguimiento" name="programar_seguimiento" value="1">
                                    <label for="programarSeguimiento">
                                        <i class="fas fa-calendar-plus"></i>
                                        Programar seguimiento automático
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="seccion-acciones">
                            <div class="botones-principales">
                                <button type="button" id="btnVistaPrevia" class="boton boton-secundario">
                                    <i class="fas fa-eye"></i>
                                    Vista Previa
                                </button>
                                <button type="submit" id="btnEnviarRespuesta" class="boton boton-primario">
                                    <i class="fas fa-paper-plane"></i>
                                    Enviar Respuesta
                                </button>
                            </div>
                            <div class="botones-secundarios">
                                <button type="button" id="btnGuardarBorrador" class="boton boton-outline">
                                    <i class="fas fa-save"></i>
                                    Guardar Borrador
                                </button>
                                <button type="button" id="btnLimpiar" class="boton boton-outline">
                                    <i class="fas fa-eraser"></i>
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de vista previa -->
    <div id="modalVistaPrevia" class="modal" style="display: none;">
        <div class="modal-content modal-grande">
            <span class="close">&times;</span>
            <h3><i class="fas fa-eye"></i> Vista Previa de la Respuesta</h3>
            <div id="contenidoVistaPrevia" class="vista-previa-contenido">
                <!-- Se llena dinámicamente -->
            </div>
            <div class="botones-modal">
                <button id="btnCerrarPrevia" class="boton boton-secundario">Cerrar</button>
                <button id="btnEnviarDesdePrevia" class="boton boton-primario">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Respuesta
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirmacion" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="tituloConfirmacion">Confirmar Envío</h3>
            <p id="mensajeConfirmacion">¿Está seguro de enviar esta respuesta?</p>
            <div class="botones-modal">
                <button id="btnConfirmarEnvio" class="boton boton-primario">Sí, Enviar</button>
                <button id="btnCancelarEnvio" class="boton boton-secundario">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    <div id="mensajeExito" class="mensaje-flotante mensaje-exito" style="display: none;">
        <i class="fas fa-check-circle"></i>
        <span id="textoMensajeExito">Respuesta enviada correctamente</span>
    </div>

    <!-- Mensaje de error -->
    <div id="mensajeError" class="mensaje-flotante mensaje-error" style="display: none;">
        <i class="fas fa-exclamation-circle"></i>
        <span id="textoMensajeError">Error al enviar la respuesta</span>
    </div>

    <!-- JavaScript -->
<script>
    // JavaScript completo corregido para responder PQRS con adjuntos
$(document).ready(function() {
    const pqrsId = <?= $pqrs['id'] ?>;
    
    // Variables para archivos
    let archivosSeleccionados = [];
    
    // Plantillas de respuesta
    const plantillas = {
        recibido: `Estimado/a <?= $pqrs['nombres'] ?>,

Hemos recibido su ${$('#tipoSolicitud').text().toLowerCase()} y queremos confirmarle que está siendo procesada por nuestro equipo.

Su solicitud ha sido registrada con el número #${String(pqrsId).padStart(4, '0')} y será atendida en el menor tiempo posible.

Le mantendremos informado/a sobre el progreso de su solicitud.

Atentamente,
Administración Conjunto ZoneMaisons`,

        proceso: `Estimado/a <?= $pqrs['nombres'] ?>,

Nos complace informarle que su solicitud #${String(pqrsId).padStart(4, '0')} se encuentra actualmente en proceso de revisión y gestión.

Nuestro equipo está trabajando para brindarle una solución satisfactoria en el menor tiempo posible.

Si tiene alguna pregunta adicional o información que considere relevante, no dude en contactarnos.

Atentamente,
Administración Conjunto ZoneMaisons`,

        resuelto: `Estimado/a <?= $pqrs['nombres'] ?>,

Nos complace informarle que su solicitud #${String(pqrsId).padStart(4, '0')} ha sido resuelta satisfactoriamente.

[Describa aquí la solución implementada o las acciones tomadas]

Si tiene alguna pregunta adicional o no está completamente satisfecho/a con la solución, no dude en contactarnos nuevamente.

Agradecemos su confianza y esperamos haber resuelto su inquietud de manera satisfactoria.

Atentamente,
Administración Conjunto ZoneMaisons`,

        info_adicional: `Estimado/a <?= $pqrs['nombres'] ?>,

Hemos recibido su solicitud #${String(pqrsId).padStart(4, '0')} y estamos trabajando en ella.

Para poder brindarle la mejor atención y resolver su solicitud de manera efectiva, necesitamos que nos proporcione información adicional:

[Especifique aquí la información requerida]

Una vez recibamos esta información, procederemos inmediatamente con la gestión de su solicitud.

Atentamente,
Administración Conjunto ZoneMaisons`
    };
    
    // Cargar plantilla
    $('.boton-plantilla').click(function() {
        const tipoPlantilla = $(this).data('plantilla');
        const textoPlantilla = plantillas[tipoPlantilla];
        
        if (textoPlantilla) {
            $('#textoRespuesta').val(textoPlantilla);
            actualizarContador();
            
            // Marcar como resuelto si es plantilla de resolución
            if (tipoPlantilla === 'resuelto') {
                $('#marcarResuelto').prop('checked', true);
            }
        }
    });
    
    // Contador de caracteres
    function actualizarContador() {
        const longitud = $('#textoRespuesta').val().length;
        $('#contadorCaracteres').text(longitud);
        
        if (longitud > 2000) {
            $('#contadorCaracteres').css('color', '#dc3545');
        } else if (longitud > 1800) {
            $('#contadorCaracteres').css('color', '#ffc107');
        } else {
            $('#contadorCaracteres').css('color', '#28a745');
        }
    }
    
    $('#textoRespuesta').on('input', actualizarContador);
    
    // Vista previa
    $('#btnVistaPrevia').click(function() {
        const respuesta = $('#textoRespuesta').val();
        if (!respuesta.trim()) {
            alert('Por favor, escriba una respuesta antes de ver la vista previa.');
            return;
        }
        
        mostrarVistaPrevia(respuesta);
    });
    
    function mostrarVistaPrevia(respuesta) {
        const contenidoHTML = `
            <div class="preview-email">
                <div class="preview-header">
                    <h4>Asunto: Respuesta a su <?= ucfirst($pqrs['tipo_pqr']) ?> - #${String(pqrsId).padStart(4, '0')}</h4>
                    <p><strong>Para:</strong> <?= $pqrs['email'] ?></p>
                    <p><strong>De:</strong> Conjunto Zona Maisons</p>
                </div>
                <div class="preview-body">
                    ${respuesta.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
        
        $('#contenidoVistaPrevia').html(contenidoHTML);
        $('#modalVistaPrevia').show();
    }
    
    // Enviar desde vista previa
    $('#btnEnviarDesdePrevia').click(function() {
        $('#modalVistaPrevia').hide();
        enviarRespuesta();
    });
    
    // Envío del formulario
    $('#formRespuesta').submit(function(e) {
        e.preventDefault();
        
        const respuesta = $('#textoRespuesta').val().trim();
        if (!respuesta) {
            alert('Por favor, escriba una respuesta.');
            return;
        }
        
        if (respuesta.length > 2000) {
            alert('La respuesta es demasiado larga. Máximo 2000 caracteres.');
            return;
        }
        
        // Mostrar confirmación
        let mensaje = '¿Está seguro de enviar esta respuesta?';
        if ($('#marcarResuelto').prop('checked')) {
            mensaje += '\n\nLa PQRS será marcada como RESUELTA.';
        }
        if (archivosSeleccionados.length > 0) {
            mensaje += `\n\nSe enviarán ${archivosSeleccionados.length} archivo(s) adjunto(s).`;
        }
        
        if (confirm(mensaje)) {
            enviarRespuesta();
        }
    });
    
    // MANEJO DE ARCHIVOS CORREGIDO
    // Eliminar eventos previos para evitar duplicados
$('#dragDropArea').off('click');
$('.link-upload').off('click');
$(document).off('click', '#dragDropArea');
$(document).off('click', '.link-upload');

// NUEVO MANEJO SIN CONFLICTOS
let eventoArchivoConfigurado = false;

if (!eventoArchivoConfigurado) {
    // Solo configurar una vez
    eventoArchivoConfigurado = true;
    
    // Click directo en el área (sin delegación)
    document.getElementById('dragDropArea').addEventListener('click', function(e) {
        // No activar si se hace click en botones de eliminar
        if (e.target.matches('.btn-eliminar-archivo') || e.target.matches('.btn-eliminar-archivo *')) {
            return;
        }
        
        console.log('Abriendo selector de archivos...');
        document.getElementById('inputArchivos').click();
    });
    
    // Drag & Drop eventos
    const dragArea = document.getElementById('dragDropArea');
    
    dragArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    
    dragArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        // Solo remover si realmente salimos del área
        if (!this.contains(e.relatedTarget)) {
            this.classList.remove('drag-over');
        }
    });
    
    dragArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        const archivos = e.dataTransfer.files;
        console.log('Archivos arrastrados:', archivos.length);
        procesarArchivos(archivos);
    });
    
    // Selección manual de archivos
    document.getElementById('inputArchivos').addEventListener('change', function(e) {
        const archivos = this.files;
        console.log('Archivos seleccionados:', archivos.length);
        procesarArchivos(archivos);
    });
    
    console.log('✅ Eventos de archivo configurados correctamente');
}

// Eliminar archivo - usando delegación pero sin conflictos
$(document).on('click', '.btn-eliminar-archivo', function(e) {
    e.preventDefault();
    e.stopPropagation(); // Evitar que se propague al dragDropArea
    
    const index = $(this).data('index');
    const nombreArchivo = archivosSeleccionados[index].name;
    
    if (confirm(`¿Eliminar ${nombreArchivo}?`)) {
        archivosSeleccionados.splice(index, 1);
        mostrarArchivosSeleccionados();
        console.log('Archivo eliminado. Total restante:', archivosSeleccionados.length);
    }
});

    
    // FUNCIÓN PRINCIPAL DE ENVÍO - CORREGIDA COMPLETAMENTE
    function enviarRespuesta() {
        console.log('=== INICIANDO ENVÍO DE RESPUESTA ===');
        
        // Crear FormData del formulario
        const form = document.getElementById('formRespuesta');
        const formData = new FormData();
        
        // Agregar campos del formulario manualmente para mejor control
        formData.append('accion', 'responder_pqrs_debug'); // Temporal para debug
        formData.append('id', pqrsId);
        formData.append('respuesta', $('#textoRespuesta').val().trim());
        
        // Agregar checkboxes solo si están marcados
        if ($('#marcarResuelto').prop('checked')) {
            formData.append('marcar_resuelto', '1');
        }
        if ($('#enviarCopia').prop('checked')) {
            formData.append('enviar_copia', '1');
        }
        if ($('#programarSeguimiento').prop('checked')) {
            formData.append('programar_seguimiento', '1');
        }
        
        // AGREGAR ARCHIVOS CORRECTAMENTE
        // AGREGAR ARCHIVOS CORRECTAMENTE - VERSIÓN SIMPLE QUE FUNCIONA
if (archivosSeleccionados.length > 0) {
    console.log('Agregando', archivosSeleccionados.length, 'archivos al FormData');
    
    archivosSeleccionados.forEach((archivo, index) => {
        formData.append('adjuntos[]', archivo, archivo.name);
        console.log(`Archivo ${index}: ${archivo.name} (${archivo.size} bytes, ${archivo.type})`);
    });
} else {
    console.log('No hay archivos para enviar');
}



        
        // Deshabilitar botón y mostrar loading
        const $btnEnviar = $('#btnEnviarRespuesta');
        $btnEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        
        // Realizar petición AJAX
        $.ajax({
            url: '../controller/AdministradorController.php', // Verificar que esta ruta sea correcta
            type: 'POST',
            data: formData,
            processData: false, // CRÍTICO: No procesar los datos
            contentType: false, // CRÍTICO: Dejar que jQuery establezca el content-type
            dataType: 'json',
            timeout: 120000, // 2 minutos para archivos grandes
            
            beforeSend: function(xhr) {
                console.log('=== ENVIANDO PETICIÓN ===');
                console.log('URL:', '../controller/AdministradorController.php');
                console.log('Archivos adjuntos:', archivosSeleccionados.length);
                console.log('Método:', 'POST');
                console.log('Timeout:', '120 segundos');
            },
            
            success: function(response) {
                console.log('=== RESPUESTA EXITOSA ===');
                console.log('Response:', response);
                
                if (response && response.success) {
                    let mensaje = response.message || 'Respuesta enviada correctamente';
                    
                    // Agregar información de adjuntos si está disponible
                    if (response.data && response.data.adjuntos_procesados > 0) {
                        mensaje += ` con ${response.data.adjuntos_procesados} archivo(s) adjunto(s)`;
                    }
                    
                    // Mostrar información adicional si está disponible
                    if (response.data && response.data.notificacion_enviada) {
                        mensaje += ' y notificación enviada por correo';
                    }
                    
                    mostrarMensaje('exito', mensaje);
                    
                    // Limpiar formulario
                    limpiarFormulario();
                    
                    // Redirigir después de un momento
                    setTimeout(function() {
                        window.location.href = 'ver_pqr.php?id=' + pqrsId;
                    }, 3000);
                    
                } else {
                    const mensajeError = response.error || response.message || 'Error desconocido al enviar la respuesta';
                    console.log('Error en respuesta:', mensajeError);
                    mostrarMensaje('error', mensajeError);
                    
                    // Restaurar botón
                    $btnEnviar.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Respuesta');
                }
            },
            
            error: function(xhr, status, error) {
                console.log('=== ERROR EN PETICIÓN AJAX ===');
                console.log('Status HTTP:', xhr.status);
                console.log('Status Text:', xhr.statusText);
                console.log('Ready State:', xhr.readyState);
                console.log('Response Text:', xhr.responseText);
                console.log('Error:', error);
                console.log('AJAX Status:', status);
                
                let mensajeError = 'Error de comunicación con el servidor';
                
                // Mensajes específicos según el error
                switch (xhr.status) {
                    case 0:
                        mensajeError = 'No se puede conectar al servidor. Verifique su conexión.';
                        break;
                    case 404:
                        mensajeError = 'Controlador no encontrado (404). Verifique la ruta del archivo.';
                        break;
                    case 413:
                        mensajeError = 'Los archivos son muy grandes para el servidor (413). Reduzca el tamaño.';
                        break;
                    case 500:
                        mensajeError = 'Error interno del servidor (500). Revise los logs de PHP.';
                        break;
                    case 200:
                        mensajeError = 'Error en el formato de respuesta del servidor';
                        console.log('Respuesta completa (Status 200 pero error parsing):', xhr.responseText);
                        break;
                    default:
                        mensajeError = `Error del servidor (${xhr.status}): ${xhr.statusText}`;
                }
                
                // Si hay respuesta del servidor, intentar mostrarla
                if (xhr.responseText && xhr.responseText.length < 500) {
                    mensajeError += `\nDetalle: ${xhr.responseText}`;
                }
                
                mostrarMensaje('error', mensajeError);
                
                // Restaurar botón
                $btnEnviar.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Respuesta');
            }
        });
    }
    
    // Mostrar mensajes al usuario
    function mostrarMensaje(tipo, texto) {
        // Limpiar mensajes anteriores
        $('.mensaje-sistema').remove();
        
        const claseCSS = tipo === 'exito' ? 'alert-success' : 'alert-danger';
        const icono = tipo === 'exito' ? 'check-circle' : 'exclamation-triangle';
        
        const mensajeHTML = `
            <div class="mensaje-sistema alert ${claseCSS}" style="margin: 20px 0; padding: 15px; border-radius: 5px; border: 1px solid; display: none;">
                <i class="fas fa-${icono}"></i>
                <strong>${tipo === 'exito' ? 'Éxito:' : 'Error:'}</strong> ${texto}
            </div>
        `;
        
        // Insertar mensaje antes del formulario
        $('.tarjeta-formulario .contenido-formulario').prepend(mensajeHTML);
        $('.mensaje-sistema').fadeIn();
        
        // Auto-ocultar después de unos segundos (solo errores)
        if (tipo === 'error') {
            setTimeout(() => {
                $('.mensaje-sistema').fadeOut();
            }, 8000);
        }
        
        // Scroll al mensaje
        $('html, body').animate({
            scrollTop: $('.mensaje-sistema').offset().top - 100
        }, 500);
    }
    
    // Limpiar formulario
    function limpiarFormulario() {
        $('#textoRespuesta').val('');
        $('#marcarResuelto').prop('checked', false);
        $('#programarSeguimiento').prop('checked', false);
        
        // Limpiar archivos
        archivosSeleccionados = [];
        mostrarArchivosSeleccionados();
        $('#inputArchivos').val('');
        $('#dragDropArea p').html('Arrastra archivos aquí o <span class="link-upload">haz clic para seleccionar</span>');
        
        actualizarContador();
    }
    
    // Limpiar formulario manual
    $('#btnLimpiar').click(function() {
        if (confirm('¿Está seguro de limpiar el formulario? Se perderán todos los datos ingresados.')) {
            limpiarFormulario();
        }
    });
    
    // Guardar borrador (funcionalidad futura)
    $('#btnGuardarBorrador').click(function() {
        alert('Funcionalidad de borrador en desarrollo');
    });
    
    // Cerrar modales
    $('.close, #btnCerrarPrevia, #btnCancelarEnvio').click(function() {
        $('.modal').hide();
    });
    
    // Cerrar modal al hacer clic fuera
    $(window).click(function(event) {
        if ($(event.target).hasClass('modal')) {
            $('.modal').hide();
        }
    });
    
    // Confirmar envío desde modal
    $('#btnConfirmarEnvio').click(function() {
        $('#modalConfirmacion').hide();
        enviarRespuesta();
    });
    
    // FUNCIONES PARA VALIDACIÓN EN TIEMPO REAL
    
    // Validar formulario en tiempo real
    function validarFormulario() {
        const respuesta = $('#textoRespuesta').val().trim();
        const esValido = respuesta.length >= 10 && respuesta.length <= 2000;
        
        $('#btnEnviarRespuesta').prop('disabled', !esValido);
        $('#btnVistaPrevia').prop('disabled', !esValido);
        
        return esValido;
    }
    
    // Aplicar validación en tiempo real
    $('#textoRespuesta').on('input', function() {
        actualizarContador();
        validarFormulario();
    });
    
    // FUNCIONES DE UTILIDAD ADICIONALES
    
    // Limpiar todos los archivos
    function limpiarTodosLosArchivos() {
        archivosSeleccionados = [];
        mostrarArchivosSeleccionados();
        $('#inputArchivos').val('');
        console.log('Todos los archivos eliminados');
    }
    
    // Debug de archivos actual
    function debugArchivos() {
        console.log('=== DEBUG ARCHIVOS ACTUAL ===');
        console.log('Total archivos:', archivosSeleccionados.length);
        archivosSeleccionados.forEach((archivo, i) => {
            console.log(`${i}: ${archivo.name} (${archivo.size} bytes, ${archivo.type})`);
        });
    }
    
    // Exponer funciones para debug desde consola
    window.debugPQRS = {
        archivos: () => debugArchivos(),
        limpiar: () => limpiarTodosLosArchivos(),
        enviar: () => enviarRespuesta(),
        form: () => document.getElementById('formRespuesta')
    };
    
    // Inicializar componentes
    actualizarContador();
    validarFormulario();

    // Procesar archivos seleccionados
function procesarArchivos(archivos) {
    console.log('=== PROCESANDO ARCHIVOS ===');
    console.log('Archivos recibidos:', archivos.length);
    
    for (let i = 0; i < archivos.length; i++) {
        const archivo = archivos[i];
        console.log(`Validando archivo ${i}: ${archivo.name} (${archivo.size} bytes, ${archivo.type})`);
        
        if (validarArchivo(archivo)) {
            archivosSeleccionados.push(archivo);
            console.log(`✅ Archivo agregado: ${archivo.name}`);
        } else {
            console.log(`❌ Archivo rechazado: ${archivo.name}`);
        }
    }
    
    console.log('Total archivos seleccionados:', archivosSeleccionados.length);
    mostrarArchivosSeleccionados();
    
    // Limpiar el input para permitir seleccionar los mismos archivos después
    document.getElementById('inputArchivos').value = '';
}

// Validar archivo
function validarArchivo(archivo) {
    const tiposPermitidos = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'text/plain'
    ];
    
    const maxSize = 15 * 1024 * 1024; // 15MB
    
    // Validar tipo
    if (!tiposPermitidos.includes(archivo.type)) {
        alert(`Formato no permitido: ${archivo.name}\nFormatos aceptados: PDF, Word, Excel, JPG, PNG, TXT`);
        return false;
    }
    
    // Validar tamaño
    if (archivo.size > maxSize) {
        alert(`Archivo muy grande: ${archivo.name}\nTamaño máximo: 15MB\nTamaño actual: ${formatearTamaño(archivo.size)}`);
        return false;
    }
    
    if (archivo.size === 0) {
        alert(`Archivo vacío: ${archivo.name}`);
        return false;
    }
    
    // Evitar duplicados por nombre y tamaño
    if (archivosSeleccionados.find(a => a.name === archivo.name && a.size === archivo.size)) {
        alert(`Archivo ya seleccionado: ${archivo.name}`);
        return false;
    }
    
    // Límite de archivos
    if (archivosSeleccionados.length >= 5) {
        alert('Máximo 5 archivos por respuesta');
        return false;
    }
    
    return true;
}

// Mostrar archivos seleccionados
function mostrarArchivosSeleccionados() {
    if (archivosSeleccionados.length === 0) {
        $('#listaArchivos').hide();
        $('#dragDropArea p').html('Arrastra archivos aquí o <span class="link-upload">haz clic para seleccionar</span>');
        return;
    }
    
    $('#listaArchivos').show();
    let html = '';
    
    archivosSeleccionados.forEach((archivo, index) => {
        const icono = obtenerIconoArchivo(archivo.type);
        const tamaño = formatearTamaño(archivo.size);
        
        html += `
            <div class="archivo-item" data-index="${index}">
                <div class="archivo-info">
                    <i class="fas fa-${icono}"></i>
                    <span class="nombre">${archivo.name}</span>
                    <span class="tamaño">${tamaño}</span>
                </div>
                <button type="button" class="btn-eliminar-archivo" data-index="${index}" title="Eliminar archivo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    
    $('#archivosSeleccionados').html(html);
    
    // Actualizar contador en el área de drag & drop
    $('#dragDropArea p').html(`<strong>${archivosSeleccionados.length} archivo(s) seleccionado(s)</strong><br><span class="link-upload">Haz clic para agregar más archivos</span>`);
}

// Obtener icono según tipo de archivo
function obtenerIconoArchivo(tipo) {
    const iconos = {
        'application/pdf': 'file-pdf',
        'application/msword': 'file-word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'file-word',
        'application/vnd.ms-excel': 'file-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'file-excel',
        'image/jpeg': 'file-image',
        'image/jpg': 'file-image',
        'image/png': 'file-image',
        'text/plain': 'file-alt'
    };
    
    return iconos[tipo] || 'file';
}

// Formatear tamaño de archivo
function formatearTamaño(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
    
    console.log('=== SISTEMA PQRS RESPUESTA INICIADO ===');
    console.log('PQRS ID:', pqrsId);
    console.log('Funciones debug disponibles en window.debugPQRS');
});
</script>

    <style>
    /* Estilos específicos para responder PQRS */
    .contenedor-admin {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .encabezado-respuesta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #7b9a82, #518056ff);
        color: white;
        border-radius: 10px;
    }
    
    .titulo-respuesta h1 {
        margin: 0;
        font-size: 1.8em;
    }
    
    .subtitulo {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 1.1em;
    }
    
    .contenedor-respuesta {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
    }
    
    .tarjeta-resumen, .tarjeta-formulario {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .tarjeta-resumen {
        height: fit-content;
        position: sticky;
        top: 20px;
    }
    
    .contenido-resumen {
        padding: 20px;
    }
    
    .info-rapida {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .item-rapido {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .item-rapido .label {
        font-weight: bold;
        min-width: 60px;
        color: #666;
    }
    
    .asunto-mensaje h4 {
        color: #552c2cff;
        margin-bottom: 10px;
    }
    
    .mensaje-original {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #7b9a82;
        font-style: italic;
        max-height: 150px;
        overflow-y: auto;
    }
    
    .contenido-formulario {
        padding: 20px;
    }
    
    .formulario-respuesta {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
    
    .seccion-destinatario, .seccion-plantillas, .seccion-respuesta, 
    .seccion-opciones, .seccion-acciones {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    
    .seccion-acciones {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .seccion-destinatario h3, .seccion-plantillas h3, 
    .seccion-respuesta h3, .seccion-opciones h3 {
        color: #7b9a82;
        margin-bottom: 15px;
        font-size: 1.2em;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-destinatario {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    
    .info-destinatario p {
        margin: 5px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .medios-envio {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .label-medios {
        font-weight: bold;
        color: #666;
    }
    
    .medio-seleccionado {
        background: #e9ecef;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9em;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .plantillas-botones {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }
    
    .boton-plantilla {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9em;
    }
    
    .boton-plantilla:hover {
        border-color: #7b9a82;
        background: #f8f9fa;
        transform: translateY(-1px);
    }
    
    .editor-container {
        position: relative;
    }
    
    .editor-respuesta {
        width: 100%;
        min-height: 300px;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-family: Arial, sans-serif;
        font-size: 1em;
        line-height: 1.6;
        resize: vertical;
        transition: border-color 0.3s;
    }
    
    .editor-respuesta:focus {
        outline: none;
        border-color: #2c5530;
    }
    
    .contador-caracteres {
        text-align: right;
        margin-top: 5px;
        font-size: 0.9em;
        color: #666;
    }
    
    .opciones-grid {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .opcion-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .opcion-item input[type="checkbox"] {
        transform: scale(1.2);
    }
    
    .opcion-item label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }
    
    .seccion-acciones {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .botones-principales, .botones-secundarios {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    .boton {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .boton-primario {
        background: #6b8671ff;
        color: white;
    }
    
    .boton-primario:hover {
        background: #61886aff;
        transform: translateY(-2px);
    }
    
    .boton-secundario {
        background: #9D825D;
        color: white;
    }
    
    .boton-secundario:hover {
        background: #9D825D;
    }
    
    .boton-outline {
        background: transparent;
        color: #6c757d;
        border: 2px solid #6c757d;
    }
    
    .boton-outline:hover {
        background: #9D825D;
        color: white;
    }
    
    .boton:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Modales */
    .modal-grande {
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .vista-previa-contenido {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin: 20px 0;
    }
    
    .preview-email {
        font-family: Arial, sans-serif;
    }
    
    .preview-header {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 2px solid #ddd;
    }
    
    .preview-header h4 {
        margin: 0 0 10px 0;
        color: #7b9a82;
    }
    
    .preview-header p {
        margin: 5px 0;
        color: #666;
    }
    
    .preview-body {
        padding: 20px;
        line-height: 1.6;
        background: white;
    }
    
    /* Mensajes flotantes */
    .mensaje-flotante {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 1001;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .mensaje-exito {
        background: #28a745;
    }
    
    .mensaje-error {
        background: #000000ff;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .contenedor-respuesta {
            grid-template-columns: 1fr;
        }
        
        .tarjeta-resumen {
            position: relative;
            order: 1;
        }
        
        .tarjeta-formulario {
            order: 2;
        }
    }
    
    @media (max-width: 768px) {
        .encabezado-respuesta {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .plantillas-botones {
            grid-template-columns: 1fr;
        }
        
        .botones-principales, .botones-secundarios {
            flex-direction: column;
        }
        
        .modal-content {
            margin: 10px;
            max-height: 95vh;
            overflow-y: auto;
        }
    }
    
    /* Estados y tipos (heredados) */
    .estado-pendiente { background: #fff3cd; color: #856404; }
    .estado-en-proceso { background: #d1ecf1; color: #0c5460; }
    .estado-resuelto { background: #d4edda; color: #155724; }
    
    .tipo-peticion { background: #e3f2fd; color: #1565c0; }
    .tipo-queja { background: #ffebee; color: #c62828; }
    .tipo-reclamo { background: #fff8e1; color: #ef6c00; }
    .tipo-sugerencia { background: #e8f5e8; color: #2e7d32; }
    
    .estado, .tipo {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
        text-transform: uppercase;
    }

/* Sección de adjuntos */
.seccion-adjuntos {
    margin-bottom: 25px;
}

.adjuntos-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.drag-drop-area {
    border: 2px dashed #007bff;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.drag-drop-area:hover,
.drag-drop-area.drag-over {
    border-color: #0056b3;
    background: #f0f8ff;
}

.upload-icon {
    font-size: 48px;
    color: #007bff;
    margin-bottom: 15px;
}

.drag-drop-area p {
    margin: 10px 0;
    font-size: 16px;
    color: #495057;
}

.link-upload {
    color: #007bff;
    cursor: pointer;
    text-decoration: underline;
}

.link-upload:hover {
    color: #0056b3;
}

.drag-drop-area small {
    color: #6c757d;
    font-size: 12px;
}

/* Lista de archivos */
.lista-archivos {
    margin-top: 20px;
}

.lista-archivos h4 {
    margin-bottom: 15px;
    color: #495057;
    font-size: 16px;
}

#archivosSeleccionados {
    background: #fff;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    max-height: 200px;
    overflow-y: auto;
}

.archivo-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #f1f3f4;
}

.archivo-item:last-child {
    border-bottom: none;
}

.archivo-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.archivo-info i {
    font-size: 20px;
    margin-right: 10px;
    width: 25px;
}

.archivo-info .fa-file-pdf { color: #dc3545; }
.archivo-info .fa-file-word { color: #0d6efd; }
.archivo-info .fa-file-excel { color: #198754; }
.archivo-info .fa-file-image { color: #fd7e14; }
.archivo-info .fa-file-alt { color: #6c757d; }

.archivo-info .nombre {
    flex: 1;
    font-weight: 500;
    color: #495057;
    margin-right: 15px;
}

.archivo-info .tamaño {
    font-size: 12px;
    color: #6c757d;
    margin-right: 15px;
}

.btn-eliminar-archivo {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 16px;
    padding: 5px;
    border-radius: 4px;
    transition: background 0.2s;
}

.btn-eliminar-archivo:hover {
    background: #f8d7da;
    color: #721c24;
}

/* Responsive */
@media (max-width: 768px) {
    .drag-drop-area {
        padding: 30px 15px;
    }
    
    .upload-icon {
        font-size: 36px;
    }
    
    .archivo-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .archivo-info {
        width: 100%;
    }
    
    .btn-eliminar-archivo {
        align-self: flex-end;
    }
}

    </style>
</body>
</html>


