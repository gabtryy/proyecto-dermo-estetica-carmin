<?php

require_once 'modelo/conexion.php';

class Configuracion extends Conexion
{
    private $nombreMoneda = '';
    private $simbolo = '';
    private $nombreMetodo = '';
    private $idMoneda = 0;
    private $fechaTasa = '';
    private $montoTasa = '';

    public function set_nombreMoneda(string $valor): void
    {
        $this->nombreMoneda = trim($valor);
    }

    public function get_nombreMoneda(): string
    {
        return $this->nombreMoneda;
    }

    public function set_simbolo(string $valor): void
    {
        $this->simbolo = trim($valor);
    }

    public function get_simbolo(): string
    {
        return $this->simbolo;
    }

    public function set_nombreMetodo(string $valor): void
    {
        $this->nombreMetodo = trim($valor);
    }

    public function get_nombreMetodo(): string
    {
        return $this->nombreMetodo;
    }

    public function set_idMoneda(int $valor): void
    {
        $this->idMoneda = $valor;
    }

    public function get_idMoneda(): int
    {
        return $this->idMoneda;
    }

    public function set_fechaTasa(string $valor): void
    {
        $this->fechaTasa = trim($valor);
    }

    public function get_fechaTasa(): string
    {
        return $this->fechaTasa;
    }

    public function set_montoTasa(string $valor): void
    {
        $this->montoTasa = trim($valor);
    }

    public function get_montoTasa(): string
    {
        return $this->montoTasa;
    }

    private function validarMoneda(): ?string
    {
        if (!preg_match('/^.{1,50}$/us', $this->get_nombreMoneda())) {
            return 'Ingrese el nombre de la moneda, con un máximo de 50 caracteres.';
        }
        if (!preg_match('/^.{1,5}$/us', $this->get_simbolo())) {
            return 'Ingrese un símbolo de hasta 5 caracteres.';
        }

        return null;
    }

    private function validarMetodo(): ?string
    {
        if (!preg_match('/^.{1,50}$/us', $this->get_nombreMetodo())) {
            return 'Ingrese el nombre del método de pago, con un máximo de 50 caracteres.';
        }

        return null;
    }

    private function validarTasa(): ?string
    {
        $fecha = DateTime::createFromFormat('!Y-m-d', $this->get_fechaTasa());
        $erroresFecha = DateTime::getLastErrors();
        if (
            $this->get_idMoneda() < 1 ||
            !$fecha ||
            ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
            $fecha->format('Y-m-d') !== $this->get_fechaTasa() ||
            $this->get_fechaTasa() > date('Y-m-d')
        ) {
            return 'Indique una moneda válida y una fecha no futura.';
        }
        if (!preg_match('/^\d{1,6}(?:\.\d{1,4})?$/', $this->get_montoTasa()) || (float) $this->get_montoTasa() <= 0) {
            return 'La tasa debe ser mayor que cero y tener hasta 4 decimales.';
        }

        return null;
    }

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
        $this->set_nombreMoneda($nombre);
        $this->set_simbolo($simbolo);
        $errorValidacion = $this->validarMoneda();
        if ($errorValidacion !== null) {
            throw new DomainException($errorValidacion);
        }

        $existente = $this->pdo->prepare(
            "SELECT 1 FROM moneda WHERE nombre_moneda = :nombre LIMIT 1"
        );
        $existente->execute([':nombre' => $this->get_nombreMoneda()]);
        if ($existente->fetchColumn()) {
            throw new DomainException('Ya existe una moneda con ese nombre.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO moneda (nombre_moneda, simbolo) VALUES (:nombre, :simbolo)"
        );
        $insertar->execute([
            ':nombre' => $this->get_nombreMoneda(),
            ':simbolo' => $this->get_simbolo(),
        ]);
    }

    public function agregarMetodo(string $nombre): void
    {
        $this->set_nombreMetodo($nombre);
        $errorValidacion = $this->validarMetodo();
        if ($errorValidacion !== null) {
            throw new DomainException($errorValidacion);
        }

        $existente = $this->pdo->prepare(
            "SELECT 1 FROM metodo_pago WHERE nom_MetodoPago = :nombre LIMIT 1"
        );
        $existente->execute([':nombre' => $this->get_nombreMetodo()]);
        if ($existente->fetchColumn()) {
            throw new DomainException('Ya existe un método de pago con ese nombre.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO metodo_pago (nom_MetodoPago) VALUES (:nombre)"
        );
        $insertar->execute([':nombre' => $this->get_nombreMetodo()]);
    }

    public function guardarTasa(int $idMoneda, string $fecha, string $tasa): void
    {
        $this->set_idMoneda($idMoneda);
        $this->set_fechaTasa($fecha);
        $this->set_montoTasa($tasa);
        $errorValidacion = $this->validarTasa();
        if ($errorValidacion !== null) {
            throw new DomainException($errorValidacion);
        }

        $this->pdo->beginTransaction();
        try {
            $moneda = $this->pdo->prepare(
                "SELECT id_moneda FROM moneda WHERE id_moneda = :idMoneda FOR UPDATE"
            );
            $moneda->execute([':idMoneda' => $this->get_idMoneda()]);
            if (!$moneda->fetchColumn()) {
                throw new DomainException('La moneda seleccionada ya no está disponible.');
            }

            $guardar = $this->pdo->prepare(
                "INSERT INTO tasa_moneda (id_moneda, fecha_tasa, monto_tasa)
                 VALUES (:idMoneda, :fecha, :tasa)"
            );
            $guardar->execute([
                ':idMoneda' => $this->get_idMoneda(),
                ':fecha' => $this->get_fechaTasa(),
                ':tasa' => $this->get_montoTasa(),
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
