<?php
require_once 'modelo/conexion.php';

class Clientes extends Conexion
{
    // Atributos privados
    private $cedula;
    private $direccion;
    private $nombres;
    private $fechadenacimiento;
    private $sexo;
    private $telefono;

    // Setters
    public function set_cedula($valor) {
        $this->cedula = $valor;
    }
    public function set_telefono($valor) {
        $this->telefono = $valor;
    }
    public function set_direccion($valor) {
        $this->direccion = $valor;
    }
    public function set_nombres($valor) {
        $this->nombres = $valor;
    }
    public function set_fechadenacimiento($valor) {
        $this->fechadenacimiento = $valor;
    }
    public function set_sexo($valor) {
        $this->sexo = $valor;
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
    public function get_sexo() {
        return $this->sexo;
    }
    public function get_telefono() {
        return $this->telefono;
    }
    public function get_direccion() {
        return $this->direccion;
    }
    public function insertar(): bool
    {
        try {
            $sql = "INSERT INTO cliente 
                    (cedula_cliente, nombre_cliente, telefono_cliente, direccion_cliente, fecha_nacimiento, genero)
                    VALUES (:cedula, :nombre, :telefono, :direccion, :fecha_nacimiento, :genero)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':cedula' => $this->cedula,
                ':nombre' => $this->nombres,
                ':telefono' => $this->telefono,
                ':direccion' => $this->direccion,
                ':fecha_nacimiento' => $this->fechadenacimiento ?: null,
                ':genero' => $this->sexo,
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
        $sql = "SELECT cedula_cliente, nombre_cliente, telefono_cliente, direccion_cliente, fecha_nacimiento, genero
                FROM cliente
                ORDER BY nombre_cliente ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCedula(string $cedula): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM cliente WHERE cedula_cliente = :cedula LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }
}
