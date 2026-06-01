<?php
require_once 'modelo/conexion.php';

class Esteticistas extends Conexion
{
    private $cedula;
    private $nombres;
    private $telefono;
    private $correo;
    private $especialidad;
    private $ultimoError;

    public function set_cedula($valor) { $this->cedula = $valor; }
    public function set_telefono($valor) { $this->telefono = $valor; }
    public function set_correo($valor) { $this->correo = $valor; }
    public function set_especialidad($valor) { $this->especialidad = $valor; }
    public function set_nombres($valor) { $this->nombres = $valor; }

    public function get_cedula() { return $this->cedula; }
    public function get_nombres() { return $this->nombres; }
    public function get_telefono() { return $this->telefono; }
    public function get_correo() { return $this->correo; }
    public function get_especialidad() { return $this->especialidad; }

    public function insertar(): bool
    {
        try {
            $sql = "INSERT INTO esteticista 
                    (cedula_esteticista, nombre_esteticista, telefono_esteticista, correo, especialidad)
                    VALUES (:cedula, :nombre, :telefono, :correo, :especialidad)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':cedula' => $this->cedula,
                ':nombre' => $this->nombres,
                ':telefono' => $this->telefono,
                ':correo' => $this->correo,
                ':especialidad' => $this->especialidad,
            ]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function getUltimoError() {
        return $this->ultimoError ?? null;
    }

    public function listar(): array
    {
        $sql = "SELECT cedula_esteticista, nombre_esteticista, telefono_esteticista, correo, especialidad
                FROM esteticista
                ORDER BY nombre_esteticista ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCedula(string $cedula): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM esteticista WHERE cedula_esteticista = :cedula LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }

    public function eliminar($cedula): bool
    {
        try {
            $sql = "DELETE FROM esteticista WHERE cedula_esteticista = :cedula";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':cedula' => $cedula]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function modificar(): bool
    {
        try {
            $sql = "UPDATE esteticista SET 
                    nombre_esteticista = :nombre,
                    telefono_esteticista = :telefono,
                    correo = :correo,
                    especialidad = :especialidad
                WHERE cedula_esteticista = :cedula";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre' => $this->nombres,
                ':telefono' => $this->telefono,
                ':correo' => $this->correo,
                ':especialidad' => $this->especialidad,
                ':cedula' => $this->cedula,
            ]);
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }
}
