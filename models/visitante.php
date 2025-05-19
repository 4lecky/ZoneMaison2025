<?php
class visitante {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarvisitante($data) {

        $sql = "INSERT INTO tbl_Visitante (
                            visi_nombre ,
                            visi_documento ,
                            visi_Tipo_documento ,
                            visi_telefono ,
                            visi_email ,
                            visi_vis_id ,
        ) VALUES (?, ?, ?, ?, ?, )"; 
        //Evitamos inyecciones sql

        $stmt = $this->pdo->prepare($sql);


    // Crear un array indexado con los valores en el mismo orden que en la consulta
    $asignaciones = [
        $data['nombre'],
        $data['tipoDoc'],
        $data['documento'],
        $data['email'],
        $data['telefono']
        
    ];

        return $stmt->execute($asignaciones);
    }

}