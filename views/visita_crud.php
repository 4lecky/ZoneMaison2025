<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "./Layout/header.php"
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - admin</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/visitas.css" />
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>

<section class="table-card">

    <div class="consulta-filtros">
        <label for="filtroVisitas">Filtrar:</label>
        <select class="filtro-visitas" id="filtroVisitas" onchange="aplicarFiltro()">
            <option value="todos">Todos los visitantes</option>
            <option value="hoy">Visitas de hoy</option>
            <option value="pendientes">Pendientes de salida</option>
            <option value="completadas">Completadas</option>
        </select>

        <script>
        function aplicarFiltro() {
            const filtro = document.getElementById("filtroVisitas").value;
            window.location.href = "visita_crud.php?filtro=" + filtro;
        }
        </script>
    </div>

    <div class="tabla-responsive">
        <table class="tabla-visitas">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre Visitante</th>
                    <th scope="col">Tipo Doc</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Email</th>
                    <th scope="col">Cédula Residente</th>
                    <th scope="col">Hora Entrada</th>
                    <th scope="col">Hora Salida</th>
                    <th scope="col">Fecha Entrada</th>
                    <th scope="col">Fecha Salida</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaCuerpo">
                <?php 
                require_once '../config/db.php';

                $sql = "SELECT 
                        v.vis_id,
                        v.vis_horaentrada,
                        v.vis_horasalida,
                        v.vis_fechaentrada,
                        v.vis_fechasalida,
                        vt.visi_nombre,
                        vt.visi_tipodocumento,
                        vt.visi_documento,
                        vt.visi_email,
                        vt.visi_usuario_cc
                    FROM tbl_visita v
                    INNER JOIN tbl_visitante vt ON v.vis_id = vt.visi_vis_id
                    ORDER BY v.vis_fechaentrada DESC;
                
                $stmt = $pdo->query($sql);

                while ($datos = $stmt->fetch(PDO::FETCH_OBJ)) { ?>                                    
                    <tr>
                        <td><?= $datos->vis_id ?></td>
                        <td><?= $datos->visi_nombre ?></td>
                        <td><?= $datos->visi_tipo_documento ?></td>
                        <td><?= $datos->visi_documento ?></td>
                        <td><?= $datos->visi_email ?></td>
                        <td><?= $datos->visi_usuario_cedula ?></td>
                        <td><?= $datos->vis_hora_entrada ?></td>
                        <td><?= $datos->vis_hora_salida ?></td>
                        <td><?= $datos->vis_fecha_entrada ?></td>
                        <td><?= $datos->vis_fecha_salida ?></td>
                        <td>
                            <a href="../views/visitascrud/Editar.php?id=<?= $datos->vis_id ?>" class="btn btn-small btn-warning">
                                <i class="ri-edit-box-line"></i>
                            </a>
                            <a href="" class="btn btn-small btn-danger"> 
                                <i class="ri-delete-bin-2-line"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="empty-state" id="estadoVacio">
        <i class="fa-solid fa-circle-info"></i>
        No hay visitas programadas próximamente
    </div>

</section>

</main>

<!-- Scripts -->
<script src="../assets/js/visitas.js"></script>
<?php require_once "./Layout/footer.php" ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

        <?php
        require_once '../config/db.php';

        $filtro = $_GET['filtro'] ?? 'todos';

        $sql = "SELECT 
                    v.vis_id,
                    v.vis_hora_entrada,
                    v.vis_hora_salida,
                    v.vis_fecha_entrada,
                    v.vis_fecha_salida,
                    vi.visi_nombre,
                    vi.visi_tipo_documento,
                    vi.visi_documento,
                    vi.visi_email,
                    vi.visi_usuario_cedula
                FROM tbl_visita v
                LEFT JOIN tbl_visitante vi ON vi.visi_vis_id = v.vis_id";

        switch ($filtro) {
            case 'hoy':
                $sql .= " WHERE DATE(v.vis_fecha_entrada) = CURDATE()";
                break;
            case 'pendientes':
                $sql .= " WHERE v.vis_hora_salida IS NULL";
                break;
            case 'completadas':
                $sql .= " WHERE v.vis_hora_salida IS NOT NULL";
                break;
            default:
                // "todos" no añade condición
                break;
        }

        $sql .= " ORDER BY v.vis_fecha_entrada DESC";

        $stmt = $pdo->query($sql);
