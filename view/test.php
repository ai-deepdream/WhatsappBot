<?php
require_once 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;
$inputPdfFilePath = __DIR__.'/PDF/bill/46067.pdf';
$outputPdfFilePath = __DIR__.'/PDF/bill/46067-crop.pdf';


$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($inputPdfFilePath);
for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
    $templateId = $pdf->importPage($pageNumber);
    $pageDimensions = $pdf->getTemplateSize($templateId);
    $pdf->AddPage('P', array(140, 210)); // A5 عمودی
    $cropX = -18;
    $pdf->SetX($cropX);
    var_dump($pageDimensions);
    $pdf->useTemplate($templateId, $cropX);
}
$pdf->Output($outputPdfFilePath, 'F');
