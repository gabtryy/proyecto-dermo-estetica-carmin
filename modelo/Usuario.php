<?php
require_once __DIR__ . '/conexion.php';

class Usuario extends Conexion {

    public function listarRol()
    {
        $sql = "SELECT * FROM rol";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar un usuario.
     * Adaptado a la estructura actual de la tabla `usuario` (cedula, id_rol, clave).
     * Recibe: $usuario (se usará como `cedula`), $clave (texto plano) y $correo opcional.
     */
    public function registrar(...$args) {
        // Soporta dos formas:
        // - Admin: registrar($cedula, $rol, $nombre, $telefono, $correo, $clave)
        // - Público: registrar($usuarioCedula, $clave, $correo = null)
        try {
            if (count($args) === 6) {
                [$cedula, $rol, $nombre, $telefono, $correo, $clave] = $args;
            } else {
                $cedula = $args[0] ?? null;
                $clave = $args[1] ?? null;
                $correo = $args[2] ?? null;
                $rol = 1; // rol por defecto
            }

            if (empty($cedula) || empty($clave)) {
                return false;
            }

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario (cedula, id_rol, clave) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cedula, $rol, $claveHash]);
        } catch (PDOException $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }

    /** Buscar por credenciales: ahora busca por `cedula`. */
    public function buscarPorCredenciales($usuario) {
        try {
            $sql = "SELECT * FROM usuario WHERE cedula = :usuario LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['usuario' => $usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar usuario: " . $e->getMessage());
            return null;
        }
    }

    // Obtener todos los usuarios
    public function listar() {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar usuario por cedula
    public function buscarPorId($cedula) {
        $sql = "SELECT * FROM usuario WHERE cedula = :cedula";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar usuario (clave opcional)
    public function actualizar($cedula, $usuario = null, $clave = null) {
        try {
            if ($clave) {
                $sql = "UPDATE usuario SET clave = :clave WHERE cedula = :cedula";
                $stmt = $this->pdo->prepare($sql);
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);
                $stmt->bindParam(':clave', $claveHash);
            } else {
                // Si sólo se quiere actualizar otros campos y existen, implementar aquí
                return false;
            }

            $stmt->bindParam(':cedula', $cedula);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar usuario por cedula
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

