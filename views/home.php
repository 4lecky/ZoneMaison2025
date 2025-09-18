<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoneMaisons - Sistema de Gestión de Propiedad Horizontal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="assets/Css/inicio.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/img/logoZM.png" alt="ZoneMaisons" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nosotros">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#modulos">Módulos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#planes">Planes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-primary-custom" href="./login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-primary" href="./signUp.php">Registrarse</a> 
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Gestión Inteligente para tu Conjunto Residencial</h1>
                    <p class="lead mb-4">
                        Optimiza la administración de tu propiedad horizontal con nuestra plataforma integral. 
                        Simplifica procesos, mejora la comunicación y transforma la experiencia de vivir en comunidad.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#planes" class="btn btn-primary-custom btn-lg">Ver Planes</a>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="ri-shield-check-line"></i> Cumplimiento Ley 675 de 2001
                            <i class="ri-lock-line ms-3"></i> Datos protegidos
                        </small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/img/Conjunto_fondo_reset.jpg" alt="Gestión de Conjuntos" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Quiénes Somos -->
    <section id="nosotros" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">¿Quiénes Somos?</h2>
                    <div class="title-underline"></div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <img src="assets/img/equipo.jpg" alt="Sobre Nosotros" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h3 class="mb-4">Transformando la Administración de Propiedad Horizontal</h3>
                    <p class="mb-3">
                        ZoneMaisons nace de la necesidad real de modernizar la gestión en conjuntos residenciales. 
                        Somos un equipo de profesionales del SENA comprometidos con digitalizar y optimizar los 
                        procesos administrativos que tradicionalmente se han manejado de forma manual.
                    </p>
                    <p class="mb-4">
                        Nuestra plataforma centraliza toda la información, automatiza tareas repetitivas y mejora 
                        la comunicación entre administradores, residentes y personal de seguridad.
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="feature-box">
                                <i class="ri-check-double-line"></i>
                                <span>100% Digital</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-box">
                                <i class="ri-time-line"></i>
                                <span>Ahorro de Tiempo</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-box">
                                <i class="ri-user-smile-line"></i>
                                <span>Fácil de Usar</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-box">
                                <i class="ri-customer-service-line"></i>
                                <span>Soporte 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Módulos -->
    <section id="modulos" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Nuestros Módulos</h2>
                    <div class="title-underline"></div>
                    <p class="lead mt-3">Todo lo que necesitas en una sola plataforma</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-message-3-line"></i>
                        </div>
                        <h4>PQRS</h4>
                        <p>Gestiona peticiones, quejas, reclamos y sugerencias de manera eficiente con seguimiento en tiempo real.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <h4>Reservas</h4>
                        <p>Sistema de reservas para zonas comunes con calendario integrado y validación automática de disponibilidad.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-user-follow-line"></i>
                        </div>
                        <h4>Visitantes</h4>
                        <p>Control completo del ingreso y salida de visitantes con notificaciones automáticas a residentes.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-car-line"></i>
                        </div>
                        <h4>Parqueaderos</h4>
                        <p>Administración de espacios de parqueo, control vehicular y cálculo automático de tarifas.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-notification-3-line"></i>
                        </div>
                        <h4>Comunicaciones</h4>
                        <p>Muro de novedades y sistema de notificaciones para mantener informada a toda la comunidad.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="ri-inbox-unarchive-line"></i>
                        </div>
                        <h4>Paquetería</h4>
                        <p>Control de recepción y entrega de paquetes con alertas automáticas a los residentes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Planes -->
    <section id="planes" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Planes y Precios</h2>
                    <div class="title-underline"></div>
                    <p class="lead mt-3">Elige el plan que mejor se adapte a tu conjunto residencial</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h3>Plan Básico</h3>
                            <p class="price">
                                <span class="currency">$</span>
                                <span class="amount">299.000</span>
                                <span class="period">/mes</span>
                            </p>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="ri-check-line"></i> Hasta 50 unidades</li>
                            <li><i class="ri-check-line"></i> Módulos básicos (PQRS, Visitantes)</li>
                            <li><i class="ri-check-line"></i> 5 usuarios administradores</li>
                            <li><i class="ri-check-line"></i> Soporte por email</li>
                            <li><i class="ri-check-line"></i> Actualizaciones mensuales</li>
                        </ul>
                        <button class="btn btn-outline-primary w-100">Elegir Plan</button>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured">
                        <div class="badge-popular">MÁS POPULAR</div>
                        <div class="pricing-header">
                            <h3>Plan Profesional</h3>
                            <p class="price">
                                <span class="currency">$</span>
                                <span class="amount">599.000</span>
                                <span class="period">/mes</span>
                            </p>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="ri-check-line"></i> Hasta 200 unidades</li>
                            <li><i class="ri-check-line"></i> Todos los módulos incluidos</li>
                            <li><i class="ri-check-line"></i> Usuarios ilimitados</li>
                            <li><i class="ri-check-line"></i> Soporte prioritario 24/7</li>
                            <li><i class="ri-check-line"></i> Personalización de marca</li>
                            <li><i class="ri-check-line"></i> Reportes avanzados</li>
                        </ul>
                        <button class="btn btn-primary-custom w-100">Elegir Plan</button>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h3>Plan Enterprise</h3>
                            <p class="price">
                                <span class="currency">$</span>
                                <span class="amount">999.000</span>
                                <span class="period">/mes</span>
                            </p>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="ri-check-line"></i> Unidades ilimitadas</li>
                            <li><i class="ri-check-line"></i> Todos los módulos + API</li>
                            <li><i class="ri-check-line"></i> Multi-conjunto</li>
                            <li><i class="ri-check-line"></i> Soporte dedicado</li>
                            <li><i class="ri-check-line"></i> Capacitación incluida</li>
                            <li><i class="ri-check-line"></i> Integraciones personalizadas</li>
                        </ul>
                        <button class="btn btn-outline-primary w-100">Contactar Ventas</button>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-lg-12 text-center">
                    <p class="mb-0">
                        <i class="ri-discount-percent-line"></i> 
                        <strong>Ahorra 20%</strong> con planes anuales
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Por qué elegirnos -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">¿Por Qué Elegirnos?</h2>
                    <div class="title-underline"></div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <h5>Seguridad Garantizada</h5>
                        <p>Cumplimos con la Ley 1581 de protección de datos. Tu información está segura con nosotros.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="ri-bar-chart-box-line"></i>
                        </div>
                        <h5>Reduce Costos</h5>
                        <p>Ahorra hasta 40% en costos administrativos automatizando procesos manuales.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="ri-smartphone-line"></i>
                        </div>
                        <h5>Acceso Móvil</h5>
                        <p>Plataforma 100% responsive. Gestiona tu conjunto desde cualquier dispositivo.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="ri-team-line"></i>
                        </div>
                        <h5>Mejora la Convivencia</h5>
                        <p>Facilita la comunicación y participación activa de toda la comunidad.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php require_once "./Layout/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>