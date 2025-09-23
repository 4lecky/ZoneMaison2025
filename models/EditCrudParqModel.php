<?php
require_once __DIR__ . "/../config/db.php";

class EditCrudParqModel
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }

    // ✅ Obtener un parqueadero por ID
    public function obtenerParqueadero($id)
    {
        $sql = "SELECT parq_id,
                    parq_vehi_placa,
                    parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi,
                    parq_num_doc_vehi,
                    parq_numeroParqueadero,
                    parq_fecha_entrada,
                    parq_fecha_salida,
                    parq_hora_entrada
            FROM tbl_parqueadero 
                WHERE parq_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }



    // Actualizar solo el registro de parqueadero
    public function actualizarParqueadero($data)
    {
        $sql = "UPDATE tbl_parqueadero 
                SET parq_vehi_placa              = :parq_vehi_placa,
                    parq_nombre_propietario_vehi = :parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi           = :parq_tipo_doc_vehi,
                    parq_num_doc_vehi            = :parq_num_doc_vehi,
                    parq_numeroParqueadero       = :parq_numeroParqueadero,
                    parq_fecha_entrada           = :parq_fecha_entrada,
                    parq_fecha_salida            = :parq_fecha_salida,
                    parq_hora_entrada            = :parq_hora_entrada
                WHERE parq_id = :parq_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':parq_vehi_placa'              => $data['parq_vehi_placa'],
            ':parq_nombre_propietario_vehi' => $data['parq_nombre_propietario_vehi'],
            ':parq_tipo_doc_vehi'           => $data['parq_tipo_doc_vehi'],
            ':parq_num_doc_vehi'            => $data['parq_num_doc_vehi'],
            ':parq_numeroParqueadero'       => $data['parq_numeroParqueadero'],
            ':parq_fecha_entrada'           => $data['parq_fecha_entrada'],
            ':parq_fecha_salida'            => $data['parq_fecha_salida'],
            ':parq_hora_entrada'            => $data['parq_hora_entrada'],
            ':parq_id'                       => $data['parq_id']
        ]);
    }

    public function eliminarParqueadero($id)
    {
        $sql = "DELETE FROM tbl_parqueadero WHERE parq_id = :id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute([":id" => $id])) {
            if ($stmt->rowCount() > 0) {
                echo "✅ Se eliminó el parqueadero con ID: " . $id;
                return true;
            } else {
                echo "⚠️ No se encontró ningún parqueadero con parq_id = " . $id;
                return false;
            }
        } else {
            echo "❌ Error al eliminar el registro de parqueadero: ";
            print_r($stmt->errorInfo());
            return false;
        }
    }

}
