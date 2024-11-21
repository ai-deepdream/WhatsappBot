<?php
if(str_ends_with($uri[2], '-2'))
	goto lastYear;
$admit = new Admit();
$admit->set(pdfCode: $uri[2]);

if ($uri[2] && $admit->check()){
    $fileName = "جواب $admit->billID آزمایشگاه پاتوبیولوژی میلاد.pdf";
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"$fileName\";");
    header("Content-Transfer-Encoding: binary");

    if (is_file(__DIR__."/PDF/answer/$admit->billID.pdf")) // New version
        readfile(__DIR__."/PDF/answer/$admit->billID.pdf");
    elseif (is_file(__DIR__."/PDF/answer/$admit->pdfCode.pdf")) // Last version
        readfile(__DIR__."/PDF/answer/$admit->pdfCode.pdf");
    else
        echo "یافت نشد";

//    redirect("view/PDF/answer/$admit->pdfCode.pdf");

}else{
    echo "یافت نشد";
}

lastYear:
$admit = new Admit();
$admit->set(pdfCode: $uri[2])->table('admit-1402');

if ($uri[2] && $admit->check()){
	$fileName = "جواب $admit->billID آزمایشگاه پاتوبیولوژی میلاد.pdf";
	header("Content-Type: application/pdf");
	header("Content-Disposition: attachment; filename=\"$fileName\";");
	header("Content-Transfer-Encoding: binary");
	
	if (is_file(__DIR__."/PDF/answer/1402/$admit->billID.pdf")) // New version
		readfile(__DIR__."/PDF/answer/1402/$admit->billID.pdf");
	elseif (is_file(__DIR__."/PDF/answer/$admit->pdfCode.pdf")) // Last version
		readfile(__DIR__."/PDF/answer/$admit->pdfCode.pdf");
	else
		echo "یافت نشد";

//    redirect("view/PDF/answer/$admit->pdfCode.pdf");

}else{
	echo "یافت نشد";
}