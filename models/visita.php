<?php
class visitante {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarvisita($data) {

        $sql = "INSERT INTO tbl_Visitante (
                            vis_hora_entrada ,
                            vis_hora_salida ,
                            vis_fecha_entrada ,
                            vis_fecha_salida ,
                            vis_torre_visitada ,
                            vis_Apto_visitado ,
                            vis_usuario_cc ,
        ) VALUES (?, ?, ?, ?, ?, ? )"; 
        //Evitamos inyecciones sql

        $stmt = $this->pdo->prepare($sql);


    // Crear un array indexado con los valores en el mismo orden que en la consulta
    $asignaciones = [
        $data['torre'],
        $data['apto'],
        $data['fechaEntrada'],
        $data['fechaSalida'],
        $data['horaInicio'],
        $data['horaSalida']
    ];

        return $stmt->execute($asignaciones);
    }

}