<?php
namespace Infrastructure\Factories;

use Domain\Interfaces\ReservaRepositoryInterface;
use Infrastructure\Database\MySQLReservaRepository;
use PDO;

class RepositoryFactory {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function createReservaRepository(): ReservaRepositoryInterface {
        return new MySQLReservaRepository($this->conn);
    }
} 