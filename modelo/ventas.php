<?php

require_once('modelo/conexion.php');

class Venta extends Conexion {

    private $cedulaCliente;
    private $idProducto;
    private $idMetodoPago;
    private $fechaCompra;
    private $cantidad;
    private $totalVenta;

    function set_cedulaCliente($valor) {
        $this->cedulaCliente = $valor;
    }

    function set_idProducto($valor) {
        $this->idProducto = $valor;
    }

    function set_idMetodoPago($valor) {
        $this->idMetodoPago = $valor;
    }

    function set_fechaCompra($valor) {
        $this->fechaCompra = $valor;
    }

    function set_cantidad($valor) {
        $this->cantidad = $valor;
    }

    function set_totalVenta($valor) {
        $this->totalVenta = $valor;
    }

    public function get_cedulaCliente() {
        return $this->cedulaCliente;
    }

    public function get_idProducto() {
        return $this->idProducto;
    }

    public function get_idMetodoPago() {
        return $this->idMetodoPago;
    }

    public function get_fechaCompra() {
        return $this->fechaCompra;
    }

    public function get_cantidad() {
        return $this->cantidad;
    }

    public function get_totalVenta() {
        return $this->totalVenta;
    }

    public function insertar(): array 
    {
        try {
            $sql = "INSERT INTO venta (cedulaCliente, idProducto, idMetodoPago, fechaCompra, cantidad, totalVenta)
                    VALUES (:cedulaCliente, :idProducto, :idMetodoPago, :fechaCompra, :cantidad, :totalVenta)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':idProducto' => $this->idProducto,
                ':idMetodoPago' => $this->idMetodoPago,
                ':fechaCompra' => $this->fechaCompra,
                ':cantidad' => $this->cantidad,
                ':totalVenta' => $this->totalVenta,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Venta registrada con éxito.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function listar(): array
    {
        try {
            $sql = "SELECT v.cedulaCliente, v.idProducto, v.idMetodoPago, v.fechaCompra, v.cantidad, v.totalVenta,
                           c.nombreCliente, p.nombreProducto, mp.nom_MetodoPago
                    FROM venta v
                    INNER JOIN cliente c ON v.cedulaCliente = c.cedulaCliente
                    INNER JOIN producto p ON v.idProducto = p.idProducto
                    INNER JOIN metodo_pago mp ON v.idMetodoPago = mp.idMetodoPago
                    ORDER BY v.fechaCompra DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function eliminar(): array
    {
        try {
            $sql = "DELETE FROM venta WHERE cedulaCliente = :cedulaCliente AND idProducto = :idProducto";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':idProducto' => $this->idProducto,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Venta eliminada correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function modificar(): array
    {
        try {
            $sql = "UPDATE venta SET 
                        idMetodoPago = :idMetodoPago,
                        fechaCompra = :fechaCompra,
                        cantidad = :cantidad,
                        totalVenta = :totalVenta
                    WHERE cedulaCliente = :cedulaCliente AND idProducto = :idProducto";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':idMetodoPago' => $this->idMetodoPago,
                ':fechaCompra' => $this->fechaCompra,
                ':cantidad' => $this->cantidad,
                ':totalVenta' => $this->totalVenta,
                ':cedulaCliente' => $this->cedulaCliente,
                ':idProducto' => $this->idProducto,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Venta modificada correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
?>
