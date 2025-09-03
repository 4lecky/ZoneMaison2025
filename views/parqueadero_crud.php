<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../guardarDatosRegistroParqueadero.php"; // aquí está la conexión PDO

// Capturar filtro desde la URL
$filtro = $_GET['filtro'] ?? 'todos';

// Consulta base
$sql = "SELECT * FROM tbl_parqueadero";

switch ($filtro) {
    case 'ocupados':
        $sql .= " WHERE parq_vehi_estadoIngreso = 'OCUPADO'";
        break;
    case 'disponibles':
        $sql .= " WHERE parq_vehi_estadoIngreso = 'DISPONIBLE'";
        break;
    case 'reservados':
        $sql .= " WHERE parq_vehi_estadoIngreso = 'RESERVADO'";
        break;
    default:
        // "todos" no añade condición
        break;
}

$sql .= " ORDER BY parq_fecha_entrada DESC";

$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Parqueaderos</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section class="table-card">
    <div class="tabla-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Placa</th>
                    <th>Propietario</th>
                    <th>Tipo Doc</th>
                    <th>N° Doc</th>
                    <th>Estado</th>
                    <th>N° Parqueadero</th>
                    <th>Fecha Entrada</th>
                    <th>Fecha Salida</th>
                    <th>Hora Entrada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($datos = $stmt->fetch(PDO::FETCH_OBJ)) { ?>
                    <tr>
                        <td><?= $datos->parq_id ?></td>
                        <td><?= $datos->parq_vehi_placa ?></td>
                        <td><?= $datos->parq_nombre_propietario_vehi ?></td>
                        <td><?= $datos->parq_tipo_doc_vehi ?></td>
                        <td><?= $datos->parq_num_doc_vehi ?></td>
                        <td><?= $datos->parq_vehi_estadoIngreso ?></td>
                        <td><?= $datos->parq_numeroParqueadero ?></td>
                        <td><?= $datos->parq_fecha_entrada ?></td>
                        <td><?= $datos->parq_fecha_salida ?></td>
                        <td><?= $datos->parq_hora_entrada ?></td>
                        <td>
                            <a href="parqueaderoCrud/Editar.php?id=<?= $datos->parq_id ?>" class="btn btn-warning btn-sm">
                                <i class="ri-edit-box-line"></i>
                            </a>
                            <a href="parqueaderoCrud/Eliminar.php?id=<?= $datos->parq_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este registro?');">
                                <i class="ri-delete-bin-2-line"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
</body>
</html>
