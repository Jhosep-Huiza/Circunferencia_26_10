<?php
require_once __DIR__ . '/../config/Database.php';

class Circunferencia
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->conectar();
    }

    /** Fórmulas: diámetro = 2r, área = πr², perímetro = 2πr */
    public static function calcular(float $radio): array
    {
        return [
            'diametro'  => round($radio * 2, 4),
            'area'      => round(M_PI * $radio ** 2, 4),
            'perimetro' => round(2 * M_PI * $radio, 4),
        ];
    }

    public function listar(string $buscar = ''): array
    {
        if ($buscar === '') {
            return $this->pdo->query("SELECT * FROM circunferencia ORDER BY id_circunferencia DESC")->fetchAll();
        }

        $sql = "SELECT * FROM circunferencia
                WHERE detalle LIKE :d OR CAST(radio AS CHAR) LIKE :r
                   OR CAST(area AS CHAR) LIKE :a OR CAST(perimetro AS CHAR) LIKE :p
                ORDER BY id_circunferencia DESC";
        $stmt = $this->pdo->prepare($sql);
        $v = '%' . $buscar . '%';
        $stmt->execute(['d' => $v, 'r' => $v, 'a' => $v, 'p' => $v]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM circunferencia WHERE id_circunferencia = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function guardar(string $detalle, float $radio): bool
    {
        $c = self::calcular($radio);
        $stmt = $this->pdo->prepare(
            "INSERT INTO circunferencia(detalle, radio, diametro, area, perimetro)
             VALUES(:detalle, :radio, :diametro, :area, :perimetro)"
        );
        return $stmt->execute(['detalle' => $detalle, 'radio' => $radio] + $c);
    }

    public function actualizar(int $id, string $detalle, float $radio): bool
    {
        $c = self::calcular($radio);
        $stmt = $this->pdo->prepare(
            "UPDATE circunferencia
             SET detalle = :detalle, radio = :radio, diametro = :diametro,
                 area = :area, perimetro = :perimetro
             WHERE id_circunferencia = :id"
        );
        return $stmt->execute(['id' => $id, 'detalle' => $detalle, 'radio' => $radio] + $c);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM circunferencia WHERE id_circunferencia = :id");
        return $stmt->execute(['id' => $id]);
    }
}