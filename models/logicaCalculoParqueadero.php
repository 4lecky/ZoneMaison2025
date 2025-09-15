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

    public function getTipo() {
        return $this->tipo;
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
    private $fechaSalida;

    public function __construct(Vehiculo $vehiculo, Tarifa $tarifa) {
        $this->vehiculo = $vehiculo;
        $this->tarifa = $tarifa;
    }

    public function marcarIngreso(DateTime $hora) {
        $this->horaIngreso = $hora;
    }

    public function marcarSalida(DateTime $hora) {
        $this->horaSalida = $hora;
    }

    public function marcarHoraSalida(DateTime $hora) {
        $this->horaSalida = $hora;
    }

    public function calcularHoras() {
        if (!$this->horaSalida || !$this->horaIngreso) {
            throw new Exception("Faltan horas de ingreso/salida.");
        }

        $diferencia = $this->horaSalida->diff($this->horaIngreso);
        $horas = ($diferencia->days * 24) + $diferencia->h + ($diferencia->i / 60);
        return ceil($horas);
    }

    public function calcularCosto() {
        $horas = $this->calcularHoras();
        return $this->tarifa->getCostoPorHora() * $horas;
    }

    public function getTipoVehiculo() {
        return $this->vehiculo->getTipo();
    }
}
