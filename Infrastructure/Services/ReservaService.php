<?php

namespace Infrastructure\Services;

use Domain\Entities\Reserva;
use Domain\Interfaces\ReservaRepositoryInterface;
use Domain\Interfaces\PagoApiInterface;
use Exception;

class ReservaService {
    private ReservaRepositoryInterface $repository;
    private PagoApiInterface $pagoApi;

    public function __construct(ReservaRepositoryInterface $repository, PagoApiInterface $pagoApi) {
        $this->repository = $repository;
        $this->pagoApi = $pagoApi;
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

    public function buscarPorCodigo(string $codigo): array {
        try {
            $reserva = $this->repository->buscarPorCodigo($codigo);

            if (!$reserva) {
                return ['error' => 'Reserva no encontrada.'];
            }

            return $reserva;
        } catch (Exception $e) {
            return ['error' => 'Error al buscar la reserva.'];
        }
    }

    public function pagarReserva(int $idReserva, float $monto, string $correo, string $tarjeta): array {
        $datos = [
            'idReserva' => $idReserva,
            'monto' => $monto,
            'correoCliente' => $correo,
            'tarjeta' => $tarjeta
        ];

        return $this->pagoApi->procesarPago($datos);
    }

}
