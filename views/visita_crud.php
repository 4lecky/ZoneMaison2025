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
    


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>

                <section class="table-card">
        
                        <div class="consulta-filtros">
                            <label for="filtroVisitas">Filtrar:</label>
                            <select class="filtro-visitas" id="filtroVisitas">
                                <option value="todos">Todos los visitantes</option>
                                <option value="hoy">Visitas de hoy</option>
                                <option value="pendientes">Pendientes de salida</option>
                                <option value="completadas">Completadas</option>
                            </select>
                        </div>

                        <div class="tabla-responsive">
                            <table class="tabla-visitas">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Hora Entrada</th>
                                        <th scope="col">Hora Salida</th>
                                        <th scope="col">Fecha Entrada</th>
                                        <th scope="col">Fecha Salida</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCuerpo">
                                    <?php require_once '../config/db.php';
                                        $stmt = $pdo->query("SELECT * FROM tbl_visita ORDER BY vis_fecha_entrada DESC");

                                        while ($datos = $stmt->fetch(PDO::FETCH_OBJ)) { ?>                                    
                                    <!-- Aquí se insertarán filas dinámicamente -->
                                     <tr>
                                        <td><?= $datos->vis_id ?></td>
                                        <td><?= $datos->vis_hora_entrada ?></td>
                                        <td><?= $datos->vis_hora_salida ?></td>
                                        <td><?= $datos->vis_fecha_entrada ?></td>
                                        <td><?= $datos->vis_fecha_salida ?></td>
                                        <td>
                                            <a href="../views/visitascrud/Editar.php?id=<?= $datos->vis_id ?>" class="btn btn-small btn-warning"><i class="ri-edit-box-line"></i></a>
                                            <a href="" class="btn btn-small btn-danger"> <i class="ri-delete-bin-2-line"></i></a>
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

 
    <?php
    require_once "./Layout/footer.php"
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>