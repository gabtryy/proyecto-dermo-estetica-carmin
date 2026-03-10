<?php
require_once 'modelo/conexion.php';

class Usuario extends Conexion {

    public function listarRol()
    {
        $sql = "SELECT * FROM rol";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($cedula, $rol, $nombre, $telefono, $correo, $clave) {
        
            

            $sql = "INSERT INTO usuario (cedula, id_rol, username, telefono, correo, clave) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

          
            return $stmt->execute([$cedula, $rol, $nombre, $telefono, $correo, $clave]);
   
    }

    public function buscarPorCredenciales($usuario) {
        try {
            $sql = "SELECT * FROM usuario WHERE username = :username";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['username' => $usuario]);
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

    // Buscar usuario por ID
    public function buscarPorId($id) {
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // <-- IMPORTANTE
}




    // Actualizar usuario
    public function actualizar($id, $usuario, $clave = null) {
        try {
            if ($clave) {
                $sql = "UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);
                $stmt->bindParam(':clave', $claveHash);
            } else {
                $sql = "UPDATE usuarios SET usuario = :usuario WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
            }

            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Eliminar usuario
    public function eliminar($cedula) {
        
            $sql = "DELETE FROM usuario WHERE cedula = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([$cedula]);
       
          
       
    }
}
