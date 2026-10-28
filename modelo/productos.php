<?php
require_once 'modelo/conexion.php';

class Productos extends Conexion
{
    private $ultimoError;

    public function listar(): array
    {
        $sql = "SELECT p.idProducto, p.nombreProducto, p.marca, p.precioProducto,
                       p.cantidadActual, p.idProveedor, p.id_tipo AS idTipo,
                       COALESCE(t.nombre_tipo, 'Sin clasificar') AS tipoProducto,
                       prov.nombreProveedor
                FROM producto p
                LEFT JOIN proveedor prov ON prov.idProveedor = p.idProveedor
                LEFT JOIN tipo_producto t ON t.id_tipo = p.id_tipo
                ORDER BY p.nombreProducto ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function catalogos(): array
    {
        $this->asegurarTiposPredeterminados();

        return [
            'proveedores' => $this->pdo->query(
                "SELECT idProveedor, nombreProveedor FROM proveedor ORDER BY nombreProveedor"
            )->fetchAll(PDO::FETCH_ASSOC),
            'tipos' => $this->pdo->query(
                "SELECT id_tipo AS idTipo, nombre_tipo AS nombreTipo
                 FROM tipo_producto ORDER BY nombre_tipo"
            )->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    private function asegurarTiposPredeterminados(): void
    {
        $this->pdo->beginTransaction();
        try {
            $categoria = $this->pdo->prepare(
                "SELECT id_categoria FROM categoria
                 WHERE nombre_categoria = 'General' ORDER BY id_categoria LIMIT 1 FOR UPDATE"
            );
            $categoria->execute();
            $idCategoria = $categoria->fetchColumn();

            if (!$idCategoria) {
                $insertarCategoria = $this->pdo->prepare(
                    "INSERT INTO categoria (nombre_categoria) VALUES ('General')"
                );
                $insertarCategoria->execute();
                $idCategoria = $this->pdo->lastInsertId();
            }

            $insertarTipo = $this->pdo->prepare(
                "INSERT INTO tipo_producto (id_categoria, nombre_tipo)
                 SELECT :idCategoria, :nombreTipo
                 WHERE NOT EXISTS (
                     SELECT 1 FROM tipo_producto WHERE id_categoria = :idCategoriaCheck
                       AND LOWER(nombre_tipo) = LOWER(:nombreTipoCheck)
                 )"
            );
            foreach (['Venta', 'Insumo'] as $nombreTipo) {
                $insertarTipo->execute([
                    ':idCategoria' => $idCategoria,
                    ':nombreTipo' => $nombreTipo,
                    ':idCategoriaCheck' => $idCategoria,
                    ':nombreTipoCheck' => $nombreTipo,
                ]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function insertar(array $datos): array
    {
        try {
            $sql = "INSERT INTO producto
                        (nombreProducto, marca, precioCosto, precioProducto, idProveedor, cantidadActual, id_tipo)
                    VALUES
                        (:nombreProducto, :marca, 0, :precioProducto, :idProveedor, :cantidadActual, :idTipo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreProducto' => $datos['nombreProducto'],
                ':marca' => $datos['marca'] !== '' ? $datos['marca'] : null,
                ':precioProducto' => $datos['precioProducto'],
                ':idProveedor' => $datos['idProveedor'],
                ':cantidadActual' => $datos['cantidadActual'],
                ':idTipo' => $datos['idTipo'],
            ]);
            return ['ok' => true, 'insertId' => (int) $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log('No se pudo registrar el producto: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo guardar el producto. Verifique el proveedor y los datos ingresados.'];
        }
    }

    public function modificar(int $idProducto, array $datos): array
    {
        try {
            $sql = "UPDATE producto
                    SET nombreProducto = :nombreProducto,
                        marca = :marca,
                        precioProducto = :precioProducto,
                        idProveedor = :idProveedor,
                        cantidadActual = :cantidadActual,
                        id_tipo = :idTipo
                    WHERE idProducto = :idProducto";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreProducto' => $datos['nombreProducto'],
                ':marca' => $datos['marca'] !== '' ? $datos['marca'] : null,
                ':precioProducto' => $datos['precioProducto'],
                ':idProveedor' => $datos['idProveedor'],
                ':cantidadActual' => $datos['cantidadActual'],
                ':idTipo' => $datos['idTipo'],
                ':idProducto' => $idProducto,
            ]);
            if ($stmt->rowCount() === 0 && !$this->existeId($idProducto)) {
                return ['ok' => false, 'mensaje' => 'El producto ya no existe.'];
            }
            return ['ok' => true];
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log('No se pudo modificar el producto: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo modificar el producto. Verifique el proveedor y los datos ingresados.'];
        }
    }

    public function eliminar(int $idProducto): array
    {
        try {
            $this->pdo->beginTransaction();
            $bloquear = $this->pdo->prepare(
                "SELECT idProducto FROM producto WHERE idProducto = :idProducto FOR UPDATE"
            );
            $bloquear->execute([':idProducto' => $idProducto]);
            if (!$bloquear->fetchColumn()) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'El producto ya no existe.'];
            }

            $enUso = $this->pdo->prepare(
                "SELECT
                    EXISTS(SELECT 1 FROM detalle_venta WHERE idProducto = :idVenta) AS enVenta,
                    EXISTS(SELECT 1 FROM detalleproductoservicio WHERE idProducto = :idServicio) AS enServicio"
            );
            $enUso->execute([
                ':idVenta' => $idProducto,
                ':idServicio' => $idProducto,
            ]);
            $referencias = $enUso->fetch(PDO::FETCH_ASSOC);
            if ($referencias['enVenta']) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'No se puede eliminar un producto incluido en ventas registradas.'];
            }
            if ($referencias['enServicio']) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'No se puede eliminar un producto asociado a un servicio.'];
            }

            $stmt = $this->pdo->prepare("DELETE FROM producto WHERE idProducto = :idProducto");
            $stmt->execute([':idProducto' => $idProducto]);
            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'El producto ya no existe.'];
            }
            $this->pdo->commit();
            return ['ok' => true];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->ultimoError = $e->getMessage();
            error_log('No se pudo eliminar el producto: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo eliminar el producto. Puede estar relacionado con otros registros.'];
        }
    }

    public function existeId(int $idProducto): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM producto WHERE idProducto = :idProducto LIMIT 1"
        );
        $stmt->execute([':idProducto' => $idProducto]);
        return (bool) $stmt->fetchColumn();
    }

    public function getUltimoError(): ?string
    {
        return $this->ultimoError ?? null;
    }
}
