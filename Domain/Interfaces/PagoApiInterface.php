<?php
namespace Domain\Interfaces;

interface PagoApiInterface {
    public function procesarPago(array $datos): array;
}