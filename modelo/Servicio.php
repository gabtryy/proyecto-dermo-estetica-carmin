<?php
require_once 'modelo/conexion.php';

class Servicio extends Conexion
{
    // Atributos privados
    private $id_servicio;
    private $nombre_servicio;
    private $precio;
    private $descripcion;
    private $ultimoError;
  
    // Setters
    public function set_id_servicio($valor) {
        $this->id_servicio = $valor;
    }
    public function set_nombre_servicio($valor) {
        $this->nombre_servicio = $valor;
    }
    public function set_precio($valor) {
        $this->precio = $valor;
    }
    public function set_descripcion($valor) {
        $this->descripcion = $valor;
    }
 
    // Getters
    public function get_id_servicio() {
        return $this->id_servicio;
    }
    public function get_nombre_servicio() {
        return $this->nombre_servicio;
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
                    (id_servicio, nombre_servicio, precio, descripcion)
                    VALUES (:id_servicio, :nombre_servicio, :precio, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_servicio' => $this->id_servicio,
                ':nombre_servicio' => $this->nombre_servicio,
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
        $sql = "SELECT id_servicio, nombre_servicio, precio, descripcion
                FROM servicio
                ORDER BY nombre_servicio ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function eliminar($id_servicio): bool
    {
        try {
            $sql = "DELETE FROM servicio WHERE id_servicio = :id_servicio       ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id_servicio' => $id_servicio]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function modificar(): bool
    {
        try {
            $sql = "UPDATE servicio SET 
                    nombre_servicio = :nombre_servicio,
                    precio = :precio,
                    descripcion = :descripcion
                WHERE id_servicio = :id_servicio";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre_servicio' => $this->nombre_servicio,
                ':precio' => $this->precio,
                ':descripcion' => $this->descripcion,
                ':id_servicio' => $this->id_servicio,
            ]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }
}
