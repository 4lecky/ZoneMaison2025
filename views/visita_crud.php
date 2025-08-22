<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
require_once "./Layout/header.php";

// Capturar filtro de la URL, si no existe, mostrar todos
$filtro = $_GET['filtro'] ?? 'todos';

// Construir consulta SQL con filtros
$sql = "SELECT   vi.* , v.*
        FROM tbl_visitante vi
        INNER JOIN tbl_visita v ON vi.visi_id = v.vis_visi_id;";

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Admin</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/visitas.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
            <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'eliminado'): ?>
                <div class="alert alert-success">✅ Registro eliminado correctamente.</div>
            <?php elseif ($_GET['msg'] == 'error'): ?>
                <div class="alert alert-danger">❌ No se pudo eliminar el registro.</div>
            <?php endif; ?>
            <?php endif; ?>

<section class="table-card">
    <div class="tabla-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Visita</th>
                    <th>Nombre Visitante</th>
                    <th>Tipo Doc</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Cédula Residente</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Fecha Entrada</th>
                    <th>Fecha Salida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($datos = $stmt->fetch(PDO::FETCH_OBJ)) { ?>
                    <tr>
                        <td><?= $datos->vis_id ?></td>
                        <td><?= $datos->visi_nombre ?></td>
                        <td><?= $datos->visi_tipo_documento ?></td>
                        <td><?= $datos->visi_documento ?></td>
                        <td><?= $datos->visi_telefono ?></td>
                        <td><?= $datos->visi_email ?></td>
                        <td><?= $datos->visi_usuario_cedula ?></td>
                        <td><?= $datos->vis_hora_entrada ?></td>
                        <td><?= $datos->vis_hora_salida ?></td>
                        <td><?= $datos->vis_fecha_entrada ?></td>
                        <td><?= $datos->vis_fecha_salida ?></td>
                        <td>
                            <a href="visitascrud/Editar.php?id=<?= $datos->vis_id ?>" class="btn btn-warning btn-sm">
                                <i class="ri-edit-box-line"></i>
                            </a>
                            <a href="visitascrud/Eliminar.php?id=<?= $datos->vis_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta visita?');">
                                <i class="ri-delete-bin-2-line"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>


<?php require_once "./Layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
