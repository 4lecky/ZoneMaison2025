<?php

$pdo = require_once __DIR__ . '/../config/db.php';

try {
 
    $sql_muro = "
        DELETE FROM tbl_muro
        WHERE TIMESTAMP(muro_Fecha, muro_Hora) < NOW() - INTERVAL 1 DAY
    ";
    $borradas_muro = $pdo->exec($sql_muro);

    $sql_paquetes = "
        DELETE FROM tbl_paquetes
        WHERE TIMESTAMP(paqu_FechaLlegada, paqu_Hora) < NOW() - INTERVAL 1 DAY
    ";
    $borradas_paquetes = $pdo->exec($sql_paquetes);

    
    $log = "[" . date("Y-m-d H:i:s") . "] "
         . "Muro: $borradas_muro publicaciones eliminadas | "
         . "Paquetería: $borradas_paquetes registros eliminados\n";

    file_put_contents(__DIR__ . "/borrado.log", $log, FILE_APPEND);

    echo "✅ $borradas_muro publicaciones y $borradas_paquetes paquetes eliminados\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
