<?php
namespace Domain\Interfaces;

use Domain\Interfaces\ReservaRepositoryInterface;

interface RepositoryFactoryInterface {
    public function createReservaRepository(): ReservaRepositoryInterface;
}
