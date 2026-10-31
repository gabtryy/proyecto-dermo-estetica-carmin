<?php

require_once('modelo/conexion.php');

class Antecedente extends Conexion {

    private $cedulaCliente;
    private $tipos = [];
    private $descripciones = [];

    public function set_cedulaCliente($valor) { $this->cedulaCliente = $valor; }
    public function set_tipos($valor) { $this->tipos = $valor; }
    public function set_descripciones($valor) { $this->descripciones = $valor; }

    public function get_cedulaCliente() { return $this->cedulaCliente; }
    public function get_tipos() { return $this->tipos; }
    public function get_descripciones() { return $this->descripciones; }

    public function listar(): array
    {
        try {
            $sql = "SELECT
            c.cedulaCliente,
            c.nombreCliente,
            GROUP_CONCAT(t.nom_tipo_antecedente SEPARATOR ', ') AS enfermedades
        FROM cliente c
        INNER JOIN antecedentes a ON c.cedulaCliente = a.cedulaCliente
        INNER JOIN tipo_antecedente t ON a.id_tipo_antecedente = t.id_tipo_antecedente
        GROUP BY c.cedulaCliente, c.nombreCliente
        ORDER BY c.nombreCliente ASC";
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

    public function listarPorCliente(): array
    {
        try {
            $sql = "SELECT a.id_antecedente, a.cedulaCliente, a.id_tipo_antecedente,
                           t.nom_tipo_antecedente, a.descripcion_antecedente
                    FROM antecedentes a
                    INNER JOIN tipo_antecedente t ON a.id_tipo_antecedente = t.id_tipo_antecedente
                    WHERE a.cedulaCliente = :cedulaCliente
                    ORDER BY t.nom_tipo_antecedente ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':cedulaCliente' => $this->cedulaCliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function eliminarPorCliente(): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM antecedentes WHERE cedulaCliente = :cedulaCliente");
            $stmt->execute([':cedulaCliente' => $this->cedulaCliente]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Antecedentes del cliente eliminados correctamente.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function guardarRegistros(): array
    {
        try {
            $consulta = $this->pdo->prepare("SELECT 1 FROM antecedentes WHERE cedulaCliente = :cedulaCliente LIMIT 1");
            $consulta->execute([':cedulaCliente' => $this->cedulaCliente]);
            if ($consulta->fetchColumn()) {
                return [
                    'resultado' => 'error',
                    'mensaje' => 'Este cliente ya tiene antecedentes registrados. Use Modificar para actualizarlos.'
                ];
            }

            $this->pdo->beginTransaction();
            $insert = $this->pdo->prepare(
                "INSERT INTO antecedentes (cedulaCliente, id_tipo_antecedente, descripcion_antecedente)
                 VALUES (:cedulaCliente, :id_tipo_antecedente, :descripcion_antecedente)"
            );

            foreach ($this->tipos as $i => $tipo) {
                $insert->execute([
                    ':cedulaCliente' => $this->cedulaCliente,
                    ':id_tipo_antecedente' => trim((string) $tipo),
                    ':descripcion_antecedente' => trim((string) ($this->descripciones[$i] ?? '')),
                ]);
            }

            $this->pdo->commit();
            return ['resultado' => 'exito', 'mensaje' => 'Antecedentes del cliente guardados correctamente.'];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['resultado' => 'error', 'mensaje' => $e->getMessage()];
        }
    }

    public function modificarRegistros(): array
    {
        try {
            $this->pdo->beginTransaction();
            $delete = $this->pdo->prepare("DELETE FROM antecedentes WHERE cedulaCliente = :cedulaCliente");
            $delete->execute([':cedulaCliente' => $this->cedulaCliente]);

            $insert = $this->pdo->prepare(
                "INSERT INTO antecedentes (cedulaCliente, id_tipo_antecedente, descripcion_antecedente)
                 VALUES (:cedulaCliente, :id_tipo_antecedente, :descripcion_antecedente)"
            );
            foreach ($this->tipos as $i => $tipo) {
                $insert->execute([
                    ':cedulaCliente' => $this->cedulaCliente,
                    ':id_tipo_antecedente' => trim((string) $tipo),
                    ':descripcion_antecedente' => trim((string) ($this->descripciones[$i] ?? '')),
                ]);
            }

            $this->pdo->commit();
            return ['resultado' => 'exito', 'mensaje' => 'Antecedentes del cliente modificados correctamente.'];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['resultado' => 'error', 'mensaje' => $e->getMessage()];
        }
    }
}
?>
