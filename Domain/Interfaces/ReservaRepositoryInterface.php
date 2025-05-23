<?php
namespace Domain\Interfaces;

interface ReservaRepositoryInterface {
    public function verificarDisponibilidad(int $idHorario, int $cantidad): bool;
    public function crearReserva(array $data): string;
    public function obtenerDisponibilidad(int $idHorario): int;
}
