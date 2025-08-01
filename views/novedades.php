<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "../config/db.php";
require_once "./Layout/header.php";

$mensaje = $_GET['success'] ?? '';
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
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

    <?php
    // Mostrar mensaje de éxito si existe
    if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php
    // Obtener mensajes del muro
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_muro ORDER BY muro_Fecha DESC, muro_Hora DESC");
        $stmt->execute();
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensajes = [];
        error_log("Error al obtener mensajes del muro: " . $e->getMessage());
    }

    // Obtener paquetes
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_paquetes WHERE paqu_estado IN ('Entregado', 'Pendiente') ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC");
        $stmt->execute();
        $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hay_paquetes = count($paquetes) > 0;
    } catch (PDOException $e) {
        $paquetes = [];
        $hay_paquetes = false;
        error_log("Error al obtener paquetes: " . $e->getMessage());
    }
    ?>

    <main>
        <section class="principal-page">
            <h2>Bienvenido a ZONEMAISONS</h2>

            <!-- Mantente al tanto -->
            <div class="code-loader">
                <span>Mantente al tanto!!!</span>
            </div>

            <h3>Todo lo que pasa en tu comunidad, en un solo lugar.</h3>
        </section>

        <section class="second-page">
            <!-- Muro -->
            <div class="muro">
                <div class="muro-header">
                    <h2>Muro</h2>
                    <a href="muro.php" class="round-button add-button" title="Agregar publicación">
                        <span>+</span>
                    </a>
                </div>

                <?php if (count($mensajes) > 0): ?>
                    <?php foreach ($mensajes as $muro): ?>
                        <div class="tarjeta">
                            <div class="tarjeta-interna">
                                <?php if (!empty($muro['muro_image'])): ?>
                                    <img src="../<?= htmlspecialchars($muro['muro_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del muro">
                                <?php endif; ?>
                                <div class="contenido">
                                    <div class="Asunto">
                                        <?= htmlspecialchars($muro['muro_Asunto'], ENT_QUOTES, 'UTF-8') ?>
                                        <div class="meta-info">
                                            <span class="hora"><?= htmlspecialchars($muro['muro_Hora'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="fecha"><?= htmlspecialchars($muro['muro_Fecha'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                    <div class="Descripcion">
                                        <p class="texto-muro"><?= nl2br(htmlspecialchars($muro['muro_Descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>
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
                                            <a href="editar_publicacion.php?id=<?= htmlspecialchars($muro['muro_Id'], ENT_QUOTES, 'UTF-8') ?>" class="round-button edit-button" title="Editar publicación">
                                                <span>✎</span>
                                            </a>
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

            <!-- Paquetería - Solo mostrar si hay paquetes -->
            <?php if ($hay_paquetes): ?>
            <section class="paqueteria">
                <div class="paqueteria-header">
                    <h2>Paquetería</h2>
                    <a href="paquetes.php" class="round-button add-button" title="Agregar paquete">
                        <span>+</span>
                    </a>
                </div>

                <?php
                // Separar paquetes por estado
                $paquetes_entregados = array_filter($paquetes, function($p) { return $p['paqu_estado'] === 'Entregado'; });
                $paquetes_pendientes = array_filter($paquetes, function($p) { return $p['paqu_estado'] === 'Pendiente'; });
                ?>

                <!-- Paquetes Pendientes -->
                <?php if (count($paquetes_pendientes) > 0): ?>
                <div class="paquetes-seccion">
                    <h3 class="subtitulo">Pendientes</h3>
                    <?php foreach ($paquetes_pendientes as $paquete): ?>
                    <div class="tarjeta paquete-entregado">
                        <div class="tarjeta-interna">
                            <div class="paquete-icono">📦</div>
                          
                            <div class="contenido">
                                <div class="Asunto">
                                    <?= htmlspecialchars($paquete['paqu_Asunto'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="Descripcion">
                                    <small><?= htmlspecialchars($paquete['paqu_Descripcion'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                                <div class="meta-paquete">
                                    <small>Llego: <?= htmlspecialchars($paquete['paqu_FechaLlegada'] ?? 'No disponible', ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php if (!empty($paquete['paqu_Hora'])): ?>
                                        <small> - <?= htmlspecialchars($paquete['paqu_Hora'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                      <div class=imagen>
                                <?php if (!empty($paquete['paqu_image'])): ?>
                                    <img src="../<?= htmlspecialchars($paquete['paqu_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del paquete">
                                <?php endif; ?>
                                </div>
                                    <div class="btn-edit">
                                     <a href="editar_paqueteria.php?id=<?= htmlspecialchars($paquete['paqu_Id'], ENT_QUOTES, 'UTF-8') ?>" class="round-button edit-button" title="Editar paqueteria">
                                                <span>✎</span>
                                            </a>
                                </div>
                            </div>
                            </div>
                                </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Paquetes Entregados -->
                <?php if (count($paquetes_entregados) > 0): ?>
                <div class="paquetes-seccion">
                    <h3 class="subtitulo">Entregados</h3>
                    <?php foreach ($paquetes_entregados as $paquete): ?>
                    <div class="tarjeta paquete-entregado">
                        <div class="tarjeta-interna">
                            <div class="paquete-icono">✅</div>
                            <div class=imagen>
                                <?php if (!empty($paquete['paqu_image'])): ?>
                                    <img src="../<?= htmlspecialchars($paquete['paqu_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagen del paquete">
                                <?php endif; ?>
                            <div class="contenido">
                                <div class="Asunto">
                                    <?= htmlspecialchars($paquete['paqu_Asunto'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
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
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </section>
    </main>

    <script src="../assets/Js/novedades.js"></script>

    <?php
    require_once "./Layout/footer.php";
    ?>

</body>

</html>