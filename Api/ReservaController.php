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
            $requestBody['idHorario'],
            $requestBody['cantidad'],
            $requestBody['correo']
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

    public function getDisponibilidad(): void {
        header('Content-Type: application/json');
        if (!isset($_GET['idHorario'])) {
            echo json_encode(['error' => 'Falta el parámetro idHorario']);
            return;
        }
    
        $idHorario = (int)$_GET['idHorario'];
        $resultado = $this->service->obtenerDisponibilidad($idHorario);
        echo json_encode($resultado);
    }

    public function getReservaPorCodigo($codigo)
    {
        $resultado = $this->service->buscarPorCodigo($codigo);
        echo json_encode($resultado);
    }

    public function postReservaPago(array $data) {
        if (!isset($data['idReserva'], $data['monto'], $data['correo'], $data['tarjeta'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }
    
        $result = $this->service->pagarReserva(
            $data['idReserva'],
            $data['monto'],
            $data['correo'],
            $data['tarjeta']
        );
    
        echo json_encode($result);
    }

}