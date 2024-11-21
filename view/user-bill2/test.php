<?php
require "vendor/autoload.php";
use CzProject\PdfRotate\PdfRotate;

$pdf = new PdfRotate;
$sourceFile = "C:\Users\LEGION\Desktop\document.pdf";
$outputFile = "C:\Users\LEGION\Desktop\document-2.pdf";

$pdf->rotatePdf($sourceFile, $outputFile, $pdf::DEGREES_270);