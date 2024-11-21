<?php
echo "Bill: Start for sending...".EOL;
global $apiLink;
$patient = new Patient();
$patients = Patient::get(log: 'Get data')->do;
foreach ($patients as $i){
    $patient->load($i);
    $patient->mobile = +$patient->mobile;
    if ($patient->mobile<9000000000 || $patient->mobile>10000000000){
        $patient->log('invalid mobile number');
        continue;
    }
    $whatsapp = new Whatsapp($apiLink, botSession);
    $message = ($patient->sex?"آقای":"خانم")
        ." $patient->fName $patient->lName \r\nپذیرش شما در آزمایشگاه پاتوبیولوژی میلاد انجام شد.";

    if (!$patient->sendCount){
        $whatsapp->send('98'.$patient->mobile, 'text', "سلام، وقت بخیر", '', '', '', false);
        sleep(8);
    }
    echo 'http://'.siteAddress.'/view/PDF/bill/'.$patient->rn.'.jpg' . EOL;
    if ($whatsapp->send('98'.$patient->mobile, 'image', $message, 'http://'.siteAddress.'/view/PDF/bill/e-'.$patient->rn.'.jpg')=="success")
        $patient->log('send message');
    else{
        $patient->log('send failed');
        continue;
    }
    $patient->sendCount($patient->sendCount+1);
}