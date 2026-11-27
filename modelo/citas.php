<?php
require_once 'modelo/conexion.php';
require_once 'modelo/productos.php';

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
            (new Productos())->asegurarConfiguracionCatalogo();
            $sql = "SELECT idServicio, nombreServicio, precio, descripcion
                    FROM servicio
                    ORDER BY nombreServicio ASC";

            $stmt = $this->pdo->query($sql);
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tasaStmt = $this->pdo->query(
                "SELECT tm.monto_tasa
                 FROM tasa_moneda tm
                 INNER JOIN moneda m ON m.id_moneda = tm.id_moneda
                 WHERE LOWER(m.nombre_moneda) IN ('dólar', 'dolar')
                   AND tm.fecha_tasa = CURDATE()
                 ORDER BY tm.id_tasa DESC
                 LIMIT 1"
            );
            $tasaDolar = (float) ($tasaStmt->fetchColumn() ?: 0);
            foreach ($servicios as &$servicio) {
                $servicio['precioBs'] = $tasaDolar > 0
                    ? round((float) $servicio['precio'] * $tasaDolar, 2)
                    : null;
                $servicio['tasaDolar'] = $tasaDolar;
            }
            unset($servicio);
            return $servicios;
        } catch (Throwable $e) {
            throw $e;
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
                    GROUP BY c.idCita, cl.nombreCliente, e.nombreEsteticista, c.fecha_cita,
                             c.hora_cita, c.estado_cita
                    ORDER BY c.fecha_cita DESC, c.hora_cita DESC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarPeriodo(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT c.fecha_cita,
                       c.hora_cita AS hora,
                       c.estado_cita,
                       cl.nombreCliente,
                       e.nombreEsteticista
                FROM citas c
                LEFT JOIN cliente cl ON cl.cedulaCliente = c.cedulaCliente
                LEFT JOIN esteticista e ON e.cedulaEsteticista = c.cedulaEsteticista
                WHERE c.fecha_cita BETWEEN :fechaInicio AND :fechaFin
                ORDER BY c.fecha_cita ASC, c.hora_cita ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $estadoStmt = $this->pdo->prepare(
                "SELECT estado_cita FROM citas WHERE idCita = :idCita FOR UPDATE"
            );
            $estadoStmt->execute([':idCita' => $idCita]);
            $estadoActual = $estadoStmt->fetchColumn();
            if ($estadoActual === false) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La cita ya no existe.'];
            }
            if (in_array(mb_strtolower(trim((string) $estadoActual), 'UTF-8'), ['finalizada', 'cancelada', 'pagada'], true)) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'No se puede editar una cita finalizada, cancelada o pagada.'];
            }

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
            $estadoStmt = $this->pdo->prepare(
                "SELECT estado_cita FROM citas WHERE idCita = :idCita FOR UPDATE"
            );
            $estadoStmt->execute([':idCita' => $idCita]);
            $estado = $estadoStmt->fetchColumn();
            if ($estado === false) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La cita no existe.'];
            }
            if (in_array(mb_strtolower(trim((string) $estado), 'UTF-8'), ['finalizada', 'pagada'], true)) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'No se puede eliminar una cita finalizada o pagada porque ya consumió insumos.'];
            }
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

    public function actualizarEstado(int $idCita, string $nuevoEstado): array
    {
        $estadosPermitidos = ['finalizada', 'cancelada', 'pagada'];
        if ($idCita < 1 || !in_array($nuevoEstado, $estadosPermitidos, true)) {
            return ['ok' => false, 'mensaje' => 'La cita o el estado indicados no son válidos.'];
        }

        try {
            (new Productos())->asegurarConfiguracionCatalogo();
            $this->pdo->beginTransaction();
            $consulta = $this->pdo->prepare(
                "SELECT estado_cita FROM citas WHERE idCita = :idCita FOR UPDATE"
            );
            $consulta->execute([':idCita' => $idCita]);
            $estadoActual = $consulta->fetchColumn();
            if ($estadoActual === false) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La cita ya no existe.'];
            }

            $estadoActual = mb_strtolower(trim((string) $estadoActual), 'UTF-8');
            if (in_array($estadoActual, ['cancelada', 'pagada'], true)) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La cita ya tiene un estado final y no se puede cambiar.'];
            }
            if ($estadoActual === '' || $estadoActual === 'pendiente') {
                if ($nuevoEstado === 'pagada') {
                    $this->pdo->rollBack();
                    return ['ok' => false, 'mensaje' => 'Primero marca la cita como finalizada para poder pagarla.'];
                }
                if ($nuevoEstado === 'finalizada') {
                    $errorInsumos = $this->descontarInsumosCita($idCita);
                    if ($errorInsumos !== null) {
                        $this->pdo->rollBack();
                        return ['ok' => false, 'mensaje' => $errorInsumos];
                    }
                }
            } elseif ($estadoActual === 'finalizada' && $nuevoEstado !== 'pagada') {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'Una cita finalizada solo puede marcarse como pagada.'];
            } elseif ($estadoActual !== 'finalizada') {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La transición de estado solicitada no es válida.'];
            }

            $actualizar = $this->pdo->prepare(
                "UPDATE citas SET estado_cita = :estado WHERE idCita = :idCita"
            );
            $actualizar->execute([
                ':estado' => $nuevoEstado,
                ':idCita' => $idCita,
            ]);
            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Estado de la cita actualizado correctamente.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudo actualizar el estado de la cita: ' . $e->getMessage());
            return [
                'ok' => false,
                'mensaje' => $e instanceof DomainException
                    ? $e->getMessage()
                    : 'No se pudo actualizar el estado de la cita.',
            ];
        }
    }

    private function descontarInsumosCita(int $idCita): ?string
    {
        $insumosStmt = $this->pdo->prepare(
            "SELECT dps.idProducto, dps.rendimiento, p.nombreProducto
             FROM detalle_citas dc
             INNER JOIN detalleproductoservicio dps ON dps.idServicio = dc.idServicio
             INNER JOIN producto p ON p.idProducto = dps.idProducto
             WHERE dc.idCita = :idCita
             ORDER BY dps.idProducto, dps.idServicio
             FOR UPDATE"
        );
        $insumosStmt->execute([':idCita' => $idCita]);

        $consumos = [];
        foreach ($insumosStmt->fetchAll(PDO::FETCH_ASSOC) as $insumo) {
            $idProducto = (int) $insumo['idProducto'];
            $rendimiento = (int) $insumo['rendimiento'];
            if ($idProducto < 1 || $rendimiento < 1) {
                throw new DomainException('La configuración de insumos de un servicio no es válida.');
            }
            if (!isset($consumos[$idProducto])) {
                $consumos[$idProducto] = [
                    'cantidad' => 0.0,
                    'nombre' => $insumo['nombreProducto'],
                ];
            }
            $consumos[$idProducto]['cantidad'] = round(
                $consumos[$idProducto]['cantidad'] + round(1 / $rendimiento, 6),
                6
            );
        }

        if (!$consumos) {
            return null;
        }

        $idsProducto = array_keys($consumos);
        sort($idsProducto, SORT_NUMERIC);
        $marcadores = implode(',', array_fill(0, count($idsProducto), '?'));
        $stockStmt = $this->pdo->prepare(
            "SELECT idProducto, cantidadActual
             FROM producto
             WHERE idProducto IN ($marcadores)
             ORDER BY idProducto
             FOR UPDATE"
        );
        $stockStmt->execute($idsProducto);
        $productos = $stockStmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        if (count($productos) !== count($consumos)) {
            return 'No se encontró el inventario de uno o más insumos del servicio.';
        }

        $actualizarStock = $this->pdo->prepare(
            "UPDATE producto SET cantidadActual = :cantidad
             WHERE idProducto = :idProducto"
        );
        foreach ($idsProducto as $idProducto) {
            $disponible = (float) $productos[$idProducto]['cantidadActual'];
            $requerido = $consumos[$idProducto]['cantidad'];
            if ($disponible + 0.000001 < $requerido) {
                return sprintf(
                    'Inventario insuficiente de %s: se requieren %s unidades y hay %s.',
                    $consumos[$idProducto]['nombre'],
                    number_format($requerido, 6, ',', '.'),
                    number_format($disponible, 6, ',', '.')
                );
            }

            $restante = round($disponible - $requerido, 6);
            if ($restante <= 0.000001) {
                $restante = 0.0;
            }
            $actualizarStock->execute([
                ':cantidad' => number_format($restante, 6, '.', ''),
                ':idProducto' => $idProducto,
            ]);
        }

        return null;
    }
}
?>
