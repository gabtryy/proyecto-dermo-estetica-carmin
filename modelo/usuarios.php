<?php
require_once 'modelo/conexion.php';

class Usuarios extends Conexion
{
    private $cedula = '';
    private $rol = '';
    private $usuario = '';
    private $clave = '';

    public function set_cedula(string $valor): void
    {
        $this->cedula = $valor;
    }

    public function get_cedula(): string
    {
        return $this->cedula;
    }

    public function set_rol(string $valor): void
    {
        $this->rol = $valor;
    }

    public function get_rol(): string
    {
        return $this->rol;
    }

    public function set_usuario(string $valor): void
    {
        $this->usuario = $valor;
    }

    public function get_usuario(): string
    {
        return $this->usuario;
    }

    public function set_clave(string $valor): void
    {
        $this->clave = $valor;
    }

    public function get_clave(): string
    {
        return $this->clave;
    }

    public function autenticar($nombreUsuario, $clave)
    {
        if (!is_string($nombreUsuario) || $nombreUsuario === '' || !is_string($clave) || $clave === '') {
            return false;
        }

        $sql = "SELECT cedula, usuario, rol, clave FROM usuario WHERE usuario = :usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario' => $nombreUsuario]);
        $usuario = $stmt->fetch();

        if (!$usuario || $usuario['clave'] !== $clave) {
            return false;
        }

        return [
            'cedula' => $usuario['cedula'],
            'usuario' => $usuario['usuario'],
            'rol' => $usuario['rol'],
        ];
    }

    public function insertar(): string
    {
        if ($this->existeCedula($this->get_cedula())) {
            return 'cedula_duplicada';
        }
        if ($this->existeUsuario($this->get_usuario())) {
            return 'usuario_duplicado';
        }

        $sql = "INSERT INTO usuario (cedula, rol, usuario, clave)
                VALUES (:cedula, :rol, :usuario, :clave)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':cedula' => $this->get_cedula(),
            ':rol' => $this->get_rol(),
            ':usuario' => $this->get_usuario(),
            ':clave' => $this->get_clave(),
        ]);

        return 'insertado';
    }

    public function listar(): array
    {
        $sql = "SELECT u.cedula, u.usuario, r.tipo_usuario AS rol
                FROM usuario u
                LEFT JOIN rol r ON r.id_rol = u.rol
                ORDER BY u.usuario ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarRoles(): array
    {
        $stmt = $this->pdo->query("SELECT id_rol, tipo_usuario FROM rol ORDER BY tipo_usuario ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCedula(string $cedula): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM usuario WHERE cedula = :cedula LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeUsuario(string $usuario): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM usuario WHERE usuario = :usuario LIMIT 1");
        $stmt->execute([':usuario' => $usuario]);
        return (bool) $stmt->fetchColumn();
    }

    public function existeRol(string $rol): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM rol WHERE id_rol = :rol LIMIT 1");
        $stmt->execute([':rol' => $rol]);
        return (bool) $stmt->fetchColumn();
    }
}
