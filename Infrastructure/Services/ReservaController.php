<?php

namespace Api;

use Infrastructure\Services\ReservaService;
use Domain\Entities\Reserva;

class ReservaController {
    private ReservaService $service;

    public function __construct(ReservaService $service) {
        $this->service = $service;
    }

    public function postReserva(array $requestBody): void {
        header('Content-Type: application/json');

        $reserva = new Reserva(
            $requestBody['idHorario'] ?? null,
            $requestBody['cantidad'] ?? null,
            $requestBody['correo'] ?? null
        );

        $response = $this->service->reservar($reserva);

        if (isset($response['error'])) {
            http_response_code(400);
            echo json_encode(['error' => $response['error']]);
        } else {
            echo json_encode([
                'mensaje' => 'Reserva realizada exitosamente',
                'codigo_boleto' => $response['codigo']
            ]);
        }
    }
}
