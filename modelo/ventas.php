<?php

require_once 'modelo/conexion.php';

class Venta extends Conexion
{
    public function listar(): array
    {
     $sql = "SELECT v.idVenta, v.fechaCompra, c.nombreCliente,
                    GROUP_CONCAT(CONCAT(dv.cantidad, ' x ', p.nombreProducto) ORDER BY p.nombreProducto SEPARATOR ', ') AS productos,
                    SUM(dv.cantidad) AS cantidadProductos,
                    SUM(dv.cantidad * dv.precioUnitario) AS totalVenta,
                    mp.nom_MetodoPago, m.simbolo, pv.monto_pagado, pv.tasa_cobrada, pv.referencia
             FROM venta v
             INNER JOIN cliente c ON c.cedulaCliente = v.cedulaCliente
             INNER JOIN detalle_venta dv ON dv.idVenta = v.idVenta
             INNER JOIN producto p ON p.idProducto = dv.idProducto
             LEFT JOIN pago_venta pv ON pv.idVenta = v.idVenta
             LEFT JOIN metodo_pago mp ON mp.idMetodoPago = pv.idMetodoPago
             LEFT JOIN moneda m ON m.id_moneda = pv.id_moneda
             GROUP BY v.idVenta, v.fechaCompra, c.nombreCliente, mp.nom_MetodoPago,
                      m.simbolo, pv.monto_pagado, pv.tasa_cobrada, pv.referencia
             ORDER BY v.fechaCompra DESC, v.idVenta DESC";

     return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function catalogos(): array
    {
     return [
         'clientes' => $this->pdo->query(
             "SELECT cedulaCliente, nombreCliente FROM cliente ORDER BY nombreCliente"
         )->fetchAll(PDO::FETCH_ASSOC),
         'productos' => $this->pdo->query(
             "SELECT idProducto, nombreProducto, marca, precioProducto, cantidadActual
             FROM producto ORDER BY nombreProducto"
         )->fetchAll(PDO::FETCH_ASSOC),
         'metodosPago' => $this->pdo->query(
             "SELECT idMetodoPago, nom_MetodoPago FROM metodo_pago ORDER BY nom_MetodoPago"
         )->fetchAll(PDO::FETCH_ASSOC),
         'monedas' => $this->pdo->query(
             "SELECT id_moneda, nombre_moneda, simbolo FROM moneda ORDER BY nombre_moneda"
         )->fetchAll(PDO::FETCH_ASSOC),
     ];
    }

    public function obtener(int $idVenta): ?array
    {
        $venta = $this->pdo->prepare(
            "SELECT v.idVenta, v.cedulaCliente, v.fechaCompra, pv.idMetodoPago, pv.id_moneda,
                    pv.tasa_cobrada, pv.referencia
             FROM venta v
             LEFT JOIN pago_venta pv ON pv.idVenta = v.idVenta
             WHERE v.idVenta = :idVenta"
        );
        $venta->execute([':idVenta' => $idVenta]);
        $datos = $venta->fetch(PDO::FETCH_ASSOC);
        if (!$datos) {
            return null;
        }

        $detalle = $this->pdo->prepare(
            "SELECT dv.idProducto, dv.cantidad, dv.precioUnitario, p.nombreProducto,
                    p.precioProducto, p.cantidadActual
             FROM detalle_venta dv
             INNER JOIN producto p ON p.idProducto = dv.idProducto
             WHERE dv.idVenta = :idVenta
             ORDER BY p.nombreProducto"
        );
        $detalle->execute([':idVenta' => $idVenta]);
        $datos['productos'] = $detalle->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    public function registrar(array $datos): array
    {
     $cedulaCliente = filter_var($datos['cedulaCliente'] ?? null, FILTER_VALIDATE_INT);
     $fechaCompraEntrada = trim((string) ($datos['fechaCompra'] ?? ''));
     $idMetodoPago = filter_var($datos['idMetodoPago'] ?? null, FILTER_VALIDATE_INT);
     $idMoneda = filter_var($datos['id_moneda'] ?? null, FILTER_VALIDATE_INT);
     $tasa = filter_var($datos['tasa_cobrada'] ?? null, FILTER_VALIDATE_FLOAT);
     $referencia = trim((string) ($datos['referencia'] ?? ''));
     $items = $datos['productos'] ?? null;

     if (!$cedulaCliente || !$idMetodoPago || !$idMoneda) {
         return ['ok' => false, 'mensaje' => 'Seleccione un cliente, método de pago y moneda válidos.'];
     }
     $fechaCompra = DateTime::createFromFormat('!Y-m-d\TH:i', $fechaCompraEntrada);
     $erroresFecha = DateTime::getLastErrors();
     if (
         !$fechaCompra ||
         ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
         $fechaCompra->format('Y-m-d\TH:i') !== $fechaCompraEntrada
     ) {
         return ['ok' => false, 'mensaje' => 'Ingrese una fecha y hora de venta válida.'];
     }
     $fechaCompra = $fechaCompra->format('Y-m-d H:i:s');
     if ($tasa === false || $tasa <= 0 || $tasa > 999999.9999) {
         return ['ok' => false, 'mensaje' => 'La tasa de cambio debe ser un número mayor que cero.'];
     }
     if (strlen($referencia) > 50) {
         return ['ok' => false, 'mensaje' => 'La referencia no puede superar los 50 caracteres.'];
     }
     if (!is_array($items) || count($items) === 0) {
         return ['ok' => false, 'mensaje' => 'Agregue al menos un producto a la venta.'];
     }

     $cantidades = [];
     foreach ($items as $item) {
         $idProducto = filter_var($item['idProducto'] ?? null, FILTER_VALIDATE_INT);
         $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT);
         if (!$idProducto || !$cantidad || $cantidad < 1) {
             return ['ok' => false, 'mensaje' => 'Hay productos o cantidades inválidas en la venta.'];
         }
         $cantidades[$idProducto] = ($cantidades[$idProducto] ?? 0) + $cantidad;
     }

     try {
         $this->pdo->beginTransaction();

         $verificar = $this->pdo->prepare(
             "SELECT
                 EXISTS(SELECT 1 FROM cliente WHERE cedulaCliente = :cliente) AS cliente,
                 EXISTS(SELECT 1 FROM metodo_pago WHERE idMetodoPago = :metodo) AS metodo,
                 EXISTS(SELECT 1 FROM moneda WHERE id_moneda = :moneda) AS moneda"
         );
         $verificar->execute([
             ':cliente' => $cedulaCliente,
             ':metodo' => $idMetodoPago,
             ':moneda' => $idMoneda,
         ]);
         $validos = $verificar->fetch(PDO::FETCH_ASSOC);
         if (!$validos || !$validos['cliente'] || !$validos['metodo'] || !$validos['moneda']) {
             $this->pdo->rollBack();
             return ['ok' => false, 'mensaje' => 'El cliente o los datos de pago seleccionados ya no están disponibles.'];
         }

         $marcadores = implode(',', array_fill(0, count($cantidades), '?'));
         $bloquear = $this->pdo->prepare(
             "SELECT idProducto, nombreProducto, precioProducto, cantidadActual
              FROM producto WHERE idProducto IN ($marcadores) FOR UPDATE"
         );
         $bloquear->execute(array_keys($cantidades));
         $productos = $bloquear->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

         if (count($productos) !== count($cantidades)) {
             $this->pdo->rollBack();
             return ['ok' => false, 'mensaje' => 'Uno o más productos ya no están disponibles.'];
         }

         $total = 0.0;
         foreach ($cantidades as $idProducto => $cantidad) {
             $producto = $productos[$idProducto];
             if ((int) $producto['cantidadActual'] < $cantidad) {
                 $this->pdo->rollBack();
                 return ['ok' => false, 'mensaje' => 'Inventario insuficiente para ' . $producto['nombreProducto'] . '.'];
             }
             $total += (float) $producto['precioProducto'] * $cantidad;
         }
         $total = round($total, 2);
         $montoPagado = round($total * $tasa, 2);

         $insertarVenta = $this->pdo->prepare(
             "INSERT INTO venta (cedulaCliente, fechaCompra) VALUES (:cedulaCliente, :fechaCompra)"
         );
         $insertarVenta->execute([
             ':cedulaCliente' => $cedulaCliente,
             ':fechaCompra' => $fechaCompra,
         ]);
         $idVenta = (int) $this->pdo->lastInsertId();

         $insertarDetalle = $this->pdo->prepare(
             "INSERT INTO detalle_venta (idVenta, idProducto, cantidad, precioUnitario)
              VALUES (:idVenta, :idProducto, :cantidad, :precioUnitario)"
         );
         $actualizarStock = $this->pdo->prepare(
             "UPDATE producto SET cantidadActual = cantidadActual - :cantidad
              WHERE idProducto = :idProducto AND cantidadActual >= :cantidad"
         );

         foreach ($cantidades as $idProducto => $cantidad) {
             $insertarDetalle->execute([
                 ':idVenta' => $idVenta,
                 ':idProducto' => $idProducto,
                 ':cantidad' => $cantidad,
                 ':precioUnitario' => $productos[$idProducto]['precioProducto'],
             ]);
             $actualizarStock->execute([
                 ':cantidad' => $cantidad,
                 ':idProducto' => $idProducto,
             ]);
             if ($actualizarStock->rowCount() !== 1) {
                 throw new RuntimeException('No se pudo actualizar el inventario.');
             }
         }

         $insertarPago = $this->pdo->prepare(
             "INSERT INTO pago_venta
                 (idVenta, idMetodoPago, id_moneda, monto_pagado, tasa_cobrada, fecha_pago, referencia)
              VALUES (:idVenta, :idMetodoPago, :idMoneda, :monto, :tasa, NOW(), :referencia)"
         );
         $insertarPago->execute([
             ':idVenta' => $idVenta,
             ':idMetodoPago' => $idMetodoPago,
             ':idMoneda' => $idMoneda,
             ':monto' => $montoPagado,
             ':tasa' => $tasa,
             ':referencia' => $referencia !== '' ? $referencia : null,
         ]);

         $this->pdo->commit();
         return ['ok' => true, 'mensaje' => 'Venta registrada correctamente.', 'idVenta' => $idVenta];
     } catch (Throwable $e) {
         if ($this->pdo->inTransaction()) {
             $this->pdo->rollBack();
         }
         error_log('No se pudo registrar la venta: ' . $e->getMessage());
         return ['ok' => false, 'mensaje' => 'No se pudo registrar la venta. Verifique los datos e intente nuevamente.'];
     }
    }

    public function modificar(int $idVenta, array $datos): array
    {
        $cedulaCliente = filter_var($datos['cedulaCliente'] ?? null, FILTER_VALIDATE_INT);
        $idMetodoPago = filter_var($datos['idMetodoPago'] ?? null, FILTER_VALIDATE_INT);
        $idMoneda = filter_var($datos['id_moneda'] ?? null, FILTER_VALIDATE_INT);
        $tasa = filter_var($datos['tasa_cobrada'] ?? null, FILTER_VALIDATE_FLOAT);
        $referencia = trim((string) ($datos['referencia'] ?? ''));
        $fechaEntrada = trim((string) ($datos['fechaCompra'] ?? ''));
        $items = $datos['productos'] ?? null;

        if ($idVenta < 1 || !$cedulaCliente || !$idMetodoPago || !$idMoneda) {
            return ['ok' => false, 'mensaje' => 'La venta, el cliente y los datos de pago deben ser válidos.'];
        }
        $fecha = DateTime::createFromFormat('!Y-m-d\TH:i', $fechaEntrada);
        $erroresFecha = DateTime::getLastErrors();
        if (
            !$fecha ||
            ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
            $fecha->format('Y-m-d\TH:i') !== $fechaEntrada
        ) {
            return ['ok' => false, 'mensaje' => 'Ingrese una fecha y hora de venta válida.'];
        }
        if ($tasa === false || $tasa <= 0 || $tasa > 999999.9999 || strlen($referencia) > 50) {
            return ['ok' => false, 'mensaje' => 'Revise la tasa de cambio y la referencia del pago.'];
        }
        if (!is_array($items) || count($items) === 0) {
            return ['ok' => false, 'mensaje' => 'Agregue al menos un producto a la venta.'];
        }

        $cantidades = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                return ['ok' => false, 'mensaje' => 'Hay productos o cantidades inválidas en la venta.'];
            }
            $idProducto = filter_var($item['idProducto'] ?? null, FILTER_VALIDATE_INT);
            $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT);
            if (!$idProducto || !$cantidad || $cantidad < 1) {
                return ['ok' => false, 'mensaje' => 'Hay productos o cantidades inválidas en la venta.'];
            }
            $cantidades[$idProducto] = ($cantidades[$idProducto] ?? 0) + $cantidad;
        }

        try {
            $this->pdo->beginTransaction();

            $ventaStmt = $this->pdo->prepare(
                "SELECT idVenta FROM venta WHERE idVenta = :idVenta FOR UPDATE"
            );
            $ventaStmt->execute([':idVenta' => $idVenta]);
            if (!$ventaStmt->fetchColumn()) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La venta ya no existe.'];
            }

            $verificar = $this->pdo->prepare(
                "SELECT
                    EXISTS(SELECT 1 FROM cliente WHERE cedulaCliente = :cliente) AS cliente,
                    EXISTS(SELECT 1 FROM metodo_pago WHERE idMetodoPago = :metodo) AS metodo,
                    EXISTS(SELECT 1 FROM moneda WHERE id_moneda = :moneda) AS moneda"
            );
            $verificar->execute([
                ':cliente' => $cedulaCliente,
                ':metodo' => $idMetodoPago,
                ':moneda' => $idMoneda,
            ]);
            $validos = $verificar->fetch(PDO::FETCH_ASSOC);
            if (!$validos || !$validos['cliente'] || !$validos['metodo'] || !$validos['moneda']) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'El cliente o los datos de pago seleccionados ya no están disponibles.'];
            }

            $detallesStmt = $this->pdo->prepare(
                "SELECT idProducto, cantidad, precioUnitario
                 FROM detalle_venta WHERE idVenta = :idVenta FOR UPDATE"
            );
            $detallesStmt->execute([':idVenta' => $idVenta]);
            $detalleAnterior = [];
            foreach ($detallesStmt->fetchAll(PDO::FETCH_ASSOC) as $itemAnterior) {
                $detalleAnterior[(int) $itemAnterior['idProducto']] = $itemAnterior;
            }

            $idsProducto = array_unique(array_merge(array_keys($cantidades), array_keys($detalleAnterior)));
            sort($idsProducto, SORT_NUMERIC);
            $marcadores = implode(',', array_fill(0, count($idsProducto), '?'));
            $bloquear = $this->pdo->prepare(
                "SELECT idProducto, nombreProducto, precioProducto, cantidadActual
                 FROM producto WHERE idProducto IN ($marcadores) ORDER BY idProducto FOR UPDATE"
            );
            $bloquear->execute($idsProducto);
            $productos = $bloquear->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            if (count($productos) !== count($idsProducto)) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'Uno o más productos ya no están disponibles.'];
            }

            $total = 0.0;
            foreach ($cantidades as $idProducto => $cantidad) {
                $disponiblesTrasRestaurar = (int) $productos[$idProducto]['cantidadActual']
                    + (int) ($detalleAnterior[$idProducto]['cantidad'] ?? 0);
                if ($disponiblesTrasRestaurar < $cantidad) {
                    $this->pdo->rollBack();
                    return ['ok' => false, 'mensaje' => 'Inventario insuficiente para ' . $productos[$idProducto]['nombreProducto'] . '.'];
                }
                $precio = isset($detalleAnterior[$idProducto])
                    ? (float) $detalleAnterior[$idProducto]['precioUnitario']
                    : (float) $productos[$idProducto]['precioProducto'];
                $productos[$idProducto]['precioVenta'] = $precio;
                $total += $precio * $cantidad;
            }
            $total = round($total, 2);
            $montoPagado = round($total * $tasa, 2);

            $restaurarStock = $this->pdo->prepare(
                "UPDATE producto SET cantidadActual = cantidadActual + :cantidad WHERE idProducto = :idProducto"
            );
            foreach ($detalleAnterior as $idProducto => $itemAnterior) {
                $restaurarStock->execute([
                    ':cantidad' => $itemAnterior['cantidad'],
                    ':idProducto' => $idProducto,
                ]);
            }

            $actualizarVenta = $this->pdo->prepare(
                "UPDATE venta SET cedulaCliente = :cedulaCliente, fechaCompra = :fechaCompra
                 WHERE idVenta = :idVenta"
            );
            $actualizarVenta->execute([
                ':cedulaCliente' => $cedulaCliente,
                ':fechaCompra' => $fecha->format('Y-m-d H:i:s'),
                ':idVenta' => $idVenta,
            ]);

            $borrarDetalle = $this->pdo->prepare("DELETE FROM detalle_venta WHERE idVenta = :idVenta");
            $borrarDetalle->execute([':idVenta' => $idVenta]);
            $insertarDetalle = $this->pdo->prepare(
                "INSERT INTO detalle_venta (idVenta, idProducto, cantidad, precioUnitario)
                 VALUES (:idVenta, :idProducto, :cantidad, :precioUnitario)"
            );
            $descontarStock = $this->pdo->prepare(
                "UPDATE producto SET cantidadActual = cantidadActual - :cantidad
                 WHERE idProducto = :idProducto AND cantidadActual >= :cantidad"
            );
            foreach ($cantidades as $idProducto => $cantidad) {
                $insertarDetalle->execute([
                    ':idVenta' => $idVenta,
                    ':idProducto' => $idProducto,
                    ':cantidad' => $cantidad,
                    ':precioUnitario' => $productos[$idProducto]['precioVenta'],
                ]);
                $descontarStock->execute([
                    ':cantidad' => $cantidad,
                    ':idProducto' => $idProducto,
                ]);
                if ($descontarStock->rowCount() !== 1) {
                    throw new RuntimeException('No se pudo actualizar el inventario.');
                }
            }

            $actualizarPago = $this->pdo->prepare(
                "UPDATE pago_venta
                 SET idMetodoPago = :idMetodoPago, id_moneda = :idMoneda, monto_pagado = :monto,
                     tasa_cobrada = :tasa, referencia = :referencia, fecha_pago = :fechaPago
                 WHERE idVenta = :idVenta"
            );
            $actualizarPago->execute([
                ':idMetodoPago' => $idMetodoPago,
                ':idMoneda' => $idMoneda,
                ':monto' => $montoPagado,
                ':tasa' => $tasa,
                ':referencia' => $referencia !== '' ? $referencia : null,
                ':fechaPago' => $fecha->format('Y-m-d H:i:s'),
                ':idVenta' => $idVenta,
            ]);
            if ($actualizarPago->rowCount() === 0) {
                $comprobarPago = $this->pdo->prepare("SELECT 1 FROM pago_venta WHERE idVenta = :idVenta");
                $comprobarPago->execute([':idVenta' => $idVenta]);
                if (!$comprobarPago->fetchColumn()) {
                    $insertarPago = $this->pdo->prepare(
                        "INSERT INTO pago_venta
                            (idVenta, idMetodoPago, id_moneda, monto_pagado, tasa_cobrada, fecha_pago, referencia)
                         VALUES (:idVenta, :idMetodoPago, :idMoneda, :monto, :tasa, :fechaPago, :referencia)"
                    );
                    $insertarPago->execute([
                        ':idVenta' => $idVenta,
                        ':idMetodoPago' => $idMetodoPago,
                        ':idMoneda' => $idMoneda,
                        ':monto' => $montoPagado,
                        ':tasa' => $tasa,
                        ':fechaPago' => $fecha->format('Y-m-d H:i:s'),
                        ':referencia' => $referencia !== '' ? $referencia : null,
                    ]);
                }
            }

            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Venta modificada correctamente.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudo modificar la venta: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo modificar la venta. Verifique los datos e intente nuevamente.'];
        }
    }

    public function eliminar(int $idVenta): array
    {
        if ($idVenta < 1) {
            return ['ok' => false, 'mensaje' => 'La venta indicada no es válida.'];
        }

        try {
            $this->pdo->beginTransaction();
            $venta = $this->pdo->prepare(
                "SELECT idVenta FROM venta WHERE idVenta = :idVenta FOR UPDATE"
            );
            $venta->execute([':idVenta' => $idVenta]);
            if (!$venta->fetchColumn()) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'La venta ya no existe.'];
            }

            $detalles = $this->pdo->prepare(
                "SELECT idProducto, cantidad FROM detalle_venta
                 WHERE idVenta = :idVenta ORDER BY idProducto FOR UPDATE"
            );
            $detalles->execute([':idVenta' => $idVenta]);
            $items = $detalles->fetchAll(PDO::FETCH_ASSOC);

            if ($items) {
                $ids = array_column($items, 'idProducto');
                $marcadores = implode(',', array_fill(0, count($ids), '?'));
                $bloquearStock = $this->pdo->prepare(
                    "SELECT idProducto FROM producto
                     WHERE idProducto IN ($marcadores) ORDER BY idProducto FOR UPDATE"
                );
                $bloquearStock->execute($ids);
                $existentes = array_map('intval', $bloquearStock->fetchAll(PDO::FETCH_COLUMN));
                $restaurar = $this->pdo->prepare(
                    "UPDATE producto SET cantidadActual = cantidadActual + :cantidad
                     WHERE idProducto = :idProducto"
                );
                foreach ($items as $item) {
                    if (in_array((int) $item['idProducto'], $existentes, true)) {
                        $restaurar->execute([
                            ':cantidad' => $item['cantidad'],
                            ':idProducto' => $item['idProducto'],
                        ]);
                    }
                }
            }

            $borrar = $this->pdo->prepare("DELETE FROM venta WHERE idVenta = :idVenta");
            $borrar->execute([':idVenta' => $idVenta]);
            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => 'Venta eliminada y stock restaurado correctamente.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudo eliminar la venta: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo eliminar la venta. Intente nuevamente.'];
        }
    }
}
