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

    public function marcarSalida() {
        $this->horaSalida = new DateTime();
    }

    public function calcularCosto() {
        if (!$this->horaSalida) {
            throw new Exception("La hora de salida no ha sido marcada.");
        }
        $diferencia = $this->horaSalida->diff($this->horaIngreso);
        $horas = $diferencia->h + ($diferencia->i / 60); // Total de horas
        return $this->tarifa->getCostoPorHora() * $horas;
    }

    public function imprimirTicket() {
        echo "Ticket:\n";
        echo "Vehículo: " . $this->vehiculo->getPlaca() . "<br>";
        echo "Hora de Ingreso: " . $this->horaIngreso->format('Y-m-d H:i:s') . "<br>";
        echo "Hora de Salida: " . ($this->horaSalida ? $this->horaSalida->format('Y-m-d H:i:s') : "No marcada") . "<br>";
        echo "Costo Total: " . ($this->horaSalida ? $this->calcularCosto() : "No calculado") . "<br>";
    }
}


?>