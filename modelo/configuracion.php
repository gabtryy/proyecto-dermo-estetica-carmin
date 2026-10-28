<?php

require_once 'modelo/conexion.php';

class Configuracion extends Conexion
{
    public function listar(): array
    {
        $monedas = $this->pdo->query(
            "SELECT m.id_moneda, m.nombre_moneda, m.simbolo,
                    CASE WHEN m.id_moneda = 1 THEN COALESCE(
                        (SELECT tm.monto_tasa FROM tasa_moneda tm
                         WHERE tm.id_moneda = m.id_moneda AND tm.fecha_tasa = CURDATE()
                         ORDER BY tm.id_tasa DESC LIMIT 1), 1)
                        ELSE (SELECT tm.monto_tasa FROM tasa_moneda tm
                              WHERE tm.id_moneda = m.id_moneda AND tm.fecha_tasa = CURDATE()
                              ORDER BY tm.id_tasa DESC LIMIT 1)
                    END AS tasa_actual
             FROM moneda m
             ORDER BY m.nombre_moneda"
        )->fetchAll(PDO::FETCH_ASSOC);

        $metodos = $this->pdo->query(
            "SELECT idMetodoPago, nom_MetodoPago
             FROM metodo_pago
             ORDER BY nom_MetodoPago"
        )->fetchAll(PDO::FETCH_ASSOC);

        $tasas = $this->pdo->query(
            "SELECT t.id_tasa, t.fecha_tasa, t.monto_tasa,
                    m.nombre_moneda, m.simbolo
             FROM tasa_moneda t
             INNER JOIN moneda m ON m.id_moneda = t.id_moneda
             ORDER BY t.fecha_tasa DESC, t.id_tasa DESC
             LIMIT 50"
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'monedas' => $monedas,
            'metodosPago' => $metodos,
            'tasas' => $tasas,
        ];
    }

    public function agregarMoneda(string $nombre, string $simbolo): void
    {
        $existente = $this->pdo->prepare(
            "SELECT 1 FROM moneda WHERE nombre_moneda = :nombre LIMIT 1"
        );
        $existente->execute([':nombre' => $nombre]);
        if ($existente->fetchColumn()) {
            throw new DomainException('Ya existe una moneda con ese nombre.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO moneda (nombre_moneda, simbolo) VALUES (:nombre, :simbolo)"
        );
        $insertar->execute([':nombre' => $nombre, ':simbolo' => $simbolo]);
    }

    public function agregarMetodo(string $nombre): void
    {
        $existente = $this->pdo->prepare(
            "SELECT 1 FROM metodo_pago WHERE nom_MetodoPago = :nombre LIMIT 1"
        );
        $existente->execute([':nombre' => $nombre]);
        if ($existente->fetchColumn()) {
            throw new DomainException('Ya existe un método de pago con ese nombre.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO metodo_pago (nom_MetodoPago) VALUES (:nombre)"
        );
        $insertar->execute([':nombre' => $nombre]);
    }

    public function guardarTasa(int $idMoneda, string $fecha, string $tasa): void
    {
        $this->pdo->beginTransaction();
        try {
            $moneda = $this->pdo->prepare(
                "SELECT id_moneda FROM moneda WHERE id_moneda = :idMoneda FOR UPDATE"
            );
            $moneda->execute([':idMoneda' => $idMoneda]);
            if (!$moneda->fetchColumn()) {
                throw new DomainException('La moneda seleccionada ya no está disponible.');
            }

            $guardar = $this->pdo->prepare(
                "INSERT INTO tasa_moneda (id_moneda, fecha_tasa, monto_tasa)
                 VALUES (:idMoneda, :fecha, :tasa)"
            );
            $guardar->execute([
                ':idMoneda' => $idMoneda,
                ':fecha' => $fecha,
                ':tasa' => $tasa,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
