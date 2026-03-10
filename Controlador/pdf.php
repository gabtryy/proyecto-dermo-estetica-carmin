<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');


function to_iso($text) {
    return mb_convert_encoding((string)$text, 'ISO-8859-1', 'UTF-8');
}

require_once __DIR__ . '/../modelo/gasto.php';
require_once __DIR__ . '/../modelo/presupuesto.php';
require_once __DIR__ . '/../modelo/categoria.php';

$g = new Gasto();
$p = new Presupuesto();
$c = new Categoria();

switch ($metodo) {
    case 'gastos':
        $cedula_usuario = $_SESSION['cedula'] ?? null;
        $gastos = [];
        $presupuestos = [];

        if ($cedula_usuario) {
            try {
                $gastos = $g->listarTodosLosGastos($cedula_usuario);
                $presupuestos = $p->listarPorUsuario($cedula_usuario);
            } catch (Throwable $e) {
                $error = "Error al obtener datos: " . $e->getMessage();
            }
        }

        
        require_once __DIR__ . '/../vendor/autoload.php';


        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, to_iso('Reporte de Gastos'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        if (isset($_SESSION['username'])) {
            $pdf->Cell(0, 6, to_iso('Usuario: ' . $_SESSION['username']), 0, 1, 'C');
        }
        $pdf->Ln(4);

     
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, to_iso('Presupuestos Disponibles'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        if (!empty($presupuestos)) {
          
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 7, to_iso('Nombre'), 1, 0, 'C');
            $pdf->Cell(40, 7, to_iso('Monto'), 1, 0, 'C');
            $pdf->Cell(40, 7, to_iso('Fecha Inicio'), 1, 0, 'C');
            $pdf->Cell(40, 7, to_iso('Fecha Final'), 1, 1, 'C');

            $pdf->SetFont('Arial', '', 9);
            foreach ($presupuestos as $pres) {
                $pdf->Cell(70, 6, to_iso($pres['nombre'] ?? ''), 1, 0);
                $pdf->Cell(40, 6, '$' . number_format($pres['monto_presupuesto'] ?? 0, 2), 1, 0, 'R');
                $pdf->Cell(40, 6, to_iso($pres['fecha_inicio'] ?? ''), 1, 0, 'C');
                $pdf->Cell(40, 6, to_iso($pres['fecha_final'] ?? ''), 1, 1, 'C');
            }
        } else {
            $pdf->Cell(0, 6, to_iso('No hay presupuestos disponibles.'), 0, 1);
        }

        $pdf->Ln(6);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, 'Gastos Recientes', 0, 1);
        $pdf->SetFont('Arial', 'B', 9);
       
        $pdf->Cell(30, 7, 'Fecha', 1, 0, 'C');
        $pdf->Cell(70, 7, utf8_decode('Descripción'), 1, 0, 'C');
        $pdf->Cell(35, 7, utf8_decode('Categoría'), 1, 0, 'C');
        $pdf->Cell(25, 7, 'Monto', 1, 0, 'C');
        $pdf->Cell(30, 7, utf8_decode('Presupuesto'), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        if (!empty($gastos)) {
            foreach ($gastos as $gasto) {
                $fecha = $gasto['fecha_gastos'] ?? '';
                $desc = $gasto['descripcion'] ?? '';
                $cat = $gasto['nombre_categoria'] ?? '';
                $monto = isset($gasto['monto_gastos']) ? number_format($gasto['monto_gastos'], 2) : '0.00';
                $presName = $gasto['nombre_presupuesto'] ?? '';

                
                $pdf->Cell(30, 6, to_iso($fecha), 1, 0);
                $pdf->Cell(70, 6, to_iso((strlen($desc) > 60) ? substr($desc, 0, 57) . '...' : $desc), 1, 0);
                $pdf->Cell(35, 6, to_iso($cat), 1, 0);
                $pdf->Cell(25, 6, '$' . $monto, 1, 0, 'R');
                $pdf->Cell(30, 6, to_iso($presName), 1, 1);
            }
        } else {
            $pdf->Cell(0, 6, utf8_decode('No hay gastos registrados.'), 1, 1);
        }

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 6, to_iso('Reporte generado automáticamente por el Sistema de Gestión de Gastos - ' . date('Y')), 0, 1, 'C');

        
        $filename = 'gastos_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'D'); 
        break;

    default:
        echo "Acción no válida.";
        break;
}
?>
