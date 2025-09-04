<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
require_once "./Layout/header.php";

//dividir por roles
$rol = $_SESSION['usuario']['rol'] ?? '';
if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
    <td class="contenedorBotones">
    <?php endif;

// Recuperar mensaje de éxito
$mensaje = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';

// Obtener mensajes del muro
$mensajes = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM tbl_muro ORDER BY muro_Fecha DESC, muro_Hora DESC");
    $stmt->execute();
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Mostrar los datos obtenidos
    echo "<!-- Debug: Mensajes obtenidos: " . count($mensajes) . " -->";
    if (!empty($mensajes)) {
        echo "<!-- Debug: Primer mensaje: ";
        echo "Asunto: " . $mensajes[0]['muro_Asunto'] . ", ";
        echo "EnviaHora: " . ($mensajes[0]['muro_EnviaHora'] ?? 'NULL') . " -->";
    }
} catch (PDOException $e) {
    error_log("Error al obtener mensajes del muro: " . $e->getMessage());
}

// roles
$rol_usuario = $_SESSION['usuario']['rol'];

if ($rol_usuario === 'Administrador' || $rol_usuario === 'Vigilante') {
    // Administrador ve todas las publicaciones
    $query = "SELECT * FROM tbl_muro ORDER BY muro_Fecha DESC, muro_Hora DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
} else {
    // Los demás usuarios solo ven publicaciones dirigidas a su rol
    $query = "SELECT * FROM tbl_muro 
              WHERE muro_Destinatario = :rol 
                 OR muro_Destinatario = 'Todos'
              ORDER BY muro_Fecha DESC, muro_Hora DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['rol' => $rol_usuario]);
}

$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener paquetes generales
$paquetes = [];
$hay_paquetes = false;

try {
    $stmt = $pdo->prepare("SELECT * FROM tbl_paquetes WHERE paqu_estado IN ('Entregado', 'Pendiente') ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC");
    $stmt->execute();
    $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hay_paquetes = count($paquetes) > 0;
} catch (PDOException $e) {
    error_log("Error al obtener paquetes: " . $e->getMessage());
}

