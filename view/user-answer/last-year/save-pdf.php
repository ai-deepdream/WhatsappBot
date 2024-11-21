<?php
use CzProject\PdfRotate\PdfRotate;

echo EOL."SAVE RESULT PDF>>>>>>>>>>>>>>>>".EOL;

$admits = $admit::get(['log'=>'', 'stateID'=>20,'debt<='=>0])->table('admit-1402')->do;
foreach ($admits as $i){
	$admit = new Admit();
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
        $base64PDF = get_page("http://192.168.1.74:8088/api/Web/GetResultReportBase64WithDbIdAsync?DbID=17&admitId=$admit->rn&app=$admit->mode&checkReminder=false&checkInterview=false");
        $pdf = base64_decode($base64PDF);
        if ($pdf === false)
            throw new Exception('Fail download');
        $pdfCode = $admit->make_pdf_code();
        file_put_contents(__DIR__."/../../PDF/answer/1402/$admit->billID.pdf", $pdf);
        $admit->log=("Downloaded");
    }catch (Exception $e){
        $admit->log=("Fail download");
    }
	$admit->pdfCode.="_2";
	$admit->save('admit-1402');

/*    if (base64_PDF($admit->rn, "answer/".$admit->billID)){
        $admit->log("Downloaded");
    }else{
    }*/
//    break;
}

$admits = $admit::get(['log'=>'', 'stateID'=>2])->table('admit-1402')->do;
foreach ($admits as $i){
	$admit = new Admit();
	$admit->load($i);
    $admit->log = "expire last year";
	$admit->save('admit-1402');

/*    if (base64_PDF($admit->rn, "answer/".$admit->billID)){
        $admit->log("Downloaded");
    }else{
    }*/
//    break;
}
