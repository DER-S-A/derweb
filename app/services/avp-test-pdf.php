<?php
include_once("autoload.php");

// Crear una instancia de la clase PDFReport

$pdf->idRendicion = 1;
$pdf = new AVPRendicionesPDF();
$pdf->imprimir();
