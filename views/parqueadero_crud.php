<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
require_once "./Layout/header.php";
require_once "../models/EditCrudParqModel.php"; // aquí está la conexión PDO

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crudParqueadero</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/parqueadero_crud/boton-cancelar.css" />
    <!-- solo css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- fontawesome -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>
    <h1 class="text-center my-4">CRUD Parqueaderos</h1>

    <section class="table-card container">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Propietario</th>
                        <th>Tipo Doc</th>
                        <th>N° Doc</th>
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
                            <td><?= $datos->parq_numeroParqueadero ?></td>
                            <td><?= $datos->parq_fecha_entrada ?></td>
                            <td><?= $datos->parq_fecha_salida ?></td>
                            <td><?= $datos->parq_hora_entrada ?></td>
                            <td>
                                <a href="parqueaderocrud/Editar.php?id=<?= $datos->parq_id ?>" class="btn btn-warning btn-sm">
                                    <i class="ri-edit-box-line"></i>
                                </a>
                                <a href="parqueaderocrud/Eliminar.php?id=<?= $datos->parq_id ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro que deseas eliminar este registro de parqueadero?');">
                                    <i class="ri-delete-bin-2-line"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>


                </tbody>
            </table>
        </div>
    </section>

<div class="contenedor-boton">
    <a href="../views/parqueadero.php" class="boton_cancelarcrud cancelar-crud-especial">Cancelar</a>
</div>


    <?php require_once "./Layout/footer.php" ?>

    <!-- JS bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>