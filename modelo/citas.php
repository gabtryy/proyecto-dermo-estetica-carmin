<?php
require_once 'modelo/conexion.php';

class Citas extends Conexion
{
    private $idCita;
    private $cedulaCliente;
    private $cedulaEsteticista;
    private $fechaCita;
    private $hora;
    private $servicios = [];

    public function set_idCita($valor) { $this->idCita = $valor; }
    public function set_cedulaCliente($valor) { $this->cedulaCliente = $valor; }
    public function set_cedulaEsteticista($valor) { $this->cedulaEsteticista = $valor; }
    public function set_fechaCita($valor) { $this->fechaCita = $valor; }
    public function set_hora($valor) { $this->hora = $valor; }
    public function set_servicios($valor) { $this->servicios = $valor; }

    public function get_idCita() { return $this->idCita; }
    public function get_cedulaCliente() { return $this->cedulaCliente; }
    public function get_cedulaEsteticista() { return $this->cedulaEsteticista; }
    public function get_fechaCita() { return $this->fechaCita; }
    public function get_hora() { return $this->hora; }
    public function get_servicios() { return $this->servicios; }

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
                           c.cedulaCliente,
                           c.cedulaEsteticista,
                           cl.nombreCliente,
                           e.nombreEsteticista,
                           c.fecha_cita,
                           c.hora_cita AS hora,
                           c.estado_cita,
                           GROUP_CONCAT(s.nombreServicio SEPARATOR ', ') AS servicios,
                           GROUP_CONCAT(s.idServicio SEPARATOR ',') AS idsServicios
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

    public function insertar(): array
    {
        $cedulaCliente = trim((string) $this->cedulaCliente);
        $cedulaEsteticista = trim((string) $this->cedulaEsteticista);
        $fechaCita = trim((string) $this->fechaCita);
        $hora = trim((string) $this->hora);
        $servicios = $this->servicios;

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

                $sql = "INSERT INTO citas (cedulaEsteticista, cedulaCliente, hora_cita, fecha_cita, estado_cita)
                    VALUES (:cedulaEsteticista, :cedulaCliente, :hora, :fecha_cita, :estado_cita)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':cedulaEsteticista' => $cedulaEsteticista,
                ':cedulaCliente' => $cedulaCliente,
                ':hora' => $hora,
                ':fecha_cita' => $fechaCita,
                ':estado_cita' => 'pendiente',
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

    public function modificar(): array
    {
        $idCita = (int) $this->idCita;
        $cedulaCliente = trim((string) $this->cedulaCliente);
        $cedulaEsteticista = trim((string) $this->cedulaEsteticista);
        $fechaCita = trim((string) $this->fechaCita);
        $hora = trim((string) $this->hora);
        $servicios = $this->servicios;

        if ($idCita <= 0 || $cedulaCliente === '' || $cedulaEsteticista === '' || $fechaCita === '' || $hora === '') {
            return ['ok' => false, 'mensaje' => 'Debe completar todos los datos de la cita.'];
        }

        if (!is_array($servicios) || count($servicios) === 0) {
            return ['ok' => false, 'mensaje' => 'Debe seleccionar al menos un servicio.'];
        }

        try {
            $stmtCliente = $this->pdo->prepare("SELECT 1 FROM cliente WHERE cedulaCliente = :cedula LIMIT 1");
            $stmtCliente->execute([':cedula' => $cedulaCliente]);
            $stmtEsteticista = $this->pdo->prepare("SELECT 1 FROM esteticista WHERE cedulaEsteticista = :cedula LIMIT 1");
            $stmtEsteticista->execute([':cedula' => $cedulaEsteticista]);
            if (!$stmtCliente->fetchColumn() || !$stmtEsteticista->fetchColumn()) {
                return ['ok' => false, 'mensaje' => 'El cliente o esteticista seleccionado no existe.'];
            }

            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare(
                "UPDATE citas SET cedulaCliente = :cedulaCliente, cedulaEsteticista = :cedulaEsteticista,
                 fecha_cita = :fecha_cita, hora_cita = :hora WHERE idCita = :idCita"
            );
            $stmt->execute([
                ':cedulaCliente' => $cedulaCliente,
                ':cedulaEsteticista' => $cedulaEsteticista,
                ':fecha_cita' => $fechaCita,
                ':hora' => $hora,
                ':idCita' => $idCita,
            ]);

            $stmtDetalle = $this->pdo->prepare("DELETE FROM detalle_citas WHERE idCita = :idCita");
            $stmtDetalle->execute([':idCita' => $idCita]);
            $stmtDetalle = $this->pdo->prepare(
                "INSERT INTO detalle_citas (idServicio, idCita) VALUES (:idServicio, :idCita)"
            );
            foreach ($servicios as $idServicio) {
                $idServicio = (int) $idServicio;
                if ($idServicio > 0) {
                    $stmtDetalle->execute([':idServicio' => $idServicio, ':idCita' => $idCita]);
                }
            }

            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Cita modificada correctamente.'];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['ok' => false, 'mensaje' => 'Error al modificar la cita: ' . $e->getMessage()];
        }
    }

    public function eliminar(): array
    {
        $idCita = (int) $this->idCita;
        try {
            $this->pdo->beginTransaction();
            $stmtDetalle = $this->pdo->prepare("DELETE FROM detalle_citas WHERE idCita = :idCita");
            $stmtDetalle->execute([':idCita' => $idCita]);
            $stmt = $this->pdo->prepare("DELETE FROM citas WHERE idCita = :idCita");
            $stmt->execute([':idCita' => $idCita]);
            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La cita no existe.'];
            }
            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Cita eliminada correctamente.'];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['ok' => false, 'mensaje' => 'Error al eliminar la cita: ' . $e->getMessage()];
        }
    }
}
?>
