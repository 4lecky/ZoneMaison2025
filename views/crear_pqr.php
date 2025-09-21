<?php
// Verificar si el usuario ha iniciado sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el rol del usuario
$rol_usuario = $_SESSION['usuario']['rol'] ?? '';

// Verificar que solo Residente, Propietario y Vigilante puedan acceder
$roles_permitidos = ['Residente', 'Propietario', 'Vigilante'];
if (!in_array($rol_usuario, $roles_permitidos, true)) {
    $_SESSION['error_mensaje'] = 'No tienes permisos para crear PQRS.';
    header("Location: pqrs.php");
    exit();
}

require_once './Layout/header.php';

$usuario = $_SESSION['usuario'];

// **NUEVA FUNCIONALIDAD: Consultar datos completos del usuario desde la BD**
require_once '../config/db.php';

try {
    $stmt = $pdo->prepare("SELECT 
        usuario_cc,
        usu_cedula,
        usu_nombre_completo,
        usu_correo,
        usu_telefono,
        usu_apartamento_residencia,
        usu_torre_residencia,
        usu_rol
        FROM tbl_usuario 
        WHERE usuario_cc = ? AND usu_estado = 'Activo'");
    
    $stmt->execute([$usuario['id']]);
    $usuario_completo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario_completo) {
        $_SESSION['error_mensaje'] = 'No se pudieron cargar los datos del usuario.';
        header("Location: pqrs.php");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error al consultar datos usuario: " . $e->getMessage());
    $_SESSION['error_mensaje'] = 'Error al cargar los datos del usuario.';
    header("Location: pqrs.php");
    exit();
}

// Mapear datos completos de la BD
$usuario_mapeado = [
    'usuario_cc' => $usuario_completo['usuario_cc'],
    'usu_cedula' => $usuario_completo['usu_cedula'],
    'usu_correo' => $usuario_completo['usu_correo'],
    'usu_telefono' => $usuario_completo['usu_telefono'],
    'usu_nombre_completo' => $usuario_completo['usu_nombre_completo'],
    'usu_rol' => $usuario_completo['usu_rol'],
    'usu_apartamento_residencia' => $usuario_completo['usu_apartamento_residencia'],
    'usu_torre_residencia' => $usuario_completo['usu_torre_residencia']
];

// Obtener mensajes de la sesión
$errores = $_SESSION['errores_pqrs'] ?? [];
$mensajeExito = $_SESSION['mensaje_pqrs'] ?? null;

// Limpiar mensajes de la sesión después de obtenerlos
unset($_SESSION['errores_pqrs'], $_SESSION['mensaje_pqrs']);

// Función para separar nombres y apellidos
function separarNombreCompleto($nombreCompleto) {
    $partes = explode(' ', trim($nombreCompleto));
    
    if (count($partes) >= 2) {
        if (count($partes) == 2) {
            return ['nombres' => $partes[0], 'apellidos' => $partes[1]];
        } else {
            // Asumir primeros 2 como nombres, resto como apellidos
            $nombres = $partes[0] . ' ' . $partes[1];
            $apellidos = implode(' ', array_slice($partes, 2));
            return ['nombres' => $nombres, 'apellidos' => $apellidos];
        }
    } else {
        return ['nombres' => $nombreCompleto, 'apellidos' => ''];
    }
}

$nombresSeparados = separarNombreCompleto($usuario_mapeado['usu_nombre_completo']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CREAR PQR</title>
  <link rel="stylesheet" href="../assets/Css/pqrs.css" />
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

  <section class="titulo-principal">
      <div class="decoration"></div>
      <div class="decoration"></div>
      <div class="decoration"></div>
      <div class="decoration"></div>
      <h1>PETICIONES, QUEJAS Y RECLAMOS</h1>
  </section>



  <div class="principal-page">
    <h2>Formulario PQRS</h2>
    <p class="form-subtitle">Por favor, diligencia la siguiente información para procesar tu solicitud.</p>
    <p class="campo-obligatorio">(*) Todos los campos son obligatorios</p>

    <!-- Mostrar MENSAJES DE ERROR del servidor -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger mensaje-servidor" id="mensaje-errores">
            <div class="alert-header">
                <i class="ri-error-warning-fill"></i>
                <strong>Se encontraron los siguientes errores:</strong>
                <button type="button" class="close-alert" onclick="cerrarAlerta('mensaje-errores')">&times;</button>
            </div>
            <ul class="error-list">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Mostrar MENSAJE DE ÉXITO del servidor -->
    <?php if ($mensajeExito && isset($mensajeExito['tipo']) && $mensajeExito['tipo'] === 'success'): ?>
        <div class="alert alert-success mensaje-servidor" id="mensaje-exito">
            <div class="alert-header">
                <i class="ri-check-circle-fill"></i>
                <strong>¡Éxito!</strong>
                <button type="button" class="close-alert" onclick="cerrarAlerta('mensaje-exito')">&times;</button>
            </div>
            <p><?= htmlspecialchars($mensajeExito['texto'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="../controller/pqrsController.php" enctype="multipart/form-data" id="formPQRS">
      
      <!-- Información Personal Pre-llenada -->
      <fieldset>
        <legend>Información Personal</legend>

        <div class="info-usuario-logueado">
          <div class="alert alert-info">
            <i class="ri-user-fill"></i>
            <strong>Solicitud registrada como:</strong> <?= htmlspecialchars($usuario_mapeado['usu_nombre_completo']) ?>
            <br><small>
                <strong>Rol:</strong> <?= htmlspecialchars($usuario_mapeado['usu_rol']) ?> | 
                <strong>Apartamento:</strong> <?= htmlspecialchars($usuario_mapeado['usu_apartamento_residencia']) ?> - Torre <?= htmlspecialchars($usuario_mapeado['usu_torre_residencia']) ?>
            </small>
          </div>
        </div>

        <!-- Campos ocultos -->
        <input type="hidden" name="usuario_cc" value="<?= htmlspecialchars($usuario_mapeado['usuario_cc']) ?>">
        <input type="hidden" name="nombres" value="<?= htmlspecialchars($nombresSeparados['nombres']) ?>">
        <input type="hidden" name="apellidos" value="<?= htmlspecialchars($nombresSeparados['apellidos']) ?>">
        <input type="hidden" name="identificacion" value="<?= htmlspecialchars($usuario_mapeado['usu_cedula']) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($usuario_mapeado['usu_correo']) ?>">
        <input type="hidden" name="telefono" value="<?= htmlspecialchars($usuario_mapeado['usu_telefono']) ?>">
        <!-- Campo oculto para medio de respuesta fijo en correo -->
        <input type="hidden" name="medio_respuesta[]" value="correo">

        <!-- Campos visibles (readonly) -->
        <div class="input-group">
          <div class="input-box">
            <label for="identificacion_display">Número de Identificación</label>
            <input type="text" id="identificacion_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_cedula']) ?>" readonly>
          </div>
          <div class="input-box">
            <label for="nombre_completo_display">Nombre Completo</label>
            <input type="text" id="nombre_completo_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_nombre_completo']) ?>" readonly>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="email_display">Correo Electrónico</label>
            <input type="email" id="email_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_correo']) ?>" readonly>
          </div>
          <div class="input-box">
            <label for="telefono_display">Teléfono de Contacto</label>
            <input type="tel" id="telefono_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_telefono']) ?>" readonly>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="apartamento_display">Apartamento</label>
            <input type="text" id="apartamento_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_apartamento_residencia']) ?> - Torre <?= htmlspecialchars($usuario_mapeado['usu_torre_residencia']) ?>" readonly>
          </div>
          <div class="input-box">
            <label for="rol_display">Rol</label>
            <input type="text" id="rol_display" class="form-control readonly-field" 
                   value="<?= htmlspecialchars($usuario_mapeado['usu_rol']) ?>" readonly>
          </div>
        </div>
      </fieldset>

      <!-- Detalles de la Solicitud -->
      <fieldset>
        <legend>Detalles de la Solicitud</legend>

        <div class="input-group">
          <div class="input-box">
            <label for="tipo_pqr">Tipo de Solicitud *</label>
            <select name="tipo_pqr" id="tipo_pqr" class="form-control" required>
              <option value="" disabled selected>Selecciona el tipo de solicitud</option>
              <option value="peticion">Petición</option>
              <option value="queja">Queja</option>
              <option value="reclamo">Reclamo</option>
              <option value="sugerencia">Sugerencia</option>
            </select>
          </div>
          <div class="input-box">
            <label for="asunto">Asunto *</label>
            <input type="text" name="asunto" id="asunto" class="form-control" 
                   placeholder="Resumen del tema de su solicitud" required maxlength="255" minlength="5">
          </div>
        </div>

        <div class="input-group">
          <div class="input-box textarea-box">
            <label for="mensaje">Descripción Detallada *</label>
            <textarea name="mensaje" id="mensaje" class="form-control textarea-field" 
                      placeholder="Describa su solicitud con el mayor detalle posible..." 
                      required minlength="10" rows="5"></textarea>
          </div>
        </div>

        <div class="archivo-section">
          <label for="archivos">Documentos Anexos (opcional)</label>
          <input type="file" name="archivos[]" id="archivos" multiple 
                 accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control">
          <small class="file-help">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Máximo 5MB por archivo.</small>
        </div>

        <!-- SECCIÓN ELIMINADA: Selección de medio de respuesta -->
        <!-- Ahora se usa campo oculto que siempre envía 'correo' -->
        <div class="medios-respuesta-info">
          <div class="alert alert-info">
            <i class="ri-mail-line"></i>
            <strong>Medio de respuesta:</strong> Recibirás la respuesta por <strong>correo electrónico</strong> 
            a la dirección: <strong><?= htmlspecialchars($usuario_mapeado['usu_correo']) ?></strong>
          </div>
        </div>
      </fieldset>

      <!-- Botón de envío -->
      <div class="form-actions">
        <button type="submit" id="btn-enviar" class="btn-primary">
          <i class="ri-send-plane-fill"></i>
          <span id="btn-text">Enviar Solicitud</span>
        </button>
        <button type="reset" class="btn-secondary">
          <i class="ri-refresh-line"></i>
          Limpiar Formulario
        </button>
      </div>
    </form>
  </div>

  <!-- JavaScript para manejar el formulario -->
  <script>
    // Función para cerrar alertas
    function cerrarAlerta(id) {
      const alerta = document.getElementById(id);
      if (alerta) {
        alerta.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => {
          alerta.remove();
        }, 300);
      }
    }

    // Auto-cerrar mensajes después de cierto tiempo
    setTimeout(() => {
      const alertas = document.querySelectorAll('.mensaje-servidor');
      alertas.forEach(alerta => {
        if (alerta.id === 'mensaje-exito') {
          cerrarAlerta(alerta.id);
        }
      });
    }, 5000);

    // Manejar envío del formulario
    document.getElementById('formPQRS').addEventListener('submit', function(e) {
      const btnEnviar = document.getElementById('btn-enviar');
      const btnText = document.getElementById('btn-text');
      
      // Deshabilitar botón y cambiar texto
      btnEnviar.disabled = true;
      btnEnviar.classList.add('btn-loading');
      btnText.textContent = 'Enviando...';
      
      // Agregar ícono de carga
      const icon = btnEnviar.querySelector('i');
      icon.className = 'ri-loader-4-line ri-spin';
    });

    // Validación en tiempo real
    document.getElementById('asunto').addEventListener('input', function() {
      const valor = this.value.trim();
      if (valor.length < 5) {
        this.setCustomValidity('El asunto debe tener al menos 5 caracteres');
      } else {
        this.setCustomValidity('');
      }
    });

    document.getElementById('mensaje').addEventListener('input', function() {
      const valor = this.value.trim();
      if (valor.length < 10) {
        this.setCustomValidity('La descripción debe tener al menos 10 caracteres');
      } else {
        this.setCustomValidity('');
      }
    });
  </script>

  <style>
    /* Estilos para alertas mejorados */
    .alert {
        padding: 15px;
        margin: 20px 0;
        border-radius: 8px;
        border: 1px solid transparent;
        position: relative;
        animation: slideDown 0.3s ease-out;
    }

    .alert-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .alert-header strong {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-alert {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .close-alert:hover {
        opacity: 1;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    .error-list {
        margin: 0;
        padding-left: 20px;
    }

    .error-list li {
        margin: 5px 0;
    }

    /* Campos readonly */
    .readonly-field {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6;
        color: #495057;
        cursor: not-allowed;
    }

    /* Formulario */
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .input-group {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .input-box {
        flex: 1;
    }

    .textarea-box {
        flex: 1;
        width: 100%;
    }

    .textarea-field {
        min-height: 120px;
        resize: vertical;
    }

    /* Fieldsets */
    fieldset {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    legend {
        font-weight: 600;
        padding: 0 10px;
        color: #333;
        font-size: 18px;
    }

    /* Labels */
    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #555;
    }

    /* Archivos */
    .archivo-section {
        margin: 25px 0;
    }

    .file-help {
        display: block;
        margin-top: 6px;
        color: #6c757d;
        font-size: 14px;
    }

    /* Medios de respuesta */
    .medios-respuesta {
        margin: 25px 0;
    }

    .medios-label {
        font-weight: 500;
        margin-bottom: 15px;
        color: #333;
    }

    .checkbox-group {
        display: flex;
        gap: 25px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 16px;
        position: relative;
        padding-left: 30px;
    }

    .checkbox-item input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark {
        position: absolute;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #fff;
        border: 2px solid #ddd;
        border-radius: 4px;
        transition: all 0.3s;
    }

    .checkbox-item:hover input ~ .checkmark {
        border-color: #007bff;
    }

    .checkbox-item input:checked ~ .checkmark {
        background-color: #007bff;
        border-color: #007bff;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .checkbox-item input:checked ~ .checkmark:after {
        display: block;
    }

    .checkbox-text {
        margin-left: 10px;
        user-select: none;
    }

    /* Botones */
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn-primary, .btn-secondary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        min-width: 160px;
        justify-content: center;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .btn-primary:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-loading {
        opacity: 0.8;
    }

    /* Animaciones */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .ri-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .input-group {
            flex-direction: column;
        }
        
        .checkbox-group {
            flex-direction: column;
            gap: 15px;
        }
        
        .form-actions {
            flex-direction: column;
            align-items: center;
        }
    }

    /* Campo obligatorio */
    .campo-obligatorio {
        color: #7b9a82;
        font-size: 14px;
        margin-bottom: 20px;
        font-style: italic;
    }

    .form-subtitle {
        color: #666;
        margin-bottom: 10px;
    }

    /* Validación visual */
    .form-control:invalid {
        border-color: #7b9a82;
    }

    .form-control:valid {
        border-color: #28a745;
    }
  </style>

</body>

<?php
require_once './Layout/footer.php'
?>

</html>