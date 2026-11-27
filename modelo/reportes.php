<?php
require_once 'modelo/conexion.php';

class Reportes extends Conexion
{
    public function listarClientes(): array
    {
        $stmt = $this->pdo->query(
            "SELECT cedulaCliente, nombreCliente FROM cliente ORDER BY nombreCliente ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEsteticistas(): array
    {
        $stmt = $this->pdo->query(
            "SELECT cedulaEsteticista, nombreEsteticista
             FROM esteticista ORDER BY nombreEsteticista ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function usuarios(string $fechaInicio, string $fechaFin, string $cedulaCliente): array
    {
        $sql = "SELECT c.cedulaCliente, c.nombreCliente,
                       COALESCE((
                           SELECT GROUP_CONCAT(
                               CONCAT(DATE(v.fechaCompra), ': ', dv.cantidad, ' x ', p.nombreProducto,
                                      ' ($', FORMAT(dv.cantidad * dv.precioUnitario, 2, 'es_ES'), ')')
                               ORDER BY v.fechaCompra DESC, p.nombreProducto
                               SEPARATOR ' | '
                           )
                           FROM venta v
                           INNER JOIN detalle_venta dv ON dv.idVenta = v.idVenta
                           INNER JOIN producto p ON p.idProducto = dv.idProducto
                           WHERE v.cedulaCliente = c.cedulaCliente
                             AND v.fechaCompra >= :fechaInicio
                             AND v.fechaCompra < DATE_ADD(:fechaFin, INTERVAL 1 DAY)
                       ), 'Sin compras') AS compras,
                       (
                           SELECT COUNT(*)
                           FROM venta v
                           WHERE v.cedulaCliente = c.cedulaCliente
                             AND v.fechaCompra >= :fechaInicio2
                             AND v.fechaCompra < DATE_ADD(:fechaFin2, INTERVAL 1 DAY)
                       ) AS cantidadCompras,
                       COALESCE((
                           SELECT GROUP_CONCAT(
                               CONCAT(ci.fecha_cita, ' ', DATE_FORMAT(ci.hora_cita, '%H:%i'), ': ',
                                      COALESCE(e.nombreEsteticista, 'Esteticista sin asignar'), ' - ',
                                      COALESCE(s.nombreServicio, 'Servicio sin asignar'), ' (',
                                      COALESCE(ci.estado_cita, 'sin estado'), ')')
                               ORDER BY ci.fecha_cita DESC, ci.hora_cita DESC
                               SEPARATOR ' | '
                           )
                           FROM citas ci
                           LEFT JOIN esteticista e ON e.cedulaEsteticista = ci.cedulaEsteticista
                           LEFT JOIN detalle_citas dc ON dc.idCita = ci.idCita
                           LEFT JOIN servicio s ON s.idServicio = dc.idServicio
                           WHERE ci.cedulaCliente = c.cedulaCliente
                             AND ci.fecha_cita BETWEEN :fechaInicio3 AND :fechaFin3
                       ), 'Sin citas en el período') AS citasAsistidas,
                       (
                           SELECT COUNT(*)
                           FROM citas ci
                           WHERE ci.cedulaCliente = c.cedulaCliente
                             AND ci.fecha_cita BETWEEN :fechaInicio4 AND :fechaFin4
                       ) AS cantidadCitas
                FROM cliente c
                WHERE c.cedulaCliente = :cedulaCliente";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio . ' 00:00:00',
            ':fechaFin' => $fechaFin,
            ':fechaInicio2' => $fechaInicio . ' 00:00:00',
            ':fechaFin2' => $fechaFin,
            ':fechaInicio3' => $fechaInicio,
            ':fechaFin3' => $fechaFin,
            ':fechaInicio4' => $fechaInicio,
            ':fechaFin4' => $fechaFin,
            ':cedulaCliente' => $cedulaCliente,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function esteticistas(string $fechaInicio, string $fechaFin, string $cedulaEsteticista): array
    {
        $sql = "SELECT e.cedulaEsteticista, e.nombreEsteticista,
                       COALESCE((
                           SELECT GROUP_CONCAT(
                               CONCAT(ci.fecha_cita, ' ', DATE_FORMAT(ci.hora_cita, '%H:%i'), ': ',
                                      COALESCE(cl.nombreCliente, 'Cliente sin asignar'), ' - ',
                                      COALESCE(s.nombreServicio, 'Servicio sin asignar'), ' (',
                                      COALESCE(ci.estado_cita, 'sin estado'), ')')
                               ORDER BY ci.fecha_cita DESC, ci.hora_cita DESC
                               SEPARATOR ' | '
                           )
                           FROM citas ci
                           LEFT JOIN cliente cl ON cl.cedulaCliente = ci.cedulaCliente
                           LEFT JOIN detalle_citas dc ON dc.idCita = ci.idCita
                           LEFT JOIN servicio s ON s.idServicio = dc.idServicio
                           WHERE ci.cedulaEsteticista = e.cedulaEsteticista
                             AND ci.fecha_cita BETWEEN :fechaInicio AND :fechaFin
                       ), 'Sin citas en el período') AS citasAsistidas,
                       (
                           SELECT COUNT(*)
                           FROM citas ci
                           WHERE ci.cedulaEsteticista = e.cedulaEsteticista
                             AND ci.fecha_cita BETWEEN :fechaInicio2 AND :fechaFin2
                       ) AS cantidadCitas
                FROM esteticista e
                WHERE e.cedulaEsteticista = :cedulaEsteticista";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin,
            ':fechaInicio2' => $fechaInicio,
            ':fechaFin2' => $fechaFin,
            ':cedulaEsteticista' => $cedulaEsteticista,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ventas(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT v.idVenta, v.fechaCompra, c.nombreCliente,
                       GROUP_CONCAT(
                           CONCAT(dv.cantidad, ' x ', p.nombreProducto)
                           ORDER BY p.nombreProducto SEPARATOR ', '
                       ) AS productos,
                       SUM(dv.cantidad) AS cantidadProductos,
                       SUM(dv.cantidad * dv.precioUnitario) AS totalVenta,
                       mp.nom_MetodoPago, m.simbolo, pv.monto_pagado
                FROM venta v
                INNER JOIN cliente c ON c.cedulaCliente = v.cedulaCliente
                INNER JOIN detalle_venta dv ON dv.idVenta = v.idVenta
                INNER JOIN producto p ON p.idProducto = dv.idProducto
                LEFT JOIN pago_venta pv ON pv.idVenta = v.idVenta
                LEFT JOIN metodo_pago mp ON mp.idMetodoPago = pv.idMetodoPago
                LEFT JOIN moneda m ON m.id_moneda = pv.id_moneda
                WHERE v.fechaCompra >= :fechaInicio
                  AND v.fechaCompra < DATE_ADD(:fechaFin, INTERVAL 1 DAY)
                GROUP BY v.idVenta, v.fechaCompra, c.nombreCliente,
                         mp.nom_MetodoPago, m.simbolo, pv.monto_pagado
                ORDER BY v.fechaCompra DESC, v.idVenta DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio . ' 00:00:00',
            ':fechaFin' => $fechaFin,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function citas(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT c.idCita, c.fecha_cita, c.hora_cita,
                       cl.nombreCliente, e.nombreEsteticista, c.estado_cita,
                       GROUP_CONCAT(s.nombreServicio ORDER BY s.nombreServicio SEPARATOR ', ') AS servicios
                FROM citas c
                LEFT JOIN cliente cl ON cl.cedulaCliente = c.cedulaCliente
                LEFT JOIN esteticista e ON e.cedulaEsteticista = c.cedulaEsteticista
                LEFT JOIN detalle_citas dc ON dc.idCita = c.idCita
                LEFT JOIN servicio s ON s.idServicio = dc.idServicio
                WHERE c.fecha_cita BETWEEN :fechaInicio AND :fechaFin
                GROUP BY c.idCita, c.fecha_cita, c.hora_cita, cl.nombreCliente,
                         e.nombreEsteticista, c.estado_cita
                ORDER BY c.fecha_cita DESC, c.hora_cita DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
