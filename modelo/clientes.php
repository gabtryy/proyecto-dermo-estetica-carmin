<?php
require_once 'modelo/conexion.php';

class Clientes extends Conexion
{
    // Atributos privados actualizados según la nueva tabla
    private $cedula;
    private $nombres;
    private $fechadenacimiento;
    private $estado;
    private $municipio;
    private $parroquia;
     

    // Setters
    public function set_cedula($valor) {
        $this->cedula = $valor;
    }
    public function set_nombres($valor) {
        $this->nombres = $valor;
    }
    public function set_fechadenacimiento($valor) {
        $this->fechadenacimiento = $valor;
    }
    public function set_estado($valor) {
        $this->estado = $valor;
    }
    public function set_municipio($valor) {
        $this->municipio = $valor;
    }
    public function set_parroquia($valor) {
        $this->parroquia = $valor;
    }

    // Getters
    public function get_cedula() {
        return $this->cedula;
    }
    public function get_nombres() {
        return $this->nombres;
    }
    public function get_fechadenacimiento() {
        return $this->fechadenacimiento;
    }
    public function get_estado() {
        return $this->estado;
    }
    public function get_municipio() {
        return $this->municipio;
    }
    public function get_parroquia() {
        return $this->parroquia;
    }

    public function insertar(): string
    {
        try {
            $sql = "INSERT INTO cliente 
                    (cedulaCliente, nombreCliente, fechaNacimiento, estadoDirCliente, municipioDirCliente, parroquiaDirCliente)
                    VALUES (:cedula, :nombre, :fecha_nacimiento, :estado, :municipio, :parroquia)";
            $stmt = $this->pdo->prepare($sql);
            // verificar duplicado por cédula
            if ($this->existeCedula($this->cedula)) {
                return 'duplicado';
            }

            $ok = $stmt->execute([
                ':cedula' => $this->cedula,
                ':nombre' => $this->nombres,
                ':fecha_nacimiento' => $this->fechadenacimiento ?: null,
                ':estado' => $this->estado,
                ':municipio' => $this->municipio,
                ':parroquia' => $this->parroquia,
            ]);

            return $ok ? 'insertado' : 'error: insert fallido';
        } catch (\PDOException $e) {
            return 'error: '.$e->getMessage();
        }
    }


    public function listar(): array
    {
        $sql = "SELECT cedulaCliente, nombreCliente, fechaNacimiento, estadoDirCliente, municipioDirCliente, parroquiaDirCliente
                FROM cliente
                ORDER BY nombreCliente ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCedula(string $cedula): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM cliente WHERE cedulaCliente = :cedula LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }

    public function eliminar($cedula): string
    {
        try {
            $sql = "DELETE FROM cliente WHERE cedulaCliente = :cedula";
            $stmt = $this->pdo->prepare($sql);
            // verificar existencia
            if (!$this->existeCedula($cedula)) {
                return 'no existe';
            }

            $ok = $stmt->execute([':cedula' => $cedula]);
            return $ok ? 'eliminado' : 'error: delete fallido';
        } catch (\PDOException $e) {
            return 'error: '.$e->getMessage();
        }
    }

    public function modificar(): string
    {
        try {
            $sql = "UPDATE cliente SET 
                    nombreCliente = :nombre,
                    fechaNacimiento = :fecha_nacimiento,
                    estadoDirCliente = :estado,
                    municipioDirCliente = :municipio,
                    parroquiaDirCliente = :parroquia
                WHERE cedulaCliente = :cedula";
            $stmt = $this->pdo->prepare($sql);
            // verificar existencia
            if (!$this->existeCedula($this->cedula)) {
                return 'no existe';
            }

            $ok = $stmt->execute([
                ':nombre' => $this->nombres,
                ':fecha_nacimiento' => $this->fechadenacimiento ?: null,
                ':estado' => $this->estado,
                ':municipio' => $this->municipio,
                ':parroquia' => $this->parroquia,
                ':cedula' => $this->cedula,
            ]);

            return $ok ? 'modificado' : 'error: update fallido';
        } catch (\PDOException $e) {
            return 'error: '.$e->getMessage();
        }
    }
}

?>