// Verificar si el usuario está logueado
if (isset($_SESSION['usuario']['cedula'])) {
    $usuario_cedula = $_SESSION['usuario']['cedula'];

    // Comprobar el rol del usuario (Administrador o Vigilante)
    $usuario_rol = $_SESSION['usuario']['rol'];

    if ($usuario_rol == 'Administrador' || $usuario_rol == 'Vigilante') {
        // Si es Administrador o Vigilante, mostrar todos los paquetes
        $query = "SELECT * FROM tbl_paquetes WHERE paqu_estado IN ('Entregado', 'Pendiente') ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC";
    } else {
        // Si es usuario regular, mostrar solo los paquetes de su cédula
        $query = "SELECT * FROM tbl_paquetes WHERE paqu_usuario_cedula = :cedula ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC";
    }

    // Ejecutar la consulta
    $stmt = $pdo->prepare($query);

    // Si es un usuario regular, pasamos la cédula
    if ($usuario_rol != 'Administrador' && $usuario_rol != 'Vigilante') {
        $stmt->execute(['cedula' => $usuario_cedula]);
    } else {
        $stmt->execute();
    }

    // Obtener los paquetes especificos
    $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hay_paquetes = count($paquetes) > 0;
}



    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ZONEMAISONS - Notificaciones</title>
        <link rel="stylesheet" href="../assets/Css/globals.css" />
        <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
        <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
        <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/novedades.css" />
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    </head>

    <body>

        <!-- Mostrar mensaje de éxito -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>

        <main>
            <section class="principal-page">
                <h2>Bienvenido a ZONEMAISONS</h2>
                <div class="code-loader">
                    <span>Mantente al tanto!!!</span>
                </div>
                <h3>Todo lo que pasa en tu comunidad, en un solo lugar.</h3>
            </section>

            <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
                <div class="boton-addpaquete">
                    <button class="animated-button btn-vermas" type="button" onclick="window.location.href='paquetes.php';">
                        <svg viewBox="0 0 24 24" class="arr-2">
                            <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                        </svg>
                        <span class="text">Crear Paquetes</span>

                        <span class="circle"></span>
                        <svg viewBox="0 0 24 24" class="arr-1">
                            <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                        </svg>
                    </button>
                </div>
            <?php endif; ?>

            <section class="second-page">
                <!-- Muro -->
                <div class="muro">
                    <div class="muro-header">
                        <h2>Muro</h2>
                        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
                            <a href="muro.php" class="round-button add-button" title="Agregar publicación">
                                <span>+</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($mensajes)): ?>
                        <?php foreach ($mensajes as $muro): ?>
                            <div class="tarjeta">
                                <div class="tarjeta-interna">
                                    <?php if (!empty($muro['muro_image'])): ?>
                                        <img src="../<?= htmlspecialchars($muro['muro_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del muro">
                                    <?php endif; ?>
                                    <div class="contenido">
                                        <div class="Asunto">
                                            <?= htmlspecialchars($muro['muro_Asunto'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="Descripcion">
                                            <p class="texto-muro"><?= nl2br(htmlspecialchars($muro['muro_Descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>

                                            <div class="meta-info">
                                                <small>Fecha: <?= htmlspecialchars($muro['muro_Fecha'], ENT_QUOTES, 'UTF-8') ?></small>

                                                <?php if (!empty($muro['muro_EnviaHora'])): ?>
                                                    <small> - Enviado a las: <?= htmlspecialchars($muro['muro_EnviaHora'], ENT_QUOTES, 'UTF-8') ?></small>
                                                <?php endif; ?>
                                            </div>

                                            <div class="acciones">
                                                <button class="animated-button btn-vermas" type="button">
                                                    <svg viewBox="0 0 24 24" class="arr-2">
                                                        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                                                    </svg>
                                                    <span class="text">Ver más</span>
                                                    <span class="circle"></span>
                                                    <svg viewBox="0 0 24 24" class="arr-1">
                                                        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                                                    </svg>
                                                </button>
                                                <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
                                                    <a href="editar_publicacion.php?id=<?= htmlspecialchars($muro['muro_Id'], ENT_QUOTES, 'UTF-8') ?>" class="round-button edit-button" title="Editar publicación">
                                                        <span>✎</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <p>No hay publicaciones en el muro aún.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paquetería (solo mostrar si hay paquetes) -->
                <?php if ($hay_paquetes): ?>
                    <section class="paqueteria">
                        <div class="paqueteria-header">
                            <h2>Paquetería</h2>
                        </div>

                        <?php
                        // Separar paquetes por estado
                        $paquetes_entregados = array_filter($paquetes, fn($p) => $p['paqu_estado'] === 'Entregado');
                        $paquetes_pendientes = array_filter($paquetes, fn($p) => $p['paqu_estado'] === 'Pendiente');
                        ?>

                        <!-- Paquetes Pendientes -->
                        <?php if (!empty($paquetes_pendientes)): ?>
                            <div class="paquetes-seccion">
                                <h3 class="subtitulo">Pendientes</h3>
                                <?php foreach ($paquetes_pendientes as $paquete): ?>
                                    <div class="tarjeta paquete-entregado">
                                        <div class="tarjeta-interna">
                                            <div class="imagen">
                                                <?php if (!empty($paquete['paqu_image'])): ?>
                                                    <div class="imagen-container">
                                                        <img src="../<?= htmlspecialchars($paquete['paqu_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del paquete" class="paquete-imagen">
                                                    </div>
                                                <?php endif; ?>

                                                <div class="contenido">
                                                    <div class="Asunto"><?= htmlspecialchars($paquete['paqu_Asunto'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <div class="Descripcion">
                                                        <small><?= htmlspecialchars($paquete['paqu_Descripcion'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    </div>
                                                    <div class="meta-paquete">
                                                        <small>Llegó: <?= htmlspecialchars($paquete['paqu_FechaLlegada'] ?? 'No disponible', ENT_QUOTES, 'UTF-8') ?></small>
                                                        <?php if (!empty($paquete['paqu_Hora'])): ?>
                                                            <small> - <?= htmlspecialchars($paquete['paqu_Hora'], ENT_QUOTES, 'UTF-8') ?></small>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                                <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
                                                    <div class="btn-edit">
                                                        <a href="editar_paqueteria.php?id=<?= htmlspecialchars($paquete['paqu_Id'], ENT_QUOTES, 'UTF-8') ?>" class="round-button edit-button" title="Editar paqueteria">
                                                            <span>✎</span>
                                                        </a>
                                                    <?php endif; ?>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Paquetes Entregados -->
                        <?php if (!empty($paquetes_entregados)): ?>
                            <div class="paquetes-seccion">
                                <h3 class="subtitulo">Entregados</h3>
                                <?php foreach ($paquetes_entregados as $paquete): ?>
                                    <div class="tarjeta paquete-entregado">
                                        <div class="tarjeta-interna">
                                            <?php if (!empty($paquete['paqu_image'])): ?>
                                                <div class="imagen-container">
                                                    <img src="../<?= htmlspecialchars($paquete['paqu_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del paquete" class="paquete-imagen">
                                                </div>
                                            <?php endif; ?>

                                            <div class="contenido">
                                                <div class="Asunto"><?= htmlspecialchars($paquete['paqu_Asunto'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <div class="Descripcion">
                                                    <small><?= htmlspecialchars($paquete['paqu_Descripcion'], ENT_QUOTES, 'UTF-8') ?></small>
                                                </div>
                                                <div class="meta-paquete">
                                                    <small>Entregado: <?= htmlspecialchars($paquete['paqu_FechaLlegada'] ?? 'No disponible', ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php if (!empty($paquete['paqu_Hora'])): ?>
                                                        <small> - <?= htmlspecialchars($paquete['paqu_Hora'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </section>
                <?php endif; ?>
            </section>
        </main>

        <div id="imageModal" class="image-modal" style="display: none;">
            <span class="image-modal-close">&times;</span>
            <img class="image-modal-content" id="modalImage" alt="Imagen ampliada">
            <div class="image-modal-caption" id="modalCaption"></div>
            <div class="image-modal-controls">
                <button class="image-modal-download" id="downloadBtn" title="Descargar imagen">
                    <i class="ri-download-line"></i>
                </button>
            </div>
        </div>

        <script src="../assets/Js/novedades.js"></script>
        <?php require_once "./Layout/footer.php"; ?>

    </body>

    </html>