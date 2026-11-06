<?php
require_once __DIR__ . '/conexion.php';

class Usuario extends Conexion {

    public function autenticar($nombreUsuario, $clave) {
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

    public function registrar($cedula, $clave) {
        try {
            if (empty($cedula) || empty($clave)) {
                return false;
            }

            $sql = "INSERT INTO usuario (cedula, clave) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cedula, $clave]);
        } catch (PDOException $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($cedula, $clave = null) {
        try {
            if (!$clave) {
                return false;
            }

            $sql = "UPDATE usuario SET clave = :clave WHERE cedula = :cedula";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':clave', $clave);
            $stmt->bindParam(':cedula', $cedula);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($cedula) {
        try {
            $sql = "DELETE FROM usuario WHERE cedula = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cedula]);
        } catch (PDOException $e) {
            error_log("Error eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
}
