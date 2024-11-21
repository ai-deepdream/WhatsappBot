<?php

$admit = new Admit();
$admit->set(pdfCode: $uri[2]);

if ($uri[2] && $admit->check()){
    $fileName = "قبض $admit->billID آزمایشگاه پاتوبیولوژی میلاد.pdf";
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"$fileName\";");
    header("Content-Transfer-Encoding: binary");
    readfile(__DIR__."/PDF/bill/$admit->billID.pdf");

//    redirect("view/PDF/answer/$admit->pdfCode.pdf");

}else{
    echo "یافت نشد";
}