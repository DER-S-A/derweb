<?php
/**
 * Clase AVPRendicionesPDF
 * Descripción:
 *  Permite generar el archivo PDF con la impresión de las rendiciones.
 */

 class AVPRendicionesPDF extends FPDF {

    public $idRendicion = 0;
    public $titulo = "Rendición de avisos de pagos (Administración)";
    private $totalEfectivo = 0.00;
    private $importeRetiro = 0.00;
    private $efectivoDepositado = 0.00;
    private $gastosTransportes = 0.00;
    private $gastosGenerales = 0.00;
    private $efectivoEntregado = 0.00;
    private $observaciones = "";
     
    function Header() {
        // Encabezado del reporte

        $objModel = new Avp_rendicionesModel();
        $rsRendicion = $objModel->getRendicionByID($this->idRendicion);

        $this->totalEfectivo = $rsRendicion->getValue("total_efectivo");
        $this->importeRetiro = $rsRendicion->getValue("importe_retiro");
        $this->efectivoDepositado = $rsRendicion->getValue("efectivo_depositado");
        $this->gastosTransportes = $rsRendicion->getValue("gastos_transporte");
        $this->gastosGenerales = $rsRendicion->getValue("gastos_generales");
        $this->efectivoEntregado = $rsRendicion->getValue("efectivo_entregado");
        $this->observaciones = $rsRendicion->getValue("observaciones");

        $this->Image("../../admin/app/logo.png", 8, 5, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, mb_convert_encoding($this->titulo, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 20, "Vendedor: " . $rsRendicion->getValue("vendedor"));
        $rsRendicion->close();

        $this->Ln();
        $this->Line(10, $this->GetY() + 5, $this->GetPageWidth() - 10, $this->GetY() + 5);
        $this->Ln(15);
    }
    
    function Footer() {
        // Pie de página del reporte
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding("Página: ", 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'C');
    }
    
    function generarReport() {        
        // Tamaño del fuente del header de la tabla
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 10, "Fecha", 1, 0, "C");
        $this->Cell(60, 10, "Cliente / Suc.", 1, 0, "C");
        $this->Cell(65, 10, mb_convert_encoding("Raz. Soc. / Suc.", 'ISO-8859-1', 'UTF-8'), 1, 0, "C");
        $this->Cell(25, 10, "Nro. Recibo", 1, 0, "C");
        $this->Cell(20, 10, "Efvo.", 1, 0, "C");
        $this->Cell(20, 10, "Dep.", 1, 0, "C");
        $this->Cell(20, 10, "Cheq.", 1, 0, "C");
        $this->Cell(20, 10, "Retens", 1, 0, "C");
        $this->Cell(20, 10, "Total RC.", 1, 0, "C");
        $this->Ln();

        // Establecer fuente y tamaño
        $this->SetFont('Arial', '', 8);

        $objModel = new Avp_rendicionesModel();
        $rsMovimientos = $objModel->getMovimientosByIdRendicion($this->idRendicion);
        while (!$rsMovimientos->EOF()) {
            $this->Cell(20, 10, $rsMovimientos->getValueFechaFormateada("fecha"), 1, 0, "L");
            $this->Cell(60, 10, $rsMovimientos->getValue("cliente_cardcode") . " / " . $rsMovimientos->getValue("codigo_sucursal"), 1, 0, "L");
            $this->Cell(65, 10, $rsMovimientos->getValue("cliente") . " / " . $rsMovimientos->getValue("sucursal"), 1, 0, "L");
            $this->Cell(25, 10, $rsMovimientos->getValue("numero_recibo"), 1, 0, "C");
            $this->Cell(20, 10, $rsMovimientos->getValue("importe_efectivo"), 1, 0, "R");
            $this->Cell(20, 10, $rsMovimientos->getValue("importe_deposito"), 1, 0, "R");
            $this->Cell(20, 10, $rsMovimientos->getValue("importe_cheques"), 1, 0, "R");
            $this->Cell(20, 10, $rsMovimientos->getValue("importe_retenciones"), 1, 0, "R");
            $this->Cell(20, 10, $rsMovimientos->getValue("total_recibo"), 1, 0, "R");
            $this->Ln();

            $rsMovimientos->next();
        }

        $rsMovimientos->close();

        $this->Line(10, $this->GetY() + 5, $this->GetPageWidth() - 10, $this->GetY() + 5);
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetX(20);
        $this->Cell(30, 10, "Total Efvo: " . $this->totalEfectivo, 0, 0, "R");
        $this->SetX(80);
        $this->Cell(30, 10, "Gastos de Transporte: " . $this->gastosTransportes, 0, 0, "R");
        $this->SetX(140);
        $this->Cell(30, 10, mb_convert_encoding("Retiró: ", 'ISO-8859-1', 'UTF-8') . $this->importeRetiro, 0, 0, "R");
        $this->SetX(200);
        $this->Cell(30, 10, "Gastos Generales: " . $this->gastosGenerales, 0, 0, "R");
        $this->Ln();
        $this->SetX(20);
        $this->Cell(30, 10, mb_convert_encoding("Depósito: ", 'ISO-8859-1', 'UTF-8') . $this->efectivoDepositado, 0, 0, "R");
        $this->SetX(80);
        $this->Cell(30, 10, "Efectivo entregado: " . $this->efectivoEntregado, 0, 0, "R");
        $this->Ln();
        $this->SetX(20);
        $this->Cell(30, 10, "Observaciones:", 0, 0, "L");
        $this->Ln();
        $this->SetX(20);
        $this->SetFont('Arial', '', 10);
        $this->Cell(100, 20, mb_convert_encoding($this->observaciones, 'ISO-8859-1', 'UTF-8'), 0, 0, "L");
    }

    public function imprimir() {
        $aResponse = [];
        $path = "../../admin/ufiles/avisos-pagos/";
        $fileName = "rendicion_" . $this->idRendicion . ".pdf";
        $this->AddPage('L', 'A4');
        $this->generarReport();
        $this->Output('F', $path . $fileName, true);
        
        $aPath = explode("/", $path);

        $aResponse["archivo_pdf"] = $aPath[4];
        $aResponse["file_name"] = $fileName;
        $aResponse["path_to_update"] = $aPath[4] . "/" . $fileName;
        return $aResponse;
    }
 }
 
