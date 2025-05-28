<?php
namespace Infrastructure\Adapters;

use Domain\Interfaces\PagoApiInterface;

class PagoApiAdapter implements PagoApiInterface {
    private string $endpoint;

    public function __construct(string $endpoint) {
        $this->endpoint = $endpoint;
    }

    public function procesarPago(array $datos): array {
        $payload = json_encode($datos);

        $ch = curl_init($this->endpoint);
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
            return ['error' => 'Error CURL: ' . $error];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200
            ? json_decode($response, true)
            : ['error' => 'Pago no procesado.'];
    }
}