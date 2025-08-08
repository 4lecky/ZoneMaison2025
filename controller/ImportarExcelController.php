<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ImportarExcelModel.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportarExcelController {

    public function importar() {
        global $pdo; // Usamos la conexión PDO definida en config

        if (isset($_FILES['archivoExcel']['tmp_name'])) {
            $archivo = $_FILES['archivoExcel']['tmp_name'];
            $spreadsheet = IOFactory::load($archivo);
            $hoja = $spreadsheet->getActiveSheet();
            $filas = $hoja->toArray();

            $ExcelModel = new Excel($pdo);

            for ($i = 1; $i < count($filas); $i++) {
                $fila = $filas[$i];

                $cedula = trim($fila[0]);
                $tipoDocumento = trim($fila[1]);
                $nombre = trim($fila[2]);
                $telefono = trim($fila[3]);
                $correo = trim($fila[4]);
                $contraseña = trim($fila[5]);
                $apartamento = trim($fila[6]); 
                $torre = trim($fila[7]);
                $parqueadero = trim($fila[8]);
                $propiedades = trim($fila[9]);


                // Solo insertar si hay datos válidos (Completarlo)
                if ($cedula && $tipoDocumento && $nombre && $telefono && $correo && $contraseña && $apartamento && $torre && $parqueadero && $propiedades) {
                    $ExcelModel->insertar($cedula, 
                    $tipoDocumento, 
                    $nombre, 
                    $telefono, 
                    $correo, 
                    $contraseña, 
                    $apartamento, 
                    $torre, 
                    $parqueadero, 
                    $propiedades);
                }
            }

            echo " Importación completada.";
        } else {
            echo " No se recibió archivo.";
        }
    }
}
