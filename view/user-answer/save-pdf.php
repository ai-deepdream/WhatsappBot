<?php
use CzProject\PdfRotate\PdfRotate;

echo EOL."SAVE RESULT PDF>>>>>>>>>>>>>>>>".EOL;

$admit = new Admit();
$admits = $admit::get(['log'=>'', 'stateID'=>20,'debt<='=>0])->do;
foreach ($admits as $i){
    $admit->load($i);
//    if ($admit->state != "ت آزمايشگاه")
//        continue;
    echo "Download answer pdf for ID: $admit->ID".EOL;
    /*    $resultPage = link_to_pdf($admit->link);
        echo $resultPage.EOL;
        if ($dl = download_pdf($resultPage, "answer/".$admit->billID)){
            $admit->log($dl);
            @unlink(__DIR__ . "/../PDF/answer/$admit->billID.pdf");
        }else{
            $admit->log("Downloaded");
        }*/
    try {
        $base64PDF = get_page("http://192.168.1.74:8088/api/ResultReport?UserName=$admit->billID&Pass=$admit->mobile");
        $pdf = base64_decode($base64PDF);
        if ($pdf === false)
            throw new Exception('Fail download');
        $pdfCode = $admit->make_pdf_code();
        file_put_contents(__DIR__."/../PDF/answer/$admit->billID.pdf", $pdf);
        $admit->log("Downloaded");
    }catch (Exception $e){
        $admit->log("Fail download");
    }

/*    if (base64_PDF($admit->rn, "answer/".$admit->billID)){
        $admit->log("Downloaded");
    }else{
    }*/
//    break;
}

$admits = $admit::get(['log'=>'', 'stateID'=>2])->do;
foreach ($admits as $i){
    $admit->load($i);
    echo "Download bill pdf for ID: $admit->ID".EOL;
    try {
        $base64PDF = get_page("http://192.168.1.74:8088/api/CostReceipt?UserName=$admit->billID&Pass=$admit->mobile");
        $pdf = base64_decode($base64PDF);
        if ($pdf === false)
            throw new Exception('Fail bill download');
        file_put_contents(__DIR__."/../PDF/bill/$admit->billID.pdf", $pdf);
        require_once "vendor/autoload.php";
        $pdf = new CzProject\PdfRotate\PdfRotate;
        $pdf->rotatePdf(__DIR__."/../PDF/bill/$admit->billID.pdf", __DIR__."/../PDF/bill/$admit->billID.pdf", $pdf::DEGREES_270);
        $pdfCode = $admit->make_pdf_code();
        // Crop PDF
        $pdfFilePath = __DIR__."/../PDF/bill/$admit->billID.pdf";
        $pdf = new setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($pdfFilePath);
        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $templateId = $pdf->importPage($pageNumber);
            $pageDimensions = $pdf->getTemplateSize($templateId);
            $pdf->AddPage('P', array(140, 210)); // A5 عمودی
            $cropX = -18;
            $pdf->SetX($cropX);
            $pdf->useTemplate($templateId, $cropX);
        }
        $pdf->Output($pdfFilePath, 'F');

        $admit->log("Downloaded");
    }catch (Exception $e){
        $admit->log("Fail download");
    }

/*    if (base64_PDF($admit->rn, "answer/".$admit->billID)){
        $admit->log("Downloaded");
    }else{
    }*/
//    break;
}

/*
echo EOL."SAVE BILL PDF>>>>>>>>>>>>>>>>".EOL;
$admits = $admit::get(['log'=>'', 'stateID'=>2])->do;
foreach ($admits as $i){
    $admit->load($i);
    echo "Download bull for ID: $admit->ID".EOL;
    $resultPage = "https://my.miladlab.ir/AccountUMM/PrintReceiptCost?AdmitIDCost=$admit->rn&AppCost=$admit->mode";
    echo $resultPage.EOL;
    $savedFile = __DIR__."/../PDF/bill/$admit->billID.pdf";
    if ($dl = download_pdf($resultPage, "bill/".$admit->billID)){
        $admit->log($dl);
        @unlink($savedFile);
    }else{
        $pdf = new PdfRotate;
        $pdf->rotatePdf($savedFile, $savedFile, $pdf::DEGREES_270);
        $admit->log("Downloaded");
    }
}
*/


/*
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=&App=1
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx%2F9lfWpQ%2B%2F%2B6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj%2FkunMOd0CCLDP%2BCeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k%3D&App=1

https://my.miladlab.ir/AccountUMM/Remote?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=

لینک پاتولوژی

*/