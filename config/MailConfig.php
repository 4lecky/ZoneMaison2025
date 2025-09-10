<?php
/**
 * Configuración de correo para el sistema PQRS - VERSIÓN CORREGIDA PARA ADJUNTOS
 * config/MailConfig.php
 */

// Intentar cargar PHPMailer usando diferentes métodos
$phpmailerLoaded = false;

// Método 1: Composer autoload
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpmailerLoaded = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
}

// Método 2: Inclusión directa (si está en vendor)
if (!$phpmailerLoaded && file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    $phpmailerLoaded = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
}

// Método 3: Si PHPMailer está en otra ubicación
if (!$phpmailerLoaded) {
    $possiblePaths = [
        __DIR__ . '/../PHPMailer/src/PHPMailer.php',
        __DIR__ . '/PHPMailer/src/PHPMailer.php',
        dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $basePath = dirname($path);
            require_once $basePath . '/PHPMailer.php';
            require_once $basePath . '/SMTP.php';
            require_once $basePath . '/Exception.php';
            $phpmailerLoaded = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
            break;
        }
    }
}

class MailConfig {
    
    // Configuración SMTP
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_SECURE = 'tls';
    const SMTP_AUTH = true;
    
    // Credenciales
    const SMTP_USERNAME = 'zonemaizon2025@gmail.com';
    const SMTP_PASSWORD = 'zzgovbvvgqrirzdh';
    
    // Información del remitente
    const FROM_EMAIL = 'zonemaizon2025@gmail.com';
    const FROM_NAME = 'Conjunto Zona Maisons';
    const REPLY_TO = 'zonemaizon2025@gmail.com';
    
    /**
     * Obtener configuración SMTP como array
     */
    public static function getSmtpConfig() {
        return [
            'host' => self::SMTP_HOST,
            'port' => self::SMTP_PORT,
            'secure' => self::SMTP_SECURE,
            'auth' => self::SMTP_AUTH,
            'username' => self::SMTP_USERNAME,
            'password' => self::SMTP_PASSWORD,
            'from_email' => self::FROM_EMAIL,
            'from_name' => self::FROM_NAME,
            'reply_to' => self::REPLY_TO
        ];
    }
}

/**
 * Clase para manejar envío de correos - VERSIÓN CORREGIDA PARA ADJUNTOS
 */
class MailService {
    private $config;
    private $phpmailerAvailable;
    
    public function __construct() {
        $this->config = MailConfig::getSmtpConfig();
        $this->phpmailerAvailable = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
        
        error_log("MailService iniciado - PHPMailer disponible: " . ($this->phpmailerAvailable ? 'SÍ' : 'NO'));
    }
    
