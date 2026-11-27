<?php
require_once 'modelo/conexion.php';

class Productos extends Conexion
{
    private $idProducto = 0;
    private $nombreProducto = '';
    private $marca = '';
    private $idProveedor = 0;
    private $precioProducto = '';
    private $cantidadActual = '0';
    private $idTipo = 0;
    private $ultimoError;

    public function set_idProducto(int $valor): void
    {
        $this->idProducto = $valor;
    }

    public function get_idProducto(): int
    {
        return $this->idProducto;
    }

    public function set_nombreProducto(string $valor): void
    {
        $this->nombreProducto = trim($valor);
    }

    public function get_nombreProducto(): string
    {
        return $this->nombreProducto;
    }

    public function set_marca(string $valor): void
    {
        $this->marca = trim($valor);
    }

    public function get_marca(): string
    {
        return $this->marca;
    }

    public function set_idProveedor(int $valor): void
    {
        $this->idProveedor = $valor;
    }

    public function get_idProveedor(): int
    {
        return $this->idProveedor;
    }

    public function set_precioProducto(string $valor): void
    {
        $this->precioProducto = trim($valor);
    }

    public function get_precioProducto(): string
    {
        return $this->precioProducto;
    }

    public function set_cantidadActual(string $valor): void
    {
        $this->cantidadActual = trim($valor);
    }

    public function get_cantidadActual(): string
    {
        return $this->cantidadActual;
    }

    public function set_idTipo(int $valor): void
    {
        $this->idTipo = $valor;
    }

    public function get_idTipo(): int
    {
        return $this->idTipo;
    }

    public function asegurarConfiguracionCatalogo(): void
    {
        try {
            $this->pdo->exec(
                "CREATE TABLE IF NOT EXISTS sistema_migraciones (
                    clave VARCHAR(64) NOT NULL PRIMARY KEY,
                    aplicada_en DATETIME NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
            $this->asegurarInventarioFraccionario();
            $this->pdo->beginTransaction();
            $ventaStmt = $this->pdo->query(
                "SELECT id_tipo, id_categoria
                 FROM tipo_producto
                 WHERE LOWER(nombre_tipo) = 'venta'
                 ORDER BY id_tipo
                 LIMIT 1
                 FOR UPDATE"
            );
            $venta = $ventaStmt->fetch(PDO::FETCH_ASSOC);
            if (!$venta) {
                throw new RuntimeException('Falta el tipo de producto Venta.');
            }

            $ambosStmt = $this->pdo->query(
                "SELECT id_tipo
                 FROM tipo_producto
                 WHERE LOWER(nombre_tipo) = 'ambos'
                 ORDER BY id_tipo
                 LIMIT 1
                 FOR UPDATE"
            );
            if (!$ambosStmt->fetchColumn()) {
                $insertarAmbos = $this->pdo->prepare(
                    "INSERT INTO tipo_producto (id_categoria, nombre_tipo)
                     VALUES (:idCategoria, 'Ambos')"
                );
                $insertarAmbos->execute([':idCategoria' => $venta['id_categoria']]);
            }

            $asignarUsoPorDefecto = $this->pdo->prepare(
                "UPDATE producto SET id_tipo = :idTipo WHERE id_tipo IS NULL"
            );
            $asignarUsoPorDefecto->execute([':idTipo' => $venta['id_tipo']]);

            $migracionStmt = $this->pdo->prepare(
                "SELECT 1 FROM sistema_migraciones
                 WHERE clave = 'precios_catalogo_usd'
                 FOR UPDATE"
            );
            $migracionStmt->execute();
            if (!$migracionStmt->fetchColumn()) {
                $tasaDolar = $this->obtenerTasaDolarHoy();
                if ($tasaDolar <= 0) {
                    throw new DomainException(
                        'Registra la tasa del dólar de hoy en Configuración para convertir los precios existentes a USD.'
                    );
                }

                $convertirProductos = $this->pdo->prepare(
                    "UPDATE producto SET precioProducto = ROUND(precioProducto / :tasa, 2)"
                );
                $convertirProductos->execute([':tasa' => $tasaDolar]);
                $convertirServicios = $this->pdo->prepare(
                    "UPDATE servicio SET precio = ROUND(precio / :tasa, 2)"
                );
                $convertirServicios->execute([':tasa' => $tasaDolar]);

                $marcarMigracion = $this->pdo->prepare(
                    "INSERT INTO sistema_migraciones (clave, aplicada_en)
                     VALUES ('precios_catalogo_usd', NOW())"
                );
                $marcarMigracion->execute();
            }
            $this->pdo->commit();
        } catch (DomainException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('No se pudieron inicializar los usos de producto: ' . $e->getMessage());
            throw $e;
        }
    }

    private function asegurarInventarioFraccionario(): void
    {
        $cantidadStmt = $this->pdo->prepare(
            "SELECT DATA_TYPE, NUMERIC_SCALE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'producto'
               AND COLUMN_NAME = 'cantidadActual'"
        );
        $cantidadStmt->execute();
        $columnaCantidad = $cantidadStmt->fetch(PDO::FETCH_ASSOC);
        if (!$columnaCantidad) {
            throw new RuntimeException('No existe la columna de inventario de productos.');
        }
        if (
            strtolower((string) $columnaCantidad['DATA_TYPE']) !== 'decimal' ||
            (int) $columnaCantidad['NUMERIC_SCALE'] < 6
        ) {
            $this->pdo->exec(
                "ALTER TABLE producto
                 MODIFY cantidadActual DECIMAL(18,6) NOT NULL"
            );
        }

        $rendimientoStmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'detalleproductoservicio'
               AND COLUMN_NAME = 'rendimiento'"
        );
        $rendimientoStmt->execute();
        if ((int) $rendimientoStmt->fetchColumn() === 0) {
            $this->pdo->exec(
                "ALTER TABLE detalleproductoservicio
                 ADD COLUMN rendimiento INT NOT NULL DEFAULT 1 AFTER cantidad"
            );
        }
    }

