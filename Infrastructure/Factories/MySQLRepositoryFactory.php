<?php
namespace Infrastructure\Factories;

use Domain\Interfaces\ReservaRepositoryInterface;
use Domain\Interfaces\RepositoryFactoryInterface;
use Infrastructure\Database\MySQLReservaRepository;
use PDO;

class MySQLRepositoryFactory implements RepositoryFactoryInterface {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function createReservaRepository(): ReservaRepositoryInterface {
        return new MySQLReservaRepository($this->conn);
    }
}
