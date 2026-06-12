<?php
require_once __DIR__ . '/conexion.php';

class Servicio extends Conexion
{
    // Atributos privados
    private $idServicio;
    private $nombreServicio;
    private $precio;
    private $descripcion;
  
    // Setters

    public function set_nombre_servicio($nombreServicio) {
        $this->nombreServicio = $nombreServicio;
    }

    // Método compatible con controlador (camelCase)
    public function set_nombreServicio($nombreServicio) {
        $this->nombreServicio = $nombreServicio;
    }
    public function set_precio($precio) {
        $this->precio = $precio;
    }
    public function set_descripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
 
    // Getters
    public function get_nombreServicio() {
        return $this->nombreServicio;
    }
    public function get_precio() {
        return $this->precio;
    }
    public function get_descripcion() {
        return $this->descripcion;
    }
    public function insertar(): bool
    {
        try {
            $sql = "INSERT INTO servicio 
                    (nombreServicio, precio, descripcion)
                    VALUES (:nombreServicio, :precio, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombreServicio' => $this->nombreServicio,
                ':precio' => $this->precio,
                ':descripcion' => $this->descripcion,
            ]);
        } catch (\PDOException $e) {
            // Guardar el error en una propiedad para que el controlador lo pueda leer
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function getUltimoError() {
        return $this->ultimoError ?? null;
    }

    public function listar(): array
    {
        $sql = "SELECT idServicio, nombreServicio, precio, descripcion
                FROM servicio
                ORDER BY nombreServicio ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function eliminar($idServicio): bool
    {
        try {
            $sql = "DELETE FROM servicio WHERE idServicio = :idServicio";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':idServicio' => $idServicio]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function modificar($idServicio): bool
    {
        try {
            $sql = "UPDATE servicio SET 
                    nombreServicio = :nombreServicio,
                    precio = :precio,
                    descripcion = :descripcion
                WHERE idServicio = :idServicio";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombreServicio' => $this->nombreServicio,
                ':precio' => $this->precio,
                ':descripcion' => $this->descripcion,
                ':idServicio' => $idServicio,
            ]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }
}