    /**
     * MÉTODO PRINCIPAL CORREGIDO: Enviar notificación PQRS con adjuntos
     */
    public function enviarNotificacionPqrsConAdjuntos($pqrsData, $tipoNotificacion = 'respuesta') {
        try {
            error_log("=== ENVIAR NOTIFICACIÓN PQRS CON ADJUNTOS ===");
            
            if (empty($pqrsData['email'])) {
                throw new Exception('Email del destinatario no disponible');
            }
            
            $destinatario = [
                'email' => $pqrsData['email'],
                'nombre' => trim(($pqrsData['nombres'] ?? '') . ' ' . ($pqrsData['apellidos'] ?? ''))
            ];
            
            // Verificar adjuntos recibidos
            $adjuntos = $pqrsData['adjuntos'] ?? [];
            error_log("Adjuntos recibidos: " . count($adjuntos));
            
            // Validar adjuntos antes del envío
            $adjuntosValidos = $this->validarAdjuntosParaCorreo($adjuntos);
            error_log("Adjuntos válidos después de validación: " . count($adjuntosValidos));
            
            // Generar contenido del correo
            if ($tipoNotificacion === 'respuesta') {
                $asunto = "Respuesta a su PQRS - Radicado: PQRS-" . date('Y') . "-" . str_pad($pqrsData['id'], 4, '0', STR_PAD_LEFT);
                $cuerpo = $this->generarHtmlRespuesta($pqrsData, $adjuntosValidos);
            } else {
                $asunto = "PQRS Recibida - Radicado: PQRS-" . date('Y') . "-" . str_pad($pqrsData['id'], 4, '0', STR_PAD_LEFT);
                $cuerpo = $this->generarHtmlCreacion($pqrsData);
            }
            
            // Enviar correo con adjuntos
            $resultado = $this->enviarCorreoConAdjuntos($destinatario, $asunto, $cuerpo, true, $adjuntosValidos);
            
            error_log("Resultado final envío: " . ($resultado ? 'EXITOSO' : 'FALLIDO'));
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en enviarNotificacionPqrsConAdjuntos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * MÉTODO CRÍTICO CORREGIDO: Enviar correo con adjuntos usando PHPMailer
     */
    private function enviarCorreoConAdjuntos($destinatario, $asunto, $cuerpo, $esHtml = true, $adjuntos = []) {
        try {
            error_log("=== ENVIANDO CORREO CON ADJUNTOS ===");
            error_log("Destinatario: " . $destinatario['email']);
            error_log("Adjuntos a procesar: " . count($adjuntos));
            
            // Validar datos básicos
            if (empty($destinatario['email']) || empty($asunto) || empty($cuerpo)) {
                throw new Exception('Datos incompletos para envío de correo');
            }
            
            // Verificar PHPMailer
            if (!$this->phpmailerAvailable) {
                error_log("ERROR: PHPMailer no disponible");
                
                // Solo hacer fallback SIN adjuntos
                if (!empty($adjuntos)) {
                    throw new Exception('No se pueden enviar adjuntos sin PHPMailer');
                }
                
                return $this->enviarCorreoNativo($destinatario, $asunto, $cuerpo, $esHtml);
            }
            
            // Crear instancia PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = $this->config['auth'];
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->CharSet = 'UTF-8';
            
            // Configuración del mensaje
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($destinatario['email'], $destinatario['nombre'] ?? '');
            $mail->addReplyTo($this->config['reply_to'], $this->config['from_name']);
            
            $mail->isHTML($esHtml);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpo;
            
            if ($esHtml) {
                $mail->AltBody = strip_tags($cuerpo);
            }
            
            // PROCESAR ADJUNTOS - PARTE CRÍTICA CORREGIDA
            $adjuntosAgregados = 0;
            if (!empty($adjuntos)) {
                error_log("Procesando " . count($adjuntos) . " adjuntos...");
                
                foreach ($adjuntos as $i => $adjunto) {
                    $nombreOriginal = $adjunto['nombre_original'] ?? "archivo_$i";
                    $rutaArchivo = $adjunto['ruta'] ?? '';
                    
                    error_log("Procesando adjunto: $nombreOriginal");
                    error_log("Ruta: $rutaArchivo");
                    
                    // Verificaciones críticas
                    if (empty($rutaArchivo)) {
                        error_log("ERROR: Ruta vacía para $nombreOriginal");
                        continue;
                    }
                    
                    if (!file_exists($rutaArchivo)) {
                        error_log("ERROR: Archivo no existe: $rutaArchivo");
                        continue;
                    }
                    
                    if (!is_readable($rutaArchivo)) {
                        error_log("ERROR: Archivo no legible: $rutaArchivo");
                        continue;
                    }
                    
                    $tamañoArchivo = filesize($rutaArchivo);
                    if ($tamañoArchivo === false || $tamañoArchivo === 0) {
                        error_log("ERROR: Archivo vacío o no se puede leer el tamaño: $rutaArchivo");
                        continue;
                    }
                    
                    error_log("Archivo válido - Tamaño: $tamañoArchivo bytes");
                    
                    // Agregar adjunto a PHPMailer
                    try {
                        $resultadoAdjunto = $mail->addAttachment($rutaArchivo, $nombreOriginal);
                        
                        if ($resultadoAdjunto) {
                            $adjuntosAgregados++;
                            error_log("✅ Adjunto agregado exitosamente: $nombreOriginal");
                        } else {
                            error_log("❌ addAttachment retornó false para: $nombreOriginal");
                        }
                        
                    } catch (Exception $e) {
                        error_log("❌ Excepción agregando adjunto $nombreOriginal: " . $e->getMessage());
                    }
                }
                
                error_log("Total adjuntos agregados a PHPMailer: $adjuntosAgregados");
            }
            
            // Verificar adjuntos finales en PHPMailer
            $attachmentsPHPMailer = $mail->getAttachments();
            error_log("Adjuntos finales en PHPMailer antes del envío: " . count($attachmentsPHPMailer));
            
            // Enviar correo
            error_log("Intentando enviar correo...");
            $resultado = $mail->send();
            
            if ($resultado) {
                $mensaje = "Correo enviado exitosamente a: " . $destinatario['email'];
                if ($adjuntosAgregados > 0) {
                    $mensaje .= " con $adjuntosAgregados adjunto(s)";
                }
                error_log("✅ " . $mensaje);
                return true;
            } else {
                error_log("❌ Error enviando correo: " . $mail->ErrorInfo);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Excepción en enviarCorreoConAdjuntos: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * NUEVO MÉTODO: Validar adjuntos antes del envío
     */
    private function validarAdjuntosParaCorreo($adjuntos) {
        $adjuntosValidos = [];
        
        if (empty($adjuntos) || !is_array($adjuntos)) {
            error_log("No hay adjuntos para validar");
            return $adjuntosValidos;
        }
        
        error_log("Validando " . count($adjuntos) . " adjuntos...");
        
        foreach ($adjuntos as $i => $adjunto) {
            $nombreOriginal = $adjunto['nombre_original'] ?? "archivo_$i";
            $rutaArchivo = $adjunto['ruta'] ?? '';
            
            error_log("Validando adjunto: $nombreOriginal");
            
            // Verificaciones básicas
            if (empty($rutaArchivo)) {
                error_log("  ❌ Ruta vacía");
                continue;
            }
            
            if (!file_exists($rutaArchivo)) {
                error_log("  ❌ Archivo no existe: $rutaArchivo");
                continue;
            }
            
            if (!is_readable($rutaArchivo)) {
                error_log("  ❌ Archivo no legible");
                continue;
            }
            
            $tamañoArchivo = filesize($rutaArchivo);
            if ($tamañoArchivo === false || $tamañoArchivo === 0) {
                error_log("  ❌ Archivo vacío o error leyendo tamaño");
                continue;
            }
            
            // Verificar tipo MIME
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $rutaArchivo);
                finfo_close($finfo);
                
                if ($mimeType && $mimeType !== $adjunto['tipo']) {
                    error_log("  ⚠️ MIME type inconsistente: esperado {$adjunto['tipo']}, detectado $mimeType");
                    // Actualizar tipo MIME real
                    $adjunto['tipo'] = $mimeType;
                }
            }
            
            error_log("  ✅ Adjunto válido - Tamaño: $tamañoArchivo bytes");
            $adjuntosValidos[] = $adjunto;
        }
        
        error_log("Adjuntos válidos: " . count($adjuntosValidos) . " de " . count($adjuntos));
        return $adjuntosValidos;
    }
    
    /**
     * Método fallback para correo sin adjuntos
     */
    private function enviarCorreoNativo($destinatario, $asunto, $cuerpo, $esHtml = true) {
        try {
            error_log("Enviando correo con método nativo (sin adjuntos)");
            
            // Configurar headers
            $headers = [];
            $headers[] = "From: {$this->config['from_name']} <{$this->config['from_email']}>";
            $headers[] = "Reply-To: {$this->config['reply_to']}";
            $headers[] = "Return-Path: {$this->config['from_email']}";
            $headers[] = "X-Mailer: PHP/" . phpversion();
            $headers[] = "X-Priority: 3";
            $headers[] = "MIME-Version: 1.0";
            
            if ($esHtml) {
                $headers[] = "Content-Type: text/html; charset=UTF-8";
                $headers[] = "Content-Transfer-Encoding: quoted-printable";
                $cuerpo = quoted_printable_encode($cuerpo);
            } else {
                $headers[] = "Content-Type: text/plain; charset=UTF-8";
                $headers[] = "Content-Transfer-Encoding: 8bit";
            }
            
            $headersString = implode("\r\n", $headers);
            
            $resultado = mail($destinatario['email'], $asunto, $cuerpo, $headersString);
            
            if ($resultado) {
                error_log("✅ Correo enviado con mail() nativo");
            } else {
                error_log("❌ Error enviando con mail() nativo");
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error método nativo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * MÉTODO DEBUG COMPLETO: Para troubleshooting
     */
    public function enviarNotificacionPqrsConAdjuntosDebug($pqrsData, $tipoNotificacion = 'respuesta') {
        error_log("=== MAILSERVICE DEBUG: INICIO ===");
        
        try {
            $destinatario = [
                'email' => $pqrsData['email'],
                'nombre' => trim(($pqrsData['nombres'] ?? '') . ' ' . ($pqrsData['apellidos'] ?? ''))
            ];
            
            error_log("Destinatario: " . $destinatario['email'] . " (" . $destinatario['nombre'] . ")");
            
            // VERIFICAR ADJUNTOS RECIBIDOS
            error_log("=== ADJUNTOS RECIBIDOS EN MAILSERVICE ===");
            $adjuntosOriginales = $pqrsData['adjuntos'] ?? [];
            error_log("Total adjuntos recibidos: " . count($adjuntosOriginales));
            
            foreach ($adjuntosOriginales as $i => $adj) {
                error_log("Adjunto recibido $i:");
                error_log("  - Nombre: " . ($adj['nombre_original'] ?? 'N/A'));
                error_log("  - Ruta: " . ($adj['ruta'] ?? 'N/A'));
                error_log("  - Tipo: " . ($adj['tipo'] ?? 'N/A'));
                error_log("  - Tamaño declarado: " . ($adj['tamaño'] ?? 'N/A'));
                
                if (isset($adj['ruta'])) {
                    $existe = file_exists($adj['ruta']);
                    $legible = is_readable($adj['ruta']);
                    error_log("  - Existe: " . ($existe ? 'SÍ' : 'NO'));
                    error_log("  - Legible: " . ($legible ? 'SÍ' : 'NO'));
                    
                    if ($existe) {
                        $tamañoReal = filesize($adj['ruta']);
                        error_log("  - Tamaño real: $tamañoReal bytes");
                        
                        if (function_exists('finfo_open')) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeReal = finfo_file($finfo, $adj['ruta']);
                            finfo_close($finfo);
                            error_log("  - MIME real: $mimeReal");
                        }
                    }
                }
            }
            
            // VALIDAR ADJUNTOS
            error_log("=== VALIDANDO ADJUNTOS ===");
            $adjuntosValidados = $this->validarAdjuntosParaCorreo($adjuntosOriginales);
            
            // VERIFICAR PHPMAILER
            error_log("=== VERIFICANDO PHPMAILER ===");
            error_log("PHPMailer disponible: " . ($this->phpmailerAvailable ? 'SÍ' : 'NO'));
            
            if (!$this->phpmailerAvailable) {
                throw new Exception('PHPMailer no disponible');
            }
            
            // GENERAR CONTENIDO DEL CORREO PRIMERO
            if ($tipoNotificacion === 'respuesta') {
                $asunto = "Respuesta a su PQRS - Radicado: PQRS-" . date('Y') . "-" . str_pad($pqrsData['id'], 4, '0', STR_PAD_LEFT);
                $cuerpo = $this->generarHtmlRespuesta($pqrsData, $adjuntosValidados);
            } else {
                $asunto = "PQRS Recibida - Radicado: PQRS-" . date('Y') . "-" . str_pad($pqrsData['id'], 4, '0', STR_PAD_LEFT);
                $cuerpo = $this->generarHtmlCreacion($pqrsData);
            }
            $esHtml = true;
            
            error_log("Asunto generado: $asunto");
            error_log("Cuerpo generado: " . (strlen($cuerpo) > 100 ? "SÍ (" . strlen($cuerpo) . " chars)" : "Muy corto"));
            
            // CREAR Y CONFIGURAR PHPMAILER
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración SMTP con timeouts extendidos
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = $this->config['auth'];
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 300; // 5 minutos para adjuntos grandes
            $mail->SMTPKeepAlive = true;
            
            // Debug SMTP activado
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP Debug [$level]: $str");
            };
            
            error_log("✅ PHPMailer configurado");
            
            // Configurar mensaje
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($destinatario['email'], $destinatario['nombre'] ?? '');
            $mail->addReplyTo($this->config['reply_to'], $this->config['from_name']);
            
            $mail->isHTML($esHtml);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpo;
            
            if ($esHtml) {
                $mail->AltBody = strip_tags($cuerpo);
            }
            
            error_log("✅ Mensaje configurado");
            
            // AGREGAR ADJUNTOS - PROCESO CRÍTICO
            error_log("=== AGREGANDO ADJUNTOS A PHPMAILER ===");
            $adjuntosAgregados = 0;
            $erroresAdjuntos = [];
            
            foreach ($adjuntosValidados as $i => $adjunto) {
                error_log("--- Agregando adjunto $i: {$adjunto['nombre_original']} ---");
                
                try {
                    // Verificación final antes de agregar
                    if (!file_exists($adjunto['ruta'])) {
                        throw new Exception("Archivo desapareció: {$adjunto['ruta']}");
                    }
                    
                    // IMPORTANTE: Usar la ruta absoluta real
                    $rutaReal = realpath($adjunto['ruta']);
                    if ($rutaReal === false) {
                        throw new Exception("No se puede resolver ruta absoluta: {$adjunto['ruta']}");
                    }
                    
                    error_log("  - Ruta real: $rutaReal");
                    
                    // Agregar a PHPMailer
                    $resultadoAdjunto = $mail->addAttachment(
                        $rutaReal, 
                        $adjunto['nombre_original'],
                        'base64',
                        $adjunto['tipo'] ?? 'application/octet-stream'
                    );
                    
                    if ($resultadoAdjunto) {
                        $adjuntosAgregados++;
                        error_log("  - ✅ ADJUNTO AGREGADO EXITOSAMENTE");
                    } else {
                        throw new Exception("addAttachment retornó false");
                    }
                    
                } catch (Exception $e) {
                    $error = "Error: " . $e->getMessage();
                    error_log("  - ❌ $error");
                    $erroresAdjuntos[] = "{$adjunto['nombre_original']}: $error";
                }
            }
            
            // VERIFICACIÓN FINAL
            $attachmentsFinal = $mail->getAttachments();
            error_log("=== VERIFICACIÓN FINAL ===");
            error_log("Adjuntos originales: " . count($adjuntosOriginales));
            error_log("Adjuntos validados: " . count($adjuntosValidados));
            error_log("Adjuntos agregados: $adjuntosAgregados");
            error_log("Adjuntos en PHPMailer: " . count($attachmentsFinal));
            error_log("Errores: " . count($erroresAdjuntos));
            
            if (!empty($erroresAdjuntos)) {
                error_log("Detalles errores:");
                foreach ($erroresAdjuntos as $error) {
                    error_log("  - $error");
                }
            }
            
            // ENVÍO FINAL
            error_log("=== ENVIANDO CORREO ===");
            $resultado = $mail->send();
            
            if ($resultado) {
                error_log("✅ CORREO ENVIADO EXITOSAMENTE");
                if ($adjuntosAgregados > 0) {
                    error_log("✅ CON $adjuntosAgregados ADJUNTO(S)");
                }
            } else {
                error_log("❌ ERROR EN ENVÍO: " . $mail->ErrorInfo);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("❌ EXCEPCIÓN EN DEBUG: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar HTML para notificación de creación
     */
    private function generarHtmlCreacion($pqrs) {
        $radicado = 'PQRS-' . date('Y') . '-' . str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                .container { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .info-box { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
                .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>PQRS Recibida</h1>
                    <h2>Conjunto Zona Maisons</h2>
                </div>
                <div class='content'>
                    <p>Estimado/a <strong>{$pqrs['nombres']} {$pqrs['apellidos']}</strong>,</p>
                    
                    <p>Su solicitud ha sido recibida exitosamente en nuestro sistema.</p>
                    
                    <div class='info-box'>
                        <h3>Información de su solicitud:</h3>
                        <p><strong>Número de Radicado:</strong> {$radicado}</p>
                        <p><strong>Tipo:</strong> " . ucfirst($pqrs['tipo_pqr']) . "</p>
                        <p><strong>Asunto:</strong> {$pqrs['asunto']}</p>
                        <p><strong>Fecha:</strong> " . date('d/m/Y H:i', strtotime($pqrs['fecha_creacion'])) . "</p>
                        <p><strong>Estado:</strong> Pendiente de revisión</p>
                    </div>
                    
                    <p><strong>Descripción:</strong></p>
                    <div class='info-box'>
                        " . nl2br(htmlspecialchars($pqrs['mensaje'])) . "
                    </div>
                    
                    <p>Le informamos que:</p>
                    <ul>
                        <li>Su solicitud será revisada por nuestro equipo administrativo</li>
                        <li>Recibirá una respuesta por correo electrónico</li>
                        <li>El tiempo estimado de respuesta es de 5 a 10 días hábiles</li>
                        <li>Puede hacer seguimiento con el número de radicado: <strong>{$radicado}</strong></li>
                    </ul>
                </div>
                <div class='footer'>
                    <p>Conjunto Zona Maisons - Sistema de PQRS</p>
                    <p>Este es un mensaje automático, por favor no responder a este correo.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * MÉTODO CORREGIDO: Generar HTML para respuesta con adjuntos
     */
    private function generarHtmlRespuesta($pqrs, $adjuntosValidados = []) {
        $radicado = 'PQRS-' . date('Y') . '-' . str_pad($pqrs['id'], 4, '0', STR_PAD_LEFT);
        
        // Preparar sección de adjuntos si existen
        $seccionAdjuntos = '';
        if (!empty($adjuntosValidados)) {
            $seccionAdjuntos = "
                <div class='info-box' style='background-color: #f0f8ff; border-left: 4px solid #2196F3;'>
                    <h4>📎 Archivos adjuntos incluidos en esta respuesta:</h4>
                    <ul style='margin: 10px 0; padding-left: 20px;'>";
            
            foreach ($adjuntosValidados as $adjunto) {
                $tamaño = $this->formatearTamaño($adjunto['tamaño'] ?? 0);
                $icono = $this->obtenerIconoTexto($adjunto['tipo'] ?? '');
                $seccionAdjuntos .= "<li style='margin: 5px 0;'>$icono <strong>{$adjunto['nombre_original']}</strong> <small>($tamaño)</small></li>";
            }
            
            $seccionAdjuntos .= "
                    </ul>
                    <small><em>Los archivos se encuentran adjuntos a este correo y pueden descargarse directamente.</em></small>
                </div>";
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                .container { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; line-height: 1.6; }
                .header { background: linear-gradient(135deg, #2196F3, #1976D2); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .info-box { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; border-radius: 4px; }
                .respuesta-box { background-color: #e8f5e8; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; border-radius: 4px; }
                .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
                h1, h2, h3 { margin-top: 0; }
                ul { margin: 5px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Respuesta a su PQRS</h1>
                    <h2>Conjunto Zona Maisons</h2>
                </div>
                <div class='content'>
                    <p>Estimado/a <strong>{$pqrs['nombres']} {$pqrs['apellidos']}</strong>,</p>
                    
                    <p>Hemos procesado su solicitud y queremos compartir nuestra respuesta.</p>
                    
                    <div class='info-box'>
                        <h3>Información de su solicitud original:</h3>
                        <p><strong>Número de Radicado:</strong> {$radicado}</p>
                        <p><strong>Tipo:</strong> " . ucfirst($pqrs['tipo_pqr']) . "</p>
                        <p><strong>Asunto:</strong> {$pqrs['asunto']}</p>
                        <p><strong>Fecha de solicitud:</strong> " . date('d/m/Y H:i', strtotime($pqrs['fecha_creacion'])) . "</p>
                        <p><strong>Fecha de respuesta:</strong> " . date('d/m/Y H:i') . "</p>
                        <p><strong>Estado:</strong> <span style='color: #4CAF50; font-weight: bold;'>Resuelto</span></p>
                    </div>
                    
                    <h3>Respuesta de la administración:</h3>
                    <div class='respuesta-box'>
                        " . nl2br(htmlspecialchars($pqrs['respuesta'])) . "
                    </div>
                    
                    {$seccionAdjuntos}
                    
                    <div class='info-box' style='background-color: #fff8e1; border-left: 4px solid #FFC107;'>
                        <p><strong>Información importante:</strong></p>
                        <ul>
                            <li>Si tiene alguna pregunta adicional sobre esta respuesta, puede comunicarse con nosotros</li>
                            <li>Conserve este correo como comprobante de la gestión realizada</li>
                            <li>Su solicitud ha sido marcada como resuelta en nuestro sistema</li>
                        </ul>
                    </div>
                    
                    <p>Gracias por utilizar nuestro sistema de PQRS.</p>
                    
                    <p style='margin-top: 20px;'><em>Atentamente,<br>
                    <strong>Administración Conjunto Zona Maisons</strong></em></p>
                </div>
                <div class='footer'>
                    <p>Conjunto Zona Maisons - Sistema de PQRS</p>
                    <p>Este es un mensaje automático. Para nuevas consultas, utilice el sistema de PQRS.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * MÉTODO HELPER: Formatear tamaño de archivo
     */
    private function formatearTamaño($bytes) {
        if ($bytes == 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
    }
    
    /**
     * MÉTODO HELPER: Obtener icono de texto según tipo MIME
     */
    private function obtenerIconoTexto($tipoMime) {
        $iconos = [
            'application/pdf' => '📄',
            'application/msword' => '📝',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
            'application/vnd.ms-excel' => '📊',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '📊',
            'application/vnd.ms-powerpoint' => '📽️',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '📽️',
            'image/jpeg' => '🖼️',
            'image/png' => '🖼️',
            'image/gif' => '🖼️',
            'text/plain' => '📄',
            'text/csv' => '📊',
            'application/zip' => '🗜️'
        ];
        
        return $iconos[$tipoMime] ?? '📎';
    }
    
    /**
     * MÉTODO PARA TESTING: Verificar configuración completa
     */
    public function verificarConfiguracionCompleta() {
        error_log("=== VERIFICACIÓN CONFIGURACIÓN COMPLETA ===");
        
        $config = [
            'php_version' => phpversion(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'openssl' => extension_loaded('openssl'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo'),
            'phpmailer_class' => class_exists('PHPMailer\\PHPMailer\\PHPMailer'),
            'uploads_writable' => is_writable('../uploads/'),
            'smtp_host' => $this->config['host'],
            'smtp_username' => !empty($this->config['username']) ? 'CONFIGURADO' : 'VACÍO'
        ];
        
        error_log("Configuración: " . print_r($config, true));
        return $config;
    }
    
    /**
     * MÉTODO PARA TESTING: Enviar correo de prueba CON adjunto
     */
    public function enviarCorreoPrueba($emailDestino, $rutaAdjunto = null, $nombreAdjunto = 'test.txt') {
        error_log("=== ENVIANDO CORREO DE PRUEBA ===");
        error_log("Destino: $emailDestino");
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración básica
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = $this->config['auth'];
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->CharSet = 'UTF-8';
            
            // Configurar mensaje de prueba
            $mail->setFrom($this->config['from_email'], 'Test Adjuntos PQRS');
            $mail->addAddress($emailDestino);
            $mail->isHTML(true);
            $mail->Subject = 'TEST - Adjuntos PQRS ' . date('H:i:s');
            $mail->Body = '<h3>Correo de prueba para adjuntos PQRS</h3><p>Si recibe este correo con el adjunto, el sistema funciona correctamente.</p>';
            
            // Agregar adjunto de prueba si se proporciona
            if ($rutaAdjunto && file_exists($rutaAdjunto)) {
                error_log("Agregando adjunto de prueba: $rutaAdjunto");
                $mail->addAttachment($rutaAdjunto, $nombreAdjunto);
            }
            
            // Enviar
            $resultado = $mail->send();
            
            if ($resultado) {
                error_log("✅ CORREO DE PRUEBA ENVIADO EXITOSAMENTE");
            } else {
                error_log("❌ Error enviando correo de prueba: " . $mail->ErrorInfo);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("❌ Excepción en correo de prueba: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar configuración de correo
     */
    public function verificarConfiguracion() {
        $status = [
            'phpmailer_disponible' => $this->phpmailerAvailable,
            'configuracion_completa' => !empty($this->config['host']) && !empty($this->config['username']),
            'mail_function_disponible' => function_exists('mail'),
            'openssl_disponible' => extension_loaded('openssl'),
            'socket_disponible' => function_exists('fsockopen')
        ];
        
        error_log("MailService - Status de configuración: " . json_encode($status));
        return $status;
    }
}

/**
 * FUNCIÓN HELPER: Verificar que PHPMailer esté disponible
 */
function verificarPHPMailer() {
    $disponible = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
    $detalles = [
        'phpmailer_disponible' => $disponible,
        'ubicacion_clase' => $disponible ? 'Cargada correctamente' : 'No encontrada',
        'extensiones_requeridas' => [
            'openssl' => extension_loaded('openssl'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo')
        ]
    ];
    
    error_log("Verificación PHPMailer: " . json_encode($detalles));
    return $detalles;
}

// Verificar carga de PHPMailer al cargar el archivo
if (defined('DEBUG_MAIL') || isset($_GET['debug_mail'])) {
    verificarPHPMailer();
}

?>