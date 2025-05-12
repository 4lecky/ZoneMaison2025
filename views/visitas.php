<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - admin</title>
    <link rel="stylesheet" href="../assets/Css/visitas.css">
</head>
<body>

    <header>
        <div class="logo-container">
            <img src="../assets/img/LogoZM.png" alt="Logo de ZONEMAISONS" class="logo">
            <div class="title-container">
                <h1>ZONEMAISONS</h1>
                <div class="underline"></div>
            </div>
        </div>
        <nav class="menu-button">
            <div class="lines">&#9776;</div>
        </nav>
    </header> 

    <nav class="main-nav">
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li><a href="index.html" class="active">Notificaciones</a></li>
            <li><a href="#">Reservas</a></li>
            <li><a href="#">Pqrs</a></li>
        </ul>
    </nav>

    <!-- Registro de Visitas -->
    <main class="container mt-4">
        <h2>REGISTRO DE VISITAS</h2>
        <section class="form-card">
            <h3>Datos Visitante</h3>
            <form class="visit-form" id="visit-form">
                <input type="text" placeholder="Nombre Completo">
                <div class="form-row">
                    <select>
                        <option>Tipo Doc.</option>
                        <option>C.C.</option>
                        <option>T.I.</option>
                    </select>
                    <input type="text" placeholder="Número Documento">
                </div>
                <input type="email" placeholder="Email">
                <div class="form-row">
                    <input type="text" placeholder="Num. Torre Visitada">
                    <input type="text" placeholder="Num. Apto Visitado">
                </div>
                <div class="form-row">
                    <input type="date">
                    <input type="date">
                </div>
                <button type="button" class="secondary" id="Limpiarbtn">
                    <i class="fa-solid fa-keyboard"></i> Limpiar
                  </button>                  
                <button type="submit" class="primary full-width" id="Registrar">Registrar Visitante </button>
            </form>
        </section>

        <section class="table-card">
            <h3>Consulta Visitantes</h3>
            <div class="Consulta">
                <select class="filtro-visitas">
                    <option>Todos los visitantes</option>
                    <option>Visitas de hoy</option>
                    <option>Pendientes de salida</option>
                    <option>Completadas</option>
                </select>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Torre/Apto</th>
                    </tr>
                </thead>
            </table>

            <div class="empty-state">
                <i class="fa-solid fa-circle-info"></i> No hay visitas programadas próximamente
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="../assets/Js/visitas.js"></script>
</body>
</html>