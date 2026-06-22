<?php
require_once 'modelo/conexion.php';

class Esteticistas extends Conexion
{
    private $cedula;
    private $nombres;
    private $telefono;
    private $correo;
    private $idEspecialidad;
    private $fechaNacimiento;
    private $ultimoError;

    public function set_cedula($valor) { $this->cedula = $valor; }
    public function set_telefono($valor) { $this->telefono = $valor; }
    public function set_correo($valor) { $this->correo = $valor; }
    // acepta id o nombre de especialidad
    public function set_especialidad($valor) { $this->idEspecialidad = $valor; }
    public function set_nombres($valor) { $this->nombres = $valor; }
    public function set_fechaNacimiento($valor) { $this->fechaNacimiento = $valor; }

    public function get_cedula() { return $this->cedula; }
    public function get_nombres() { return $this->nombres; }
    public function get_telefono() { return $this->telefono; }
    public function get_correo() { return $this->correo; }
    public function get_especialidad() { return $this->idEspecialidad; }
    public function get_fechaNacimiento() { return $this->fechaNacimiento; }

    public function getUltimoError() {
        return $this->ultimoError ?? null;
    }

    // Resuelve id de especialidad: si recibe número lo devuelve, si recibe texto intenta buscarlo o crearlo
    private function resolveEspecialidadId($valor)
    {
        if (!$valor) return null;
        if (is_numeric($valor) && intval($valor) > 0) {
            return intval($valor);
        }
        try {
            $stmt = $this->pdo->prepare("SELECT idEspecialidad FROM especialidad WHERE nombreEspecialidad = :nombre LIMIT 1");
            $stmt->execute([':nombre' => $valor]);
            $id = $stmt->fetchColumn();
            if ($id) return (int)$id;

            $ins = $this->pdo->prepare("INSERT INTO especialidad (nombreEspecialidad) VALUES (:nombre)");
            $ins->execute([':nombre' => $valor]);
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return null;
        }
    }

    public function insertar(): array
    {
        try {
            $this->pdo->beginTransaction();

            $idEsp = $this->resolveEspecialidadId($this->idEspecialidad);

            $sql = "INSERT INTO esteticista (cedulaEsteticista, nombreEsteticista, correoElectronico, idEspecialidad, fechaNacimiento)
                    VALUES (:cedula, :nombre, :correo, :idEspecialidad, :fechaNacimiento)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':cedula' => $this->cedula,
                ':nombre' => $this->nombres,
                ':correo' => $this->correo,
                ':idEspecialidad' => $idEsp,
                ':fechaNacimiento' => $this->fechaNacimiento,
            ]);

            if (!$ok) {
                $this->pdo->rollBack();
                $this->ultimoError = implode(' | ', $stmt->errorInfo());
                return ['ok' => false, 'error' => $this->ultimoError];
            }

            // insertar teléfono si se proporcionó
            if ($this->telefono && trim($this->telefono) !== '') {
                $sql2 = "INSERT INTO telefonoesteticista (cedulaEsteticista, numTelefonoEsteticista) VALUES (:cedula, :telefono)";
                $stmt2 = $this->pdo->prepare($sql2);
                $ok2 = $stmt2->execute([':cedula' => $this->cedula, ':telefono' => $this->telefono]);
                if (!$ok2) {
                    $this->pdo->rollBack();
                    $this->ultimoError = implode(' | ', $stmt2->errorInfo());
                    return ['ok' => false, 'error' => $this->ultimoError];
                }
            }

            $this->pdo->commit();
            return ['ok' => true, 'data' => null, 'message' => 'Esteticista insertado'];
        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function listar(): array
    {
        try {
                     $sql = "SELECT e.cedulaEsteticista AS cedula_esteticista,
                              e.nombreEsteticista AS nombre_esteticista,
                              (SELECT t.numTelefonoEsteticista FROM telefonoesteticista t WHERE t.cedulaEsteticista = e.cedulaEsteticista LIMIT 1) AS telefono_esteticista,
                              e.correoElectronico AS correo,
                              e.fechaNacimiento AS fecha_nacimiento,
                              IFNULL(esp.idEspecialidad, 0) AS id_especialidad,
                              IFNULL(esp.nombreEspecialidad, '') AS especialidad
                          FROM esteticista e
                          LEFT JOIN especialidad esp ON esp.idEspecialidad = e.idEspecialidad
                          ORDER BY e.nombreEsteticista ASC";
            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['ok' => true, 'data' => $data];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function existeCedula(string $cedula): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 1 FROM esteticista WHERE cedulaEsteticista = :cedula LIMIT 1");
            $stmt->execute([':cedula' => $cedula]);
            $exists = (bool) $stmt->fetchColumn();
            return ['ok' => true, 'exists' => $exists];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function listarEspecialidades(): array
    {
        try {
            $sql = "SELECT idEspecialidad, nombreEspecialidad FROM especialidad ORDER BY nombreEspecialidad ASC";
            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['ok' => true, 'data' => $data];
        } catch (\PDOException $e) {
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function eliminar($cedula): array
    {
        try {
            $this->pdo->beginTransaction();

            $stmt1 = $this->pdo->prepare("DELETE FROM telefonoesteticista WHERE cedulaEsteticista = :cedula");
            $stmt1->execute([':cedula' => $cedula]);

            $stmt2 = $this->pdo->prepare("DELETE FROM esteticista WHERE cedulaEsteticista = :cedula");
            $stmt2->execute([':cedula' => $cedula]);

            $this->pdo->commit();
            return ['ok' => true, 'message' => 'Esteticista eliminado'];
        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }

    public function modificar(): array
    {
        try {
            $this->pdo->beginTransaction();

            $idEsp = $this->resolveEspecialidadId($this->idEspecialidad);

            $sql = "UPDATE esteticista SET 
                    nombreEsteticista = :nombre,
                    correoElectronico = :correo,
                    idEspecialidad = :idEspecialidad,
                    fechaNacimiento = :fechaNacimiento
                WHERE cedulaEsteticista = :cedula";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombre' => $this->nombres,
                ':correo' => $this->correo,
                ':idEspecialidad' => $idEsp,
                ':fechaNacimiento' => $this->fechaNacimiento,
                ':cedula' => $this->cedula,
            ]);

            if (!$ok) {
                $this->pdo->rollBack();
                $this->ultimoError = implode(' | ', $stmt->errorInfo());
                return ['ok' => false, 'error' => $this->ultimoError];
            }

            if ($this->telefono !== null) {
                $stmtPhone = $this->pdo->prepare("SELECT idTelefonoEsteticista FROM telefonoesteticista WHERE cedulaEsteticista = :cedula LIMIT 1");
                $stmtPhone->execute([':cedula' => $this->cedula]);
                $phoneId = $stmtPhone->fetchColumn();
                if ($phoneId) {
                    $stmtUpd = $this->pdo->prepare("UPDATE telefonoesteticista SET numTelefonoEsteticista = :telefono WHERE idTelefonoEsteticista = :id");
                    $stmtUpd->execute([':telefono' => $this->telefono, ':id' => $phoneId]);
                } else {
                    $stmtIns = $this->pdo->prepare("INSERT INTO telefonoesteticista (cedulaEsteticista, numTelefonoEsteticista) VALUES (:cedula, :telefono)");
                    $stmtIns->execute([':cedula' => $this->cedula, ':telefono' => $this->telefono]);
                }
            }

            $this->pdo->commit();
            return ['ok' => true, 'message' => 'Esteticista modificado'];
        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            $this->ultimoError = $e->getMessage();
            return ['ok' => false, 'error' => $this->ultimoError];
        }
    }
}