    public function listar(): array
    {
        $this->asegurarConfiguracionCatalogo();
        $sql = "SELECT p.idProducto, p.nombreProducto, p.marca, p.precioProducto,
                       p.cantidadActual, p.idProveedor, p.id_tipo AS idTipo,
                       COALESCE(t.nombre_tipo, 'Sin clasificar') AS tipoProducto,
                       prov.nombreProveedor
                FROM producto p
                LEFT JOIN proveedor prov ON prov.idProveedor = p.idProveedor
                LEFT JOIN tipo_producto t ON t.id_tipo = p.id_tipo
                ORDER BY p.nombreProducto ASC";
        $productos = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $tasaDolar = $this->obtenerTasaDolarHoy();
        foreach ($productos as &$producto) {
            $producto['precioUSD'] = (float) $producto['precioProducto'];
            $producto['precioBs'] = $tasaDolar > 0
                ? round($producto['precioUSD'] * $tasaDolar, 2)
                : null;
        }
        unset($producto);
        return $productos;
    }

    public function catalogos(): array
    {
        $this->asegurarConfiguracionCatalogo();
        return [
            'tasaDolar' => $this->obtenerTasaDolarHoy(),
            'proveedores' => $this->pdo->query(
                "SELECT idProveedor, nombreProveedor FROM proveedor ORDER BY nombreProveedor"
            )->fetchAll(PDO::FETCH_ASSOC),
            'tipos' => $this->pdo->query(
                "SELECT id_tipo AS idTipo, nombre_tipo AS nombreTipo
                 FROM tipo_producto ORDER BY nombre_tipo"
            )->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    public function obtenerTasaDolarHoy(): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT tm.monto_tasa
             FROM tasa_moneda tm
             INNER JOIN moneda m ON m.id_moneda = tm.id_moneda
             WHERE LOWER(m.nombre_moneda) IN ('dólar', 'dolar')
               AND tm.fecha_tasa = CURDATE()
             ORDER BY tm.id_tasa DESC
             LIMIT 1"
        );
        $stmt->execute();
        return (float) ($stmt->fetchColumn() ?: 0);
    }

