<?php

require_once('modelo/conexion.php');

class Proveedor extends Conexion {
    
    private $idProveedor;
    private $rif;
    private $nombreProveedor;
    private $estadoDirProveedor;
    private $municipioDirProveedor;
    private $parroquiaDirProveedor;
    private $telefono;
    private $correo;

    function set_idProveedor($valor) {
        $this->idProveedor = $valor;
    }

    function set_rif($valor) {
        $this->rif = $valor;
    }

    function set_nombreProveedor($valor) {
        $this->nombreProveedor = $valor;
    }

    function set_estadoDirProveedor($valor) {
        $this->estadoDirProveedor = $valor;
    }

    function set_municipioDirProveedor($valor) {
        $this->municipioDirProveedor = $valor;
    }

    function set_parroquiaDirProveedor($valor) {
        $this->parroquiaDirProveedor = $valor;
    }

    function set_telefono($valor) {
        $this->telefono = $valor;
    }

    function set_correo($valor) {
        $this->correo = $valor;
    }

    public function get_idProveedor() {
        return $this->idProveedor;
    }

    public function get_rif() {
        return $this->rif;
    }

    public function get_nombreProveedor() {
        return $this->nombreProveedor;
    }

    public function get_estadoDirProveedor() {
        return $this->estadoDirProveedor;
    }

    public function get_municipioDirProveedor() {
        return $this->municipioDirProveedor;
    }

    public function get_parroquiaDirProveedor() {
        return $this->parroquiaDirProveedor;
    }

    public function get_telefono() {
        return $this->telefono;
    }

    public function get_correo() {
        return $this->correo;
    }

    public function insertar(): array 
    {
        try {
            $sql = "INSERT INTO proveedor (rif, nombreProveedor, estadoDirProveedor, municipioDirProveedor, parroquiaDirProveedor, telefono, correo)
                    VALUES (:rif, :nombreProveedor, :estadoDirProveedor, :municipioDirProveedor, :parroquiaDirProveedor, :telefono, :correo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':rif' => $this->rif,
                ':nombreProveedor' => $this->nombreProveedor,
                ':estadoDirProveedor' => $this->estadoDirProveedor,
                ':municipioDirProveedor' => $this->municipioDirProveedor,
                ':parroquiaDirProveedor' => $this->parroquiaDirProveedor,
                ':telefono' => $this->telefono,
                ':correo' => $this->correo,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Proveedor registrado con éxito.'
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
            $sql = "SELECT idProveedor, rif, nombreProveedor, estadoDirProveedor, municipioDirProveedor, parroquiaDirProveedor, telefono, correo
                    FROM proveedor
                    ORDER BY nombreProveedor ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function eliminar(): array
    {
        try {
            $sql = "DELETE FROM proveedor WHERE idProveedor = :idProveedor";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idProveedor' => $this->idProveedor]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Proveedor eliminado correctamente.'
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
            $sql = "UPDATE proveedor SET 
                        rif = :rif,
                        nombreProveedor = :nombreProveedor,
                        estadoDirProveedor = :estadoDirProveedor,
                        municipioDirProveedor = :municipioDirProveedor,
                        parroquiaDirProveedor = :parroquiaDirProveedor,
                        telefono = :telefono,
                        correo = :correo
                    WHERE idProveedor = :idProveedor";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':rif' => $this->rif,
                ':nombreProveedor' => $this->nombreProveedor,
                ':estadoDirProveedor' => $this->estadoDirProveedor,
                ':municipioDirProveedor' => $this->municipioDirProveedor,
                ':parroquiaDirProveedor' => $this->parroquiaDirProveedor,
                ':telefono' => $this->telefono,
                ':correo' => $this->correo,
                ':idProveedor' => $this->idProveedor,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Proveedor modificado correctamente.'
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
