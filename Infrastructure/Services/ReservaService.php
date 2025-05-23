<?php

namespace Infrastructure\Services;

use Domain\Entities\Reserva;
use Domain\Interfaces\ReservaRepositoryInterface;
use Exception;

class ReservaService {
    private ReservaRepositoryInterface $repository;

    public function __construct(ReservaRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function reservar(Reserva $reserva): array {
        if ($reserva->getCantidadAsientos() < 1 || $reserva->getCantidadAsientos() > 4) {
            return [
                'error' => 'Cantidad inválida. Solo puedes reservar entre 1 y 4 asientos.'
            ];
        }

        try {
            $disponible = $this->repository->verificarDisponibilidad(
                $reserva->getIdHorario(),
                $reserva->getCantidadAsientos()
            );
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }

        if (!$disponible) {
            return [
                'error' => 'Excedió el límite de asientos disponibles; reduzca la cantidad o elija otra salida.'
            ];
        }

        try {
            $codigo = $this->repository->crearReserva([
                'idHorario' => $reserva->getIdHorario(),
                'cantidad' => $reserva->getCantidadAsientos(),
                'correo' => $reserva->getCorreoCliente()
            ]);

            return ['codigo' => $codigo];
        } catch (Exception $e) {
            return ['error' => 'Error al crear la reserva.'];
        }
    }

    public function obtenerDisponibilidad(int $idHorario): array {
        $disponibles = $this->repository->obtenerDisponibilidad($idHorario);
        if ($disponibles === -1) {
            return ['error' => 'Horario no disponible.'];
        }
        return ['disponibles' => $disponibles];
    }

}
