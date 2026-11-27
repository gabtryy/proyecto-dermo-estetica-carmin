<?php

require_once('modelo/conexion.php');


class Servicio extends Conexion {
    
    // Atributos privados
    private $idServicio;
    private $nombreServicio;
    private $precio;
    private $descripcion;
    private $productos = [];
    

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

    function set_productos(array $valor) {
        $this->productos = $valor;
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
            $this->pdo->beginTransaction();
            $sql = "INSERT INTO servicio (nombreServicio, precio, descripcion)
                    VALUES (:nombreServicio, :precio, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreServicio' => $this->nombreServicio,
                ':precio'         => $this->precio,
                ':descripcion'    => $this->descripcion,
            ]);

            $idServicio = (int) $this->pdo->lastInsertId();
            $this->guardarProductos($idServicio);
            $this->pdo->commit();

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio registrado con éxito.'
            ];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
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
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$servicios) {
                return [];
            }

            $productos = $this->pdo->query(
                "SELECT d.idServicio, d.idProducto, d.cantidad,
                        p.nombreProducto, p.marca
                 FROM detalleproductoservicio d
                 INNER JOIN producto p ON p.idProducto = d.idProducto
                 ORDER BY p.nombreProducto ASC"
            )->fetchAll(PDO::FETCH_ASSOC);
            $productosPorServicio = [];
            foreach ($productos as $producto) {
                $productosPorServicio[(int) $producto['idServicio']][] = [
                    'idProducto' => (int) $producto['idProducto'],
                    'nombreProducto' => $producto['nombreProducto'],
                    'marca' => $producto['marca'],
                    'cantidad' => (int) $producto['cantidad'],
                ];
            }

            foreach ($servicios as &$servicio) {
                $servicio['productos'] = $productosPorServicio[(int) $servicio['idServicio']] ?? [];
            }
            unset($servicio);

            return $servicios;
        } catch (Exception $e) {
            return [];
        }
        
    }

    public function listarProductos(): array
    {
        $sql = "SELECT idProducto, nombreProducto, marca
                FROM producto
                ORDER BY nombreProducto ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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

        try {
            $this->pdo->beginTransaction();
            $existe = $this->pdo->prepare(
                "SELECT idServicio FROM servicio WHERE idServicio = :idServicio FOR UPDATE"
            );
            $existe->execute([':idServicio' => $idServicio]);
            if (!$existe->fetchColumn()) {
                $this->pdo->rollBack();
                return ['resultado' => 'error', 'mensaje' => 'El servicio seleccionado ya no existe.'];
            }

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

            $eliminarProductos = $this->pdo->prepare(
                "DELETE FROM detalleproductoservicio WHERE idServicio = :idServicio"
            );
            $eliminarProductos->execute([':idServicio' => $idServicio]);
            $this->guardarProductos($idServicio);
            $this->pdo->commit();

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio modificado correctamente.'
            ];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'resultado' => 'error',
                'mensaje'   => $e->getMessage()
            ];
        }
        
    }

    private function guardarProductos(int $idServicio): void
    {
        if (!$this->productos) {
            return;
        }

        $idsProducto = array_column($this->productos, 'idProducto');
        $marcadores = implode(',', array_fill(0, count($idsProducto), '?'));
        $consultar = $this->pdo->prepare(
            "SELECT idProducto FROM producto WHERE idProducto IN ($marcadores) FOR UPDATE"
        );
        $consultar->execute($idsProducto);
        $productosExistentes = array_map('intval', $consultar->fetchAll(PDO::FETCH_COLUMN));
        if (count($productosExistentes) !== count($idsProducto)) {
            throw new RuntimeException('Uno o más productos seleccionados ya no existen.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO detalleproductoservicio (idServicio, idProducto, cantidad)
             VALUES (:idServicio, :idProducto, :cantidad)"
        );
        foreach ($this->productos as $producto) {
            $insertar->execute([
                ':idServicio' => $idServicio,
                ':idProducto' => $producto['idProducto'],
                ':cantidad' => $producto['cantidad'],
            ]);
        }
    }
}
?>