    public function insertar(): array
    {
        try {
            $this->asegurarConfiguracionCatalogo();
            if ($this->obtenerTasaDolarHoy() <= 0) {
                throw new DomainException('Registra la tasa del dólar de hoy en Configuración antes de guardar precios.');
            }
            $sql = "INSERT INTO producto
                        (nombreProducto, marca, precioCosto, precioProducto, idProveedor, cantidadActual, id_tipo)
                    VALUES
                        (:nombreProducto, :marca, 0, :precioProducto, :idProveedor, :cantidadActual, :idTipo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombreProducto' => $this->get_nombreProducto(),
                ':marca' => $this->get_marca() !== '' ? $this->get_marca() : null,
                ':precioProducto' => $this->get_precioProducto(),
                ':idProveedor' => $this->get_idProveedor(),
                ':cantidadActual' => $this->get_cantidadActual(),
                ':idTipo' => $this->get_idTipo(),
            ]);
            return ['ok' => true, 'insertId' => (int) $this->pdo->lastInsertId()];
        } catch (DomainException $e) {
            return ['ok' => false, 'mensaje' => $e->getMessage()];
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log('No se pudo registrar el producto: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo guardar el producto. Verifique el proveedor y los datos ingresados.'];
        } catch (Throwable $e) {
            error_log('No se pudo preparar el registro del producto: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'No se pudo guardar el producto.'];
        }
    }

    public function modificar(): array
    {
        try {
            $this->asegurarConfiguracionCatalogo();
            if ($this->obtenerTasaDolarHoy() <= 0) {
                throw new DomainException('Registra la tasa del dólar de hoy en Configuración antes de guardar precios.');
            }
            $this->pdo->beginTransaction();
            $productoStmt = $this->pdo->prepare(
                "SELECT idProducto
                 FROM producto
                 WHERE idProducto = :idProducto
                 FOR UPDATE"
            );
            $productoStmt->execute([':idProducto' => $this->get_idProducto()]);
            if (!$productoStmt->fetchColumn()) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'El producto ya no existe.'];
            }

            $tipoStmt = $this->pdo->prepare(
                "SELECT LOWER(nombre_tipo)
                 FROM tipo_producto
                 WHERE id_tipo = :idTipo"
            );
            $tipoStmt->execute([':idTipo' => $this->get_idTipo()]);
            $nombreTipo = $tipoStmt->fetchColumn();
            if ($nombreTipo === false) {
                $this->pdo->rollBack();
                return ['ok' => false, 'mensaje' => 'Seleccione un uso de producto válido.'];
            }

            if ($nombreTipo === 'venta') {
                $insumoEnServicio = $this->pdo->prepare(
                    "SELECT EXISTS(
                        SELECT 1 FROM detalleproductoservicio WHERE idProducto = :idProducto
                    )"
                );
                $insumoEnServicio->execute([':idProducto' => $this->get_idProducto()]);
                if ($insumoEnServicio->fetchColumn()) {
                    $this->pdo->rollBack();
                    return [
                        'ok' => false,
                        'mensaje' => 'Quite este producto de los servicios antes de cambiar su uso a Venta.',
                    ];
                }
            }

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
                ':nombreProducto' => $this->get_nombreProducto(),
                ':marca' => $this->get_marca() !== '' ? $this->get_marca() : null,
                ':precioProducto' => $this->get_precioProducto(),
                ':idProveedor' => $this->get_idProveedor(),
                ':cantidadActual' => $this->get_cantidadActual(),
                ':idTipo' => $this->get_idTipo(),
                ':idProducto' => $this->get_idProducto(),
            ]);
            $this->pdo->commit();
            return ['ok' => true];
        } catch (DomainException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['ok' => false, 'mensaje' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
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
                "SELECT EXISTS(
                    SELECT 1 FROM detalleproductoservicio WHERE idProducto = :idServicio
                ) AS enServicio"
            );
            $enUso->execute([':idServicio' => $idProducto]);
            $referencias = $enUso->fetch(PDO::FETCH_ASSOC);
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
