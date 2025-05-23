<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../domain/interfaces/ReservaRepositoryInterface.php';
require_once __DIR__ . '/../domain/entities/Reserva.php';
require_once __DIR__ . '/../infrastructure/database/MySQLReservaRepository.php';
require_once __DIR__ . '/../infrastructure/factories/RepositoryFactory.php';
require_once __DIR__ . '/../infrastructure/services/ReservaService.php';
require_once __DIR__ . '/../api/ReservaController.php';



use Infrastructure\Factories\RepositoryFactory;
use Infrastructure\Services\ReservaService;
use Api\ReservaController;

$conn = include __DIR__ . '/../config/db.php';
$factory = new RepositoryFactory($conn);
$service = new ReservaService($factory->createReservaRepository());
$controller = new ReservaController($service);

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST' && str_contains($requestUri, '/api/reservas')) {
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $response = $controller->postReserva($requestBody);
    echo $response;
} elseif ($requestMethod === 'GET' && isset($_GET['idHorario'])) {
    $controller->getDisponibilidad();
    exit;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada.']);
}
