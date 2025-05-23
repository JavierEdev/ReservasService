<?php

namespace Infrastructure\Database;

use Domain\Interfaces\ReservaRepositoryInterface;
use PDO;
use Exception;

class MySQLReservaRepository implements ReservaRepositoryInterface {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function verificarDisponibilidad(int $idHorario, int $cantidad): bool {
        $sql = "SELECT b.capacidad - COALESCE(SUM(r.cantidad_asientos), 0) AS disponibles
                FROM horario h
                JOIN bus b ON h.id_bus = b.id_bus
                LEFT JOIN reserva r ON h.id_horario = r.id_horario AND r.estado != 'cancelada'
                WHERE h.id_horario = :idHorario AND h.estado = 1
                GROUP BY b.capacidad";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idHorario' => $idHorario]);
        $disponibles = $stmt->fetchColumn();

        if ($disponibles === false) {
            throw new Exception('Horario no existente o inactivo');
        }

        return $disponibles >= $cantidad;
    }

    public function crearReserva(array $data): string {
        $intentos = 0;
        $codigo = null;

        do {
            $codigo = 'BOLETO' . strtoupper(bin2hex(random_bytes(4)));
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reserva WHERE codigo_boleto = :codigo");
            $stmt->execute(['codigo' => $codigo]);
            $exists = $stmt->fetchColumn() > 0;
            $intentos++;
        } while ($exists && $intentos < 5);

        if ($exists) {
            throw new Exception("No se pudo generar un código de boleto único. Intenta nuevamente.");
        }

        $sql = "INSERT INTO reserva (id_horario, cantidad_asientos, estado, codigo_boleto, correo_cliente, fecha_reserva)
                VALUES (:id_horario, :cantidad_asientos, 'reservada', :codigo_boleto, :correo_cliente, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'id_horario' => $data['idHorario'],
            'cantidad_asientos' => $data['cantidad'],
            'codigo_boleto' => $codigo,
            'correo_cliente' => $data['correo']
        ]);

        return $codigo;
    }

    public function obtenerDisponibilidad(int $idHorario): int {
        $sql = "SELECT b.capacidad - COALESCE(SUM(r.cantidad_asientos), 0) AS disponibles
                FROM horario h
                JOIN bus b ON h.id_bus = b.id_bus
                LEFT JOIN reserva r ON h.id_horario = r.id_horario AND r.estado != 'cancelada'
                WHERE h.id_horario = :idHorario AND h.estado = 1
                GROUP BY b.capacidad";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idHorario' => $idHorario]);
        $disponibles = $stmt->fetchColumn();

        return $disponibles !== false ? (int)$disponibles : -1;
    }

}
