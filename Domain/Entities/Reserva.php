<?php

namespace Domain\Entities;

class Reserva {
    private int $idHorario;
    private int $cantidadAsientos;
    private string $correoCliente;

    public function __construct(int $idHorario, int $cantidadAsientos, string $correoCliente) {
        $this->idHorario = $idHorario;
        $this->cantidadAsientos = $cantidadAsientos;
        $this->correoCliente = $correoCliente;
    }

    public function getIdHorario(): int {
        return $this->idHorario;
    }

    public function getCantidadAsientos(): int {
        return $this->cantidadAsientos;
    }

    public function getCorreoCliente(): string {
        return $this->correoCliente;
    }
}