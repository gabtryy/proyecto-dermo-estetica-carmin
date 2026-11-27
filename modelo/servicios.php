<?php

require_once('modelo/conexion.php');
require_once 'modelo/productos.php';


class Servicio extends Conexion {
    
    // Atributos privados
    private $idServicio;
    private $nombreServicio;
    private $precio;
    private $descripcion;
    private $productosServicio = [];
    

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

    function set_productosServicio(array $valor): void {
        $this->productosServicio = $valor;
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
            (new Productos())->asegurarConfiguracionCatalogo();
            if ($this->obtenerTasaDolarHoy() <= 0) {
                throw new DomainException('Registra la tasa del dólar de hoy en Configuración antes de guardar precios.');
            }
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
            $this->guardarProductosServicio($idServicio);
            $this->pdo->commit();

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio registrado con éxito.'
            ];
        } catch (DomainException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'resultado' => 'error',
                'mensaje'   => $e->getMessage()
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudo registrar el servicio: ' . $e->getMessage());
            return [
                'resultado' => 'error',
                'mensaje'   => 'No se pudo registrar el servicio.'
            ];
        }
        
    }

  
    public function listar(): array
    {
        try {
            (new Productos())->asegurarConfiguracionCatalogo();
            $sql = "SELECT s.idServicio, s.nombreServicio, s.precio, s.descripcion,
                           GROUP_CONCAT(
                               CONCAT(dps.idProducto, ':', dps.cantidad, ':', dps.rendimiento)
                               ORDER BY dps.idProducto SEPARATOR ','
                           ) AS productosServicio,
                           GROUP_CONCAT(
                               CONCAT(p.nombreProducto, ' · rinde ', dps.rendimiento, ' sesiones por unidad')
                               ORDER BY p.nombreProducto SEPARATOR ', '
                           ) AS insumos
                    FROM servicio s
                    LEFT JOIN detalleproductoservicio dps ON dps.idServicio = s.idServicio
                    LEFT JOIN producto p ON p.idProducto = dps.idProducto
                    GROUP BY s.idServicio, s.nombreServicio, s.precio, s.descripcion
                    ORDER BY s.nombreServicio ASC";
            $stmt = $this->pdo->query($sql);
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tasaStmt = $this->pdo->query(
                "SELECT tm.monto_tasa
                 FROM tasa_moneda tm
                 INNER JOIN moneda m ON m.id_moneda = tm.id_moneda
                 WHERE LOWER(m.nombre_moneda) IN ('dólar', 'dolar')
                   AND tm.fecha_tasa = CURDATE()
                 ORDER BY tm.id_tasa DESC
                 LIMIT 1"
            );
            $tasaDolar = (float) ($tasaStmt->fetchColumn() ?: 0);
            foreach ($servicios as &$servicio) {
                $servicio['precioUSD'] = (float) $servicio['precio'];
                $servicio['precioBs'] = $tasaDolar > 0
                    ? round($servicio['precioUSD'] * $tasaDolar, 2)
                    : null;
            }
            unset($servicio);
            return $servicios;
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    public function catalogosProductosServicio(): array
    {
        $productosModelo = new Productos();
        $productosModelo->asegurarConfiguracionCatalogo();
        $stmt = $this->pdo->query(
            "SELECT p.idProducto, p.nombreProducto, p.cantidadActual, tp.nombre_tipo AS usoProducto
             FROM producto p
             INNER JOIN tipo_producto tp ON tp.id_tipo = p.id_tipo
             WHERE LOWER(tp.nombre_tipo) IN ('insumo', 'ambos')
             ORDER BY p.nombreProducto ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    private function guardarProductosServicio(int $idServicio): void
    {
        $productos = [];
        foreach ($this->productosServicio as $idProducto => $configuracion) {
            $idProducto = filter_var($idProducto, FILTER_VALIDATE_INT);
            if (is_array($configuracion)) {
                $rendimiento = filter_var($configuracion['rendimiento'] ?? null, FILTER_VALIDATE_INT);
            } else {
                $rendimiento = 1;
            }
            $cantidad = 1;
            if (
                !$idProducto || $idProducto < 1 ||
                !$rendimiento || $rendimiento < 1 || $rendimiento > 1000000
            ) {
                throw new DomainException('Verifique los productos y el rendimiento de cada insumo asignado al servicio.');
            }
            $productos[$idProducto] = [
                'cantidad' => $cantidad,
                'rendimiento' => $rendimiento,
            ];
        }

        if (!$productos) {
            return;
        }

        $marcadores = implode(',', array_fill(0, count($productos), '?'));
        $validar = $this->pdo->prepare(
            "SELECT p.idProducto
             FROM producto p
             INNER JOIN tipo_producto tp ON tp.id_tipo = p.id_tipo
             WHERE p.idProducto IN ($marcadores)
               AND LOWER(tp.nombre_tipo) IN ('insumo', 'ambos')
             ORDER BY p.idProducto
             FOR UPDATE"
        );
        $validar->execute(array_keys($productos));
        if (count($validar->fetchAll(PDO::FETCH_COLUMN)) !== count($productos)) {
            throw new DomainException('Solo se pueden asignar a servicios productos clasificados como Insumo o Ambos.');
        }

        $insertar = $this->pdo->prepare(
            "INSERT INTO detalleproductoservicio (idServicio, idProducto, cantidad, rendimiento)
             VALUES (:idServicio, :idProducto, :cantidad, :rendimiento)"
        );
        foreach ($productos as $idProducto => $configuracion) {
            $insertar->execute([
                ':idServicio' => $idServicio,
                ':idProducto' => $idProducto,
                ':cantidad' => $configuracion['cantidad'],
                ':rendimiento' => $configuracion['rendimiento'],
            ]);
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

        try {
            (new Productos())->asegurarConfiguracionCatalogo();
            if ($this->obtenerTasaDolarHoy() <= 0) {
                throw new DomainException('Registra la tasa del dólar de hoy en Configuración antes de guardar precios.');
            }
            $this->pdo->beginTransaction();
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
            if ($stmt->rowCount() === 0) {
                $existe = $this->pdo->prepare("SELECT 1 FROM servicio WHERE idServicio = :idServicio");
                $existe->execute([':idServicio' => $idServicio]);
                if (!$existe->fetchColumn()) {
                    $this->pdo->rollBack();
                    return ['resultado' => 'error', 'mensaje' => 'El servicio seleccionado no existe.'];
                }
            }

            $borrarProductos = $this->pdo->prepare(
                "DELETE FROM detalleproductoservicio WHERE idServicio = :idServicio"
            );
            $borrarProductos->execute([':idServicio' => $idServicio]);
            $this->guardarProductosServicio($idServicio);
            $this->pdo->commit();

            return [
                'resultado' => 'exito',
                'mensaje'   => 'Servicio modificado correctamente.'
            ];
        } catch (DomainException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['resultado' => 'error', 'mensaje' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudo modificar el servicio: ' . $e->getMessage());
            return [
                'resultado' => 'error',
                'mensaje'   => 'No se pudo modificar el servicio.'
            ];
        }
        
    }
}
?>