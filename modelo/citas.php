<?php
require_once 'modelo/conexion.php';

class Citas extends Conexion
{
    public function listarClientes(): array
    {
        try {
            $sql = "SELECT cedulaCliente AS cedula, nombreCliente AS nombre
                    FROM cliente
                    ORDER BY nombreCliente ASC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarEsteticistas(): array
    {
        try {
            $sql = "SELECT cedulaEsteticista AS cedula, nombreEsteticista AS nombre
                    FROM esteticista
                    ORDER BY nombreEsteticista ASC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarServicios(): array
    {
        try {
            $sql = "SELECT idServicio, nombreServicio, precio, descripcion
                    FROM servicio
                    ORDER BY nombreServicio ASC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function consultar(): array
    {
        try {
            $sql = "SELECT c.idCita,
                           cl.nombreCliente,
                           e.nombreEsteticista,
                           c.fecha_cita,
                           c.hora_cita AS hora,
                           GROUP_CONCAT(s.nombreServicio SEPARATOR ', ') AS servicios
                    FROM citas c
                    LEFT JOIN cliente cl ON cl.cedulaCliente = c.cedulaCliente
                    LEFT JOIN esteticista e ON e.cedulaEsteticista = c.cedulaEsteticista
                    LEFT JOIN detalle_citas dc ON dc.idCita = c.idCita
                    LEFT JOIN servicio s ON s.idServicio = dc.idServicio
                    GROUP BY c.idCita, cl.nombreCliente, e.nombreEsteticista, c.fecha_cita, c.hora_cita
                    ORDER BY c.fecha_cita DESC, c.hora_cita DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function insertar(array $datos): array
    {
        $cedulaCliente = trim((string) ($datos['cedulaCliente'] ?? ''));
        $cedulaEsteticista = trim((string) ($datos['cedulaEsteticista'] ?? ''));
        $fechaCita = trim((string) ($datos['fecha_cita'] ?? ''));
        $hora = trim((string) ($datos['hora'] ?? ''));
        $servicios = $datos['servicios'] ?? [];

        if ($cedulaCliente === '' || $cedulaEsteticista === '' || $fechaCita === '' || $hora === '') {
            return ['ok' => false, 'mensaje' => 'Debe seleccionar cliente, esteticista, fecha y hora.'];
        }

        if (!is_array($servicios) || count($servicios) === 0) {
            return ['ok' => false, 'mensaje' => 'Debe seleccionar al menos un servicio.'];
        }

        try {
            $stmtCliente = $this->pdo->prepare("SELECT 1 FROM cliente WHERE cedulaCliente = :cedula LIMIT 1");
            $stmtCliente->execute([':cedula' => $cedulaCliente]);
            if (!$stmtCliente->fetchColumn()) {
                return ['ok' => false, 'mensaje' => 'El cliente seleccionado no existe.'];
            }

            $stmtEsteticista = $this->pdo->prepare("SELECT 1 FROM esteticista WHERE cedulaEsteticista = :cedula LIMIT 1");
            $stmtEsteticista->execute([':cedula' => $cedulaEsteticista]);
            if (!$stmtEsteticista->fetchColumn()) {
                return ['ok' => false, 'mensaje' => 'El esteticista seleccionado no existe.'];
            }

            $this->pdo->beginTransaction();

            $sql = "INSERT INTO citas (cedulaEsteticista, cedulaCliente, hora_cita, fecha_cita)
                    VALUES (:cedulaEsteticista, :cedulaCliente, :hora, :fecha_cita)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':cedulaEsteticista' => $cedulaEsteticista,
                ':cedulaCliente' => $cedulaCliente,
                ':hora' => $hora,
                ':fecha_cita' => $fechaCita,
            ]);

            if (!$ok) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'No se pudo guardar la cita.'];
            }

            $idCita = (int) $this->pdo->lastInsertId();

            foreach ($servicios as $idServicio) {
                $idServicio = (int) $idServicio;
                if ($idServicio <= 0) {
                    continue;
                }

                $stmtDetalle = $this->pdo->prepare(
                    "INSERT INTO detalle_citas (idServicio, idCita) VALUES (:idServicio, :idCita)"
                );
                $stmtDetalle->execute([
                    ':idServicio' => $idServicio,
                    ':idCita' => $idCita,
                ]);
            }

            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Cita registrada correctamente.'];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['ok' => false, 'mensaje' => 'Error al registrar la cita: ' . $e->getMessage()];
        }
    }
}
?>
