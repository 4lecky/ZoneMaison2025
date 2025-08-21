<?php
session_start();
require_once __DIR__ . '/../models/perfilUsuario.php';

class perfilUsuarioController {
    private $Perfil;

    public function __construct($pdo) {
        $this->usuario = new Usuario($pdo);
        session_start();
    }



}

