<?php
require_once 'modelo/conexion.php';

class Productos extends Conexion
{
    // Atributos privados actualizados según la nueva tabla
    private $idProducto;
    private $nombreProducto;
    private $marca;
    private $idProveedor;
    private $precioProducto;
    private $cantidadActual;
    private $tipoProducto;
    private $ultimoError; 

    // Setters
    public function set_id($valor) {
        $this->idProducto = $valor;
    }
    public function set_nombreProducto($valor) {
        $this->nombreProducto = $valor;
    }
    // Alias para compatibilidad con el controlador
    public function set_nombre($valor) { 
        $this->nombreProducto = $valor; 
    }

    public function set_marca($valor) {
        $this->marca = $valor;
    }
    public function set_idProveedor($valor) { 
        $this->idProveedor = $valor; 
    }
    public function set_precioProducto($valor) {
        $this->precioProducto = $valor;
    }
    // Alias
    public function set_precio($valor) { 
        $this->precioProducto = $valor; 
    }
    public function set_cantidadActual($valor) {
        $this->cantidadActual = $valor;
    }
    // Alias
    public function set_cantidad($valor) { 
        $this->cantidadActual = $valor; 
    }
    public function set_tipoProducto($valor) {
        $this->tipoProducto = $valor;
    }

    // Getters
    public function get_id() {
        return $this->idProducto;
    }
    public function get_nombreProducto() {
        return $this->nombreProducto;
    }
    public function get_marca() {
        return $this->marca;
    }
    public function get_idProveedor() { 
        return $this->idProveedor; 
    }
    public function get_precioProducto() {
        return $this->precioProducto;
    }
    public function get_cantidadActual() {
        return $this->cantidadActual;
    }
    public function get_tipoProducto() {
        return $this->tipoProducto;
    }

    public function insertar(): array
    {
        try {
            $sql = "INSERT INTO producto (nombreProducto, marca, precioProducto, idProveedor, cantidadActual, tipoProducto) 
                    VALUES (:nombreProducto, :marca, :precioProducto, :idProveedor, :cantidadActual, :tipoProducto)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombreProducto' => $this->nombreProducto,
                ':marca' => $this->marca,
                ':precioProducto' => $this->precioProducto,
                ':idProveedor' => $this->idProveedor,
                ':cantidadActual' => $this->cantidadActual,
                ':tipoProducto' => $this->tipoProducto,
            ]);

            if ($ok) {
                return ['ok' => true, 'insertId' => $this->pdo->lastInsertId()];
            }

            $this->ultimoError = 'Error al ejecutar INSERT';
            return ['ok' => false, 'error' => $this->ultimoError];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function getUltimoError() {
        return $this->ultimoError ?? null;
    }

    // Exponer el objeto PDO para casos donde prefiramos ejecutar consultas directamente
    public function getPdo()
    {
        return $this->pdo;
    }

    public function listar(): array
    {
        $sql = "SELECT p.idProducto, p.nombreProducto, p.marca, p.precioProducto, p.cantidadActual, p.tipoProducto, p.idProveedor, prov.nombreProveedor
                FROM producto p
                LEFT JOIN proveedor prov ON p.idProveedor = prov.idProveedor
                ORDER BY p.nombreProducto ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProveedores(): array
    {
        $stmt = $this->pdo->query("SELECT idProveedor, nombreProveedor FROM proveedor ORDER BY nombreProveedor ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeId($id): array
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM producto WHERE idProducto = :idProducto LIMIT 1");
        $stmt->execute([':idProducto' => $id]);
        return (array) $stmt->fetchColumn();
    }

    public function eliminar($id): array
    {
        try {
            $sql = "DELETE FROM producto WHERE idProducto = :id";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([':id' => $id]);
            if ($ok) {
                return ['ok' => true];
            }
            $this->ultimoError = 'No se pudo eliminar el registro';
            return ['ok' => false, 'error' => $this->ultimoError];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function modificar(): array
    {
        try {
            $sql = "UPDATE producto SET 
                    nombreProducto = :nombreProducto,
                    marca = :marca,
                    precioProducto = :precioProducto,
                    idProveedor = :idProveedor,
                    cantidadActual = :cantidadActual,
                    tipoProducto = :tipoProducto
                WHERE idProducto = :id";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombreProducto' => $this->nombreProducto,
                ':marca' => $this->marca,
                ':precioProducto' => $this->precioProducto,
                ':idProveedor' => $this->idProveedor,
                ':cantidadActual' => $this->cantidadActual,
                ':tipoProducto' => $this->tipoProducto,
                ':id' => $this->idProducto,
            ]);

            if ($ok) {
                return ['ok' => true];
            }

            $this->ultimoError = 'Error al ejecutar UPDATE';
            return ['ok' => false, 'error' => $this->ultimoError];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }
}
?>

