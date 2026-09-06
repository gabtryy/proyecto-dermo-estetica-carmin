<?php
require_once('modelo/conexion.php');

class Diagnostico extends Conexion
{
    private $cedulaCliente;
    private $idPiel;
    private $frente;
    private $mejillaIzquierda;
    private $mejillaDerecha;
    private $menton;

    public function set_cedulaCliente($valor) {
        $this->cedulaCliente = $valor;
    }

    public function set_idPiel($valor) {
        $this->idPiel = $valor;
    }

    public function set_frente($valor) {
        $this->frente = $valor;
    }

    public function set_mejillaIzquierda($valor) {
        $this->mejillaIzquierda = $valor;
    }

    public function set_mejillaDerecha($valor) {
        $this->mejillaDerecha = $valor;
    }

    public function set_menton($valor) {
        $this->menton = $valor;
    }

    public function listarPieles(): array
    {
        $sql = "SELECT idPiel, nom_Piel
                FROM piel
                ORDER BY nom_Piel ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar(): array
    {
        try {
            $descripcion = 'frente: ' . ($this->frente ?: 'sin detalles')
                . '; mejilla izquierda: ' . ($this->mejillaIzquierda ?: 'sin detalles')
                . '; mejilla derecha: ' . ($this->mejillaDerecha ?: 'sin detalles')
                . '; menton: ' . ($this->menton ?: 'sin detalles');

            $sql = "INSERT INTO diagnostico
                    (cedulaCliente, idPiel, fecha_diagnostico, descripcion_diagnostico)
                    VALUES (:cedulaCliente, :idPiel, :fecha, :descripcion)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':idPiel' => $this->idPiel,
                ':fecha' => date('Y-m-d'),
                ':descripcion' => $descripcion,
            ]);

            return [
                'resultado' => 'exito',
                'mensaje' => 'Diagnóstico registrado con éxito.'
            ];
        } catch (Exception $e) {
            return [
                'resultado' => 'error',
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
?>
