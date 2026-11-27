<?php

require_once 'modelo/conexion.php';
require_once 'modelo/productos.php';

class Venta extends Conexion
{
    private $idVenta = 0;
    private $cedulaCliente = 0;
    private $fechaCompra = '';
    private $idMetodoPago = 0;
    private $idMoneda = 0;
    private $tasaCobrada = 0.0;
    private $referencia = '';
    private $productos = [];

    public function set_idVenta(int $valor): void
    {
        $this->idVenta = $valor;
    }

    public function get_idVenta(): int
    {
        return $this->idVenta;
    }

    public function set_cedulaCliente(int $valor): void
    {
        $this->cedulaCliente = $valor;
    }

    public function get_cedulaCliente(): int
    {
        return $this->cedulaCliente;
    }

    public function set_fechaCompra(string $valor): void
    {
        $this->fechaCompra = $valor;
    }

    public function get_fechaCompra(): string
    {
        return $this->fechaCompra;
    }

    public function set_idMetodoPago(int $valor): void
    {
        $this->idMetodoPago = $valor;
    }

    public function get_idMetodoPago(): int
    {
        return $this->idMetodoPago;
    }

    public function set_idMoneda(int $valor): void
    {
        $this->idMoneda = $valor;
    }

    public function get_idMoneda(): int
    {
        return $this->idMoneda;
    }

    public function set_tasaCobrada(float $valor): void
    {
        $this->tasaCobrada = $valor;
    }

    public function get_tasaCobrada(): float
    {
        return $this->tasaCobrada;
    }

    public function set_referencia(string $valor): void
    {
        $this->referencia = trim($valor);
    }

    public function get_referencia(): string
    {
        return $this->referencia;
    }

    public function set_productos(array $valor): void
    {
        $this->productos = $valor;
    }

    public function get_productos(): array
    {
        return $this->productos;
    }

