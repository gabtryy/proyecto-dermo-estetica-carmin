<?php
require_once 'modelo/conexion.php';

class Reportes extends Conexion
{
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
