<?php

class Vehiculo {
    private $placa;
    private $tipo;

    public function __construct($placa, $tipo) {
        $this->placa = $placa;
        $this->tipo = $tipo;
    }

    public function getPlaca() {
        return $this->placa;
    }
}

class Tarifa {
    private $costoPorHora;

    public function __construct($costoPorHora) {
        $this->costoPorHora = $costoPorHora;
    }

    public function getCostoPorHora() {
        return $this->costoPorHora;
    }
}

class Ticket {
    private $vehiculo;
    private $tarifa;
    private $horaIngreso;
    private $horaSalida;

    public function __construct(Vehiculo $vehiculo, Tarifa $tarifa) {
        $this->vehiculo = $vehiculo;
        $this->tarifa = $tarifa;
        $this->horaIngreso = new DateTime();
    }

    public function marcarIngreso(DateTime $hora) {
        $this->horaIngreso = $hora;
    }

    public function marcarSalida(DateTime $hora) {
        $this->horaSalida = $hora;
    }

    public function calcularCosto() {
        if (!$this->horaSalida) {
            throw new Exception("La hora de salida no ha sido marcada.");
        }
        $diferencia = $this->horaSalida->diff($this->horaIngreso);
        $horas = $diferencia->h + ($diferencia->i / 60);
        return $this->tarifa->getCostoPorHora() * $horas;
    }
}
