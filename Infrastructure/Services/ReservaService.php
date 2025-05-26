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
    $payload = json_encode([
        'idReserva' => $idReserva,
        'monto' => $monto,
        'correoCliente' => $correo,
        'tarjeta' => $tarjeta
    ]);

    $ch = curl_init('https://localhost:7116/api/pagos');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => 'Error en la solicitud CURL: ' . $error];
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        return ['error' => 'No se pudo procesar el pago'];
    }
}

}
