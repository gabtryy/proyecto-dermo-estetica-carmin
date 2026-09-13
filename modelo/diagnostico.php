<?php
require_once('modelo/conexion.php');

class Diagnostico extends Conexion
{
    private $cedulaCliente;
    private $idPiel;
    private $frente;
    private $nariz;
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

    public function set_nariz($valor) {
        $this->nariz = $valor;
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

    public function listar(): array
    {
        $sql = "SELECT d.idDiagnostico,
                       c.nombreCliente,
                       p.nom_Piel,
                       d.fecha_diagnostico,
                       d.frente,
                       d.nariz,
                       d.mejilla_izq,
                       d.mejilla_der,
                       d.menton
                FROM diagnostico d
                LEFT JOIN cliente c ON c.cedulaCliente = d.cedulaCliente
                LEFT JOIN piel p ON p.idPiel = d.idPiel
                ORDER BY d.fecha_diagnostico DESC, d.idDiagnostico DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar(): array
    {
        try {
            $campos = [
                'frente' => $this->frente,
                'nariz' => $this->nariz,
                'mejilla_izq' => $this->mejillaIzquierda,
                'mejilla_der' => $this->mejillaDerecha,
                'menton' => $this->menton,
            ];

            foreach ($campos as $campo => $valor) {
                $campos[$campo] = ($valor !== null && trim($valor) !== '') ? trim($valor) : 'sin detalle';
            }

            $sql = "INSERT INTO diagnostico
                    (cedulaCliente, idPiel, fecha_diagnostico, frente, nariz, mejilla_izq, mejilla_der, menton)
                    VALUES (:cedulaCliente, :idPiel, :fecha, :frente, :nariz, :mejilla_izq, :mejilla_der, :menton)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cedulaCliente' => $this->cedulaCliente,
                ':idPiel' => $this->idPiel,
                ':fecha' => date('Y-m-d'),
                ':frente' => $campos['frente'],
                ':nariz' => $campos['nariz'],
                ':mejilla_izq' => $campos['mejilla_izq'],
                ':mejilla_der' => $campos['mejilla_der'],
                ':menton' => $campos['menton'],
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