    private function asignarDatos(array $datos, ?int $idVenta = null): void
    {
        $this->set_cedulaCliente((int) ($datos['cedulaCliente'] ?? 0));
        $this->set_fechaCompra(str_replace('T', ' ', (string) ($datos['fechaCompra'] ?? '')) . ':00');
        $this->set_idMetodoPago((int) ($datos['idMetodoPago'] ?? 0));
        $this->set_idMoneda((int) ($datos['id_moneda'] ?? 0));
        $this->set_tasaCobrada((float) ($datos['tasa_cobrada'] ?? 0));
        $this->set_referencia((string) ($datos['referencia'] ?? ''));

        $cantidades = [];
        foreach ((array) ($datos['productos'] ?? []) as $item) {
            $item = (array) $item;
            $idProducto = (int) ($item['idProducto'] ?? 0);
            $cantidad = (int) ($item['cantidad'] ?? 0);
            $cantidades[$idProducto] = ($cantidades[$idProducto] ?? 0) + $cantidad;
        }
        $this->set_productos($cantidades);
        if ($idVenta !== null) {
            $this->set_idVenta($idVenta);
        }
    }

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
     $productosModelo = new Productos();
     $productosModelo->asegurarConfiguracionCatalogo();
     $monedaBolivar = $this->pdo->query(
         "SELECT id_moneda FROM moneda
          WHERE LOWER(nombre_moneda) IN ('bolívar', 'bolivar')
          ORDER BY id_moneda LIMIT 1"
     )->fetchColumn();
     $tasaDolarStmt = $this->pdo->query(
         "SELECT tm.monto_tasa
          FROM tasa_moneda tm
          INNER JOIN moneda m ON m.id_moneda = tm.id_moneda
          WHERE LOWER(m.nombre_moneda) IN ('dólar', 'dolar')
            AND tm.fecha_tasa = CURDATE()
          ORDER BY tm.id_tasa DESC LIMIT 1"
     );
     $tasaDolar = (float) ($tasaDolarStmt->fetchColumn() ?: 0);
     $productos = $this->pdo->query(
          "SELECT p.idProducto, p.nombreProducto, p.marca, p.precioProducto, p.cantidadActual
           FROM producto p
           INNER JOIN tipo_producto tp ON tp.id_tipo = p.id_tipo
           WHERE LOWER(tp.nombre_tipo) IN ('venta', 'ambos')
           ORDER BY p.nombreProducto"
     )->fetchAll(PDO::FETCH_ASSOC);
     foreach ($productos as &$producto) {
          $producto['precioUSD'] = (float) $producto['precioProducto'];
          $producto['precioBs'] = $tasaDolar > 0
              ? round($producto['precioUSD'] * $tasaDolar, 2)
              : null;
     }
     unset($producto);
     return [
         'clientes' => $this->pdo->query(
             "SELECT cedulaCliente, nombreCliente FROM cliente ORDER BY nombreCliente"
         )->fetchAll(PDO::FETCH_ASSOC),
         'productos' => $productos,
         'tasaDolar' => $tasaDolar,
         'idMonedaBolivar' => $monedaBolivar ? (int) $monedaBolivar : null,
         'metodosPago' => $this->pdo->query(
             "SELECT idMetodoPago, nom_MetodoPago FROM metodo_pago ORDER BY nom_MetodoPago"
         )->fetchAll(PDO::FETCH_ASSOC),
     ];
    }

    private function obtenerIdMonedaBolivar(): int
    {
        $stmt = $this->pdo->query(
            "SELECT id_moneda FROM moneda
             WHERE LOWER(nombre_moneda) IN ('bolívar', 'bolivar')
             ORDER BY id_moneda LIMIT 1"
        );
        $idMoneda = (int) $stmt->fetchColumn();
        if ($idMoneda < 1) {
            throw new DomainException('Registra la moneda Bolívar en Configuración antes de guardar ventas.');
        }
        return $idMoneda;
    }

    private function obtenerTasaDolarHoy(): float
    {
        $stmt = $this->pdo->query(
            "SELECT tm.monto_tasa
             FROM tasa_moneda tm
             INNER JOIN moneda m ON m.id_moneda = tm.id_moneda
             WHERE LOWER(m.nombre_moneda) IN ('dólar', 'dolar')
               AND tm.fecha_tasa = CURDATE()
             ORDER BY tm.id_tasa DESC
             LIMIT 1"
        );
        return (float) ($stmt->fetchColumn() ?: 0);
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
        $this->asignarDatos($datos);

        $cedulaCliente = $this->get_cedulaCliente();
        $fechaCompra = $this->get_fechaCompra();
        $idMetodoPago = $this->get_idMetodoPago();
        $referencia = $this->get_referencia();
        $cantidades = $this->get_productos();

     try {
         (new Productos())->asegurarConfiguracionCatalogo();
         if (!$cantidades) {
             return ['ok' => false, 'mensaje' => 'Agrega al menos un producto a la venta.'];
         }
         $idMoneda = $this->obtenerIdMonedaBolivar();
         $tasa = $this->obtenerTasaDolarHoy();
         if ($tasa <= 0) {
             return ['ok' => false, 'mensaje' => 'Registra la tasa del dólar de hoy en Configuración antes de registrar ventas.'];
         }
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
             "SELECT p.idProducto, p.nombreProducto, p.precioProducto, p.cantidadActual,
                     LOWER(tp.nombre_tipo) AS tipoProducto
              FROM producto p
              LEFT JOIN tipo_producto tp ON tp.id_tipo = p.id_tipo
              WHERE p.idProducto IN ($marcadores) FOR UPDATE"
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
             if (!in_array($producto['tipoProducto'], ['venta', 'ambos'], true)) {
                 $this->pdo->rollBack();
                 return ['ok' => false, 'mensaje' => 'Solo se pueden vender productos clasificados como Venta o Ambos.'];
             }
             if ((float) $producto['cantidadActual'] < $cantidad) {
                 $this->pdo->rollBack();
                 return ['ok' => false, 'mensaje' => 'Inventario insuficiente para ' . $producto['nombreProducto'] . '.'];
             }
             $productos[$idProducto]['precioVentaBs'] = round((float) $producto['precioProducto'] * $tasa, 2);
             $total += $productos[$idProducto]['precioVentaBs'] * $cantidad;
         }
         $total = round($total, 2);
         $montoPagado = $total;

         $insertarVenta = $this->pdo->prepare(
             "INSERT INTO venta (cedulaCliente, fechaCompra) VALUES (:cedulaCliente, :fechaCompra)"
         );
         $insertarVenta->execute([
             ':cedulaCliente' => $cedulaCliente,
             ':fechaCompra' => $fechaCompra,
         ]);
         $idVenta = (int) $this->pdo->lastInsertId();
         $this->set_idVenta($idVenta);

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
                 ':precioUnitario' => $productos[$idProducto]['precioVentaBs'],
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
         return [
             'ok' => false,
             'mensaje' => $e instanceof DomainException
                 ? $e->getMessage()
                 : 'No se pudo registrar la venta. Verifique los datos e intente nuevamente.',
         ];
     }
    }

    public function modificar(int $idVenta, array $datos): array
    {
        $this->asignarDatos($datos, $idVenta);

        $idVenta = $this->get_idVenta();
        $cedulaCliente = $this->get_cedulaCliente();
        $idMetodoPago = $this->get_idMetodoPago();
        $referencia = $this->get_referencia();
        $cantidades = $this->get_productos();

        try {
            (new Productos())->asegurarConfiguracionCatalogo();
            if (!$cantidades) {
                return ['ok' => false, 'mensaje' => 'La venta debe conservar al menos un producto.'];
            }
            $idMoneda = $this->obtenerIdMonedaBolivar();
            $tasa = $this->obtenerTasaDolarHoy();
            if ($tasa <= 0) {
                return ['ok' => false, 'mensaje' => 'Registra la tasa del dólar de hoy en Configuración antes de modificar ventas.'];
            }
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
                "SELECT p.idProducto, p.nombreProducto, p.precioProducto, p.cantidadActual,
                        LOWER(tp.nombre_tipo) AS tipoProducto
                 FROM producto p
                 LEFT JOIN tipo_producto tp ON tp.id_tipo = p.id_tipo
                 WHERE p.idProducto IN ($marcadores) ORDER BY p.idProducto FOR UPDATE"
            );
            $bloquear->execute($idsProducto);
            $productos = $bloquear->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            if (count($productos) !== count($idsProducto)) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'Uno o más productos ya no están disponibles.'];
            }

            $total = 0.0;
            foreach ($cantidades as $idProducto => $cantidad) {
                if (!in_array($productos[$idProducto]['tipoProducto'], ['venta', 'ambos'], true)) {
                    $cantidadAnterior = (int) ($detalleAnterior[$idProducto]['cantidad'] ?? 0);
                    if ($cantidadAnterior < 1 || $cantidad > $cantidadAnterior) {
                        $this->pdo->rollBack();
                        return ['ok' => false, 'mensaje' => 'Solo se pueden agregar productos clasificados como Venta o Ambos.'];
                    }
                }
                $disponiblesTrasRestaurar = (float) $productos[$idProducto]['cantidadActual']
                    + (int) ($detalleAnterior[$idProducto]['cantidad'] ?? 0);
                if ($disponiblesTrasRestaurar < $cantidad) {
                    $this->pdo->rollBack();
                    return ['ok' => false, 'mensaje' => 'Inventario insuficiente para ' . $productos[$idProducto]['nombreProducto'] . '.'];
                }
                $precioBs = isset($detalleAnterior[$idProducto])
                    ? (float) $detalleAnterior[$idProducto]['precioUnitario']
                    : round((float) $productos[$idProducto]['precioProducto'] * $tasa, 2);
                $productos[$idProducto]['precioVentaBs'] = $precioBs;
                $total += $precioBs * $cantidad;
            }
            $total = round($total, 2);
            $montoPagado = $total;

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
                ':fechaCompra' => $this->get_fechaCompra(),
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
                    ':precioUnitario' => $productos[$idProducto]['precioVentaBs'],
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
                ':fechaPago' => $this->get_fechaCompra(),
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
                        ':fechaPago' => $this->get_fechaCompra(),
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
            return [
                'ok' => false,
                'mensaje' => $e instanceof DomainException
                    ? $e->getMessage()
                    : 'No se pudo modificar la venta. Verifique los datos e intente nuevamente.',
            ];
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
