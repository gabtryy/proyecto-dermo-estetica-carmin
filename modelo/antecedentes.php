<?php

require_once('modelo/conexion.php');

class Antecedente extends Conexion {

    private $id_antecedente;
    private $cedulaCliente;
    private $id_tipo_antecedente;
    private $descripcion_antecedente;

    function set_id_antecedente($valor) {
        $this->id_antecedente = $valor;
    }

    function set_cedulaCliente($valor) {
        $this->cedulaCliente = $valor;
    }

    function set_id_tipo_antecedente($valor) {
        $this->id_tipo_antecedente = $valor;
    }

    function set_descripcion_antecedente($valor) {
        $this->descripcion_antecedente = $valor;
    }

    public function get_id_antecedente() {
        return $this->id_antecedente;
    }

    public function get_cedulaCliente() {
        return $this->cedulaCliente;
    }

    public function get_id_tipo_antecedente() {
        return $this->id_tipo_antecedente;
    }

    public function get_descripcion_antecedente() {
        return $this->descripcion_antecedente;
    }

    public function insertar(): array 
    {
        try {
            $sql = "INSERT INTO antecedentes (cedulaCliente, id_tipo_antecedente, descripcion_antecedente)
                    VALUES (:cedulaCliente, :id_tipo_antecedente, :descripcion_antecedente)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':id_tipo_antecedente' => $this->id_tipo_antecedente,
                ':descripcion_antecedente' => $this->descripcion_antecedente,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Antecedente registrado con éxito.'
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
            $sql = "SELECT a.id_antecedente, a.cedulaCliente, c.nombreCliente, a.id_tipo_antecedente,
                           t.nom_tipo_antecedente, a.descripcion_antecedente
                    FROM antecedentes a
                    INNER JOIN cliente c ON a.cedulaCliente = c.cedulaCliente
                    INNER JOIN tipo_antecedente t ON a.id_tipo_antecedente = t.id_tipo_antecedente
                    ORDER BY c.nombreCliente ASC, t.nom_tipo_antecedente ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function listarClientes(): array
    {
        try {
            $sql = "SELECT cedulaCliente, nombreCliente FROM cliente ORDER BY nombreCliente ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function listarTiposAntecedentes(): array
    {
        try {
            $sql = "SELECT id_tipo_antecedente, nom_tipo_antecedente
                    FROM tipo_antecedente
                    ORDER BY nom_tipo_antecedente ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function listarPorCliente($cedulaCliente): array
    {
        try {
            $sql = "SELECT a.id_antecedente, a.cedulaCliente, a.id_tipo_antecedente,
                           t.nom_tipo_antecedente, a.descripcion_antecedente
                    FROM antecedentes a
                    INNER JOIN tipo_antecedente t ON a.id_tipo_antecedente = t.id_tipo_antecedente
                    WHERE a.cedulaCliente = :cedulaCliente
                    ORDER BY t.nom_tipo_antecedente ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':cedulaCliente' => $cedulaCliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function eliminar(): array
    {
        try {
            $sql = "DELETE FROM antecedentes WHERE id_antecedente = :id_antecedente";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_antecedente' => $this->id_antecedente]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Antecedente eliminado correctamente.'
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
            $sql = "UPDATE antecedentes SET 
                        cedulaCliente = :cedulaCliente,
                        id_tipo_antecedente = :id_tipo_antecedente,
                        descripcion_antecedente = :descripcion_antecedente
                    WHERE id_antecedente = :id_antecedente";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':id_tipo_antecedente' => $this->id_tipo_antecedente,
                ':descripcion_antecedente' => $this->descripcion_antecedente,
                ':id_antecedente' => $this->id_antecedente,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Antecedente modificado correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function guardarCliente($cedulaCliente, array $tipos = [], array $descripciones = []): array
    {
        try {
            $this->pdo->beginTransaction();
            $delete = $this->pdo->prepare("DELETE FROM antecedentes WHERE cedulaCliente = :cedulaCliente");
            $delete->execute([':cedulaCliente' => $cedulaCliente]);

            for ($i = 0; $i < count($tipos); $i++) {
                $tipo = trim((string) $tipos[$i]);
                $descripcion = trim((string) ($descripciones[$i] ?? ''));

                if ($tipo === '') {
                    continue;
                }

                $insert = $this->pdo->prepare("INSERT INTO antecedentes (cedulaCliente, id_tipo_antecedente, descripcion_antecedente)
                    VALUES (:cedulaCliente, :id_tipo_antecedente, :descripcion_antecedente)");

                $insert->execute([
                    ':cedulaCliente' => $cedulaCliente,
                    ':id_tipo_antecedente' => $tipo,
                    ':descripcion_antecedente' => $descripcion,
                ]);
            }

            $this->pdo->commit();
            return [
                'resultado' => 'exito',
                'mensaje' => 'Antecedentes del cliente guardados correctamente.'
            ];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
?>
