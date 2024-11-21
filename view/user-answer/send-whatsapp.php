<?php
goto end;
echo "Start for sending whatsapp...".EOL;
global $apiLink;
$admit = new Admit();
$admits = Admit::get(log: 'Downloaded')->do;
foreach ($admits as $i){
    $admit->load($i);
    if (is_numeric($admit->mobile))
        $admit->mobile = +$admit->mobile;
    else{
        $admit->log('invalid mobile number');
        continue;
    }
    if ($admit->mobile<9000000000 || $admit->mobile>10000000000){
        $admit->log('invalid mobile number');
        continue;
    }
    $whatsapp = new Whatsapp($apiLink, botSession);
    $message = $admit->message;

    if ($admit->stateID == 20)  // وضعیت 20 جواب آماده شده است
        $message = substr($message, 0, strpos($message, 'لينک دانلود جواب')) . substr($message, strpos($message, 'بعلت بحران کرون'));
    elseif ($admit->stateID == 2)  // وضعیت 20 جواب آماده شده است
        $message = substr($message, 0, strpos($message, 'لينک دريافت قبض'));

    if (!$admit->sendCount){
        $whatsapp->send('98'.$admit->mobile, 'text', "سلام، وقت بخیر", '', '', '', false);
        sleep(8);
    }
    if (($rs=$whatsapp->send('98'.$admit->mobile, 'text', $message))=="success")
        $admit->log('send message');
    else{
        $admit->log('Whatsapp send message failed: '.$rs);
        continue;
    }

    $sendFile=null;
    switch ($admit->stateID){
        case 20:
            $sendFile = $whatsapp->send('98'.$admit->mobile, 'document', '', 'http://'.siteAddress.'/view/PDF/answer/'.$admit->billID.'.pdf', 'application/pdf', "جواب".$admit->billID.'.pdf');
            break;
        case 2:
            $sendFile = $whatsapp->send('98'.$admit->mobile, 'document', '', 'http://'.siteAddress.'/view/PDF/bill/'.$admit->billID.'.pdf', 'application/pdf', "قبض".$admit->billID.'.pdf');
            break;
        default: break;
    }
    if ($sendFile=="success"){
        $admit->log = 'send file';
        $admit->sendTs = time();
        $admit->read = '☑';
        $admit->save();
    } else{
        $admit->log('send file failed');
    }
    $admit->sendCount($admit->sendCount+1);
}

// ارسال پیام تکمیل وجه
$admits = $admit::get(["debt>"=> 0, 'stateID'=>20, "log"=> ''])->do;
foreach ($admits as $i){
    $admit->load($i);
    if (is_numeric($admit->mobile))
        $admit->mobile = +$admit->mobile;
    else{
        $admit->log('invalid mobile number');
        continue;
    }
    if ($admit->mobile<9000000000 || $admit->mobile>10000000000){
        $admit->log('invalid mobile number');
        continue;
    }
    $whatsapp = new Whatsapp($apiLink, botSession);
    $message = $admit->message;
    $message = substr($message, 0, strpos($message, 'لينک دانلود جواب')) . substr($message, strpos($message, 'بعلت بحران کرون'));
    $message .= "\n *مانده حساب شما بیشتر از مبلغ مجاز می‌باشد. لطفا جهت دریافت جواب آزمایش خود، ابتدا بدهی را تسویه نمایید.* \n بدین منظور با داخلی 17 یا 38 تماس بگیرید.";
    if (!$admit->sendCount){
        $whatsapp->send('98'.$admit->mobile, 'text', "سلام، وقت بخیر", '', '', '', false);
        sleep(8);
    }
    if (($rs=$whatsapp->send('98'.$admit->mobile, 'text', $message))=="success"){
        $admit->log = 'send message';
        $admit->sendTs = time();
        $admit->read = '☑';
        $admit->save();
    }

else{
        $admit->log('Whatsapp send message failed: '.$rs);
    }
    $admit->sendCount($admit->sendCount+1);
}

// ارسال پیام نمونه برداری
$admits = $admit::get(['stateID!='=>20, 'stateID^!='=>2, "log"=> ''])->do;
foreach ($admits as $i){
    $admit->load($i);
    if (is_numeric($admit->mobile))
        $admit->mobile = +$admit->mobile;
    else{
        $admit->log('invalid mobile number');
        continue;
    }
    if ($admit->mobile<9000000000 || $admit->mobile>10000000000){
        $admit->log('invalid mobile number');
        continue;
    }
    $whatsapp = new Whatsapp($apiLink, botSession);
    $message = $admit->message;
    if (!$admit->sendCount){
        $whatsapp->send('98'.$admit->mobile, 'text', "سلام، وقت بخیر", '', '', '', false);
        sleep(8);
    }
    if (($rs=$whatsapp->send('98'.$admit->mobile, 'text', $message))=="success"){
        $admit->log = 'send message';
        $admit->sendTs = time();
        $admit->read = '☑';
        $admit->save();
    }
    else{
        $admit->log('Whatsapp send message failed: '.$rs);
    }
    $admit->sendCount($admit->sendCount+1);
}

// ارسال پیام احراز هویت
$admits = $admit::get(['stateID=='=>20, 'rn'=>0, "log"=> ''])->do;
foreach ($admits as $i){
    $admit->load($i);
    if (is_numeric($admit->mobile))
        $admit->mobile = +$admit->mobile;
    else{
        $admit->log('invalid mobile number');
        continue;
    }
    if ($admit->mobile<9000000000 || $admit->mobile>10000000000){
        $admit->log('invalid mobile number');
        continue;
    }
    $whatsapp = new Whatsapp($apiLink, botSession);
    $message = $admit->message;
    if (!$admit->sendCount){
        $whatsapp->send('98'.$admit->mobile, 'text', "سلام، وقت بخیر", '', '', '', false);
        sleep(8);
    }
    if (($rs=$whatsapp->send('98'.$admit->mobile, 'text', $message))=="success"){
        $admit->log = 'send message';
        $admit->sendTs = time();
        $admit->read = '☑';
        $admit->save();
    }
    else{
        $admit->log('Whatsapp send message failed: '.$rs);
    }
    $admit->sendCount($admit->sendCount+1);
}
/*
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=&App=1
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx%2F9lfWpQ%2B%2F%2B6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj%2FkunMOd0CCLDP%2BCeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k%3D&App=1

https://my.miladlab.ir/AccountUMM/Remote?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
*/

end: