<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../domain/interfaces/ReservaRepositoryInterface.php';
require_once __DIR__ . '/../domain/interfaces/PagoApiInterface.php';
require_once __DIR__ . '/../domain/interfaces/RepositoryFactoryInterface.php';
require_once __DIR__ . '/../domain/entities/Reserva.php';
require_once __DIR__ . '/../infrastructure/database/MySQLReservaRepository.php';
require_once __DIR__ . '/../infrastructure/adapters/PagoApiAdapter.php';
require_once __DIR__ . '/../infrastructure/factories/MySQLRepositoryFactory.php';
require_once __DIR__ . '/../infrastructure/services/ReservaService.php';
require_once __DIR__ . '/../api/ReservaController.php';

use Infrastructure\Factories\MySQLRepositoryFactory;
use Infrastructure\Services\ReservaService;
use Infrastructure\Adapters\PagoApiAdapter;
use Api\ReservaController;

$conn = include __DIR__ . '/../config/db.php';

$factory = new MySQLRepositoryFactory($conn);
$pagoApi = new PagoApiAdapter("https://localhost:7116/api/pagos");

$service = new ReservaService(
    $factory->createReservaRepository(),
    $pagoApi
);

$controller = new ReservaController($service);

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST' && str_contains($requestUri, '/api/reservas')) {
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $response = $controller->postReserva($requestBody);
    echo $response;
} elseif ($requestMethod === 'POST' && str_contains($requestUri, '/api/reservaPago')) {
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $controller->postReservaPago($requestBody);
} elseif ($requestMethod === 'GET') {
    if (isset($_GET['idHorario'])) {
        $controller->getDisponibilidad();
    } elseif (isset($_GET['codigo'])) {
        $controller->getReservaPorCodigo($_GET['codigo']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Parámetro inválido.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada.']);
}
