<?php

require_once('modelo/conexion.php');


class Servicio extends Conexion {
    
    // Atributos privados
    private $idServicio;
    private $nombreServicio;
    private $precio;
    private $descripcion;
    

    function set_idServicio($valor) {
        $this->idServicio = $valor;
    }

    function set_nombreServicio($valor) {
        $this->nombreServicio = $valor;
    }
    
    function set_precio($valor) {
        $this->precio = $valor;
    }
    
    function set_descripcion($valor) {
        $this->descripcion = $valor;
    }
 
  
    public function get_idServicio() {
        return $this->idServicio;
    }
    public function get_nombreServicio() {
        return $this->nombreServicio;
    }
    public function get_precio() {
        return $this->precio;
    }
    public function get_descripcion() {
        return $this->descripcion;
    }

   
    public function insertar(): array 
    {
        try {
            $sql = "INSERT INTO servicio (nombreServicio, precio, descripcion)
                    VALUES (:nombreServicio, :precio, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreServicio' => $this->nombreServicio,
                ':precio'         => $this->precio,
                ':descripcion'    => $this->descripcion,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio registrado con éxito.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje'   => $e->getMessage()
            ];
        }
        
    }

  
    public function listar(): array
    {
        try {
            $sql = "SELECT idServicio, nombreServicio, precio, descripcion
                    FROM servicio
                    ORDER BY nombreServicio ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
        
    }

    public function eliminar(): array
    {
        try {
            $sql = "DELETE FROM servicio WHERE idServicio = :idServicio";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idServicio' => $this->idServicio]);

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio eliminado correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje'   => $e->getMessage()
            ];
        }
        
    }

    
    public function modificar(): array
    {
        try {
            $sql = "UPDATE servicio SET 
                        nombreServicio = :nombreServicio,
                        precio = :precio,
                        descripcion = :descripcion
                    WHERE idServicio = :idServicio";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreServicio' => $this->nombreServicio,
                ':precio'         => $this->precio,
                ':descripcion'    => $this->descripcion,
                ':idServicio'     => $this->idServicio,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio modificado correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje'   => $e->getMessage()
            ];
        }
        
    }
}
?>