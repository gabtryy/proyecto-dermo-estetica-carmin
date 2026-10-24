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

    private function validarDatos(): ?string
    {
        $nombre = trim((string) $this->nombreServicio);
        $precio = trim((string) $this->precio);

        if (mb_strlen($nombre, 'UTF-8') < 3 || mb_strlen($nombre, 'UTF-8') > 100) {
            return 'El nombre del servicio debe tener entre 3 y 100 caracteres.';
        }
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $precio) || (float) $precio <= 0) {
            return 'El precio debe ser mayor que cero y tener como máximo dos decimales.';
        }

        return null;
    }

    public function insertar(): array 
    {
        $errorValidacion = $this->validarDatos();
        if ($errorValidacion !== null) {
            return ['resultado' => 'error', 'mensaje' => $errorValidacion];
        }

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
        $idServicio = filter_var($this->idServicio, FILTER_VALIDATE_INT);
        if (!$idServicio || $idServicio < 1) {
            return ['resultado' => 'error', 'mensaje' => 'El servicio seleccionado no es válido.'];
        }
        $errorValidacion = $this->validarDatos();
        if ($errorValidacion !== null) {
            return ['resultado' => 'error', 'mensaje' => $errorValidacion];
        }

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
                ':idServicio'     => $idServicio,
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