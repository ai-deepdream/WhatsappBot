<?php
echo "Start for sending SMS...".EOL;
$admit = new Admit();
//$admits = Admit::get(log: 'Whatsapp send message failed: The receiver number is not exists.')->do;
$admits = Admit::get(smsLog: '')->table('admit-1402')->do;

foreach ($admits as $i){
    $admit->load($i);
    if (!is_numeric($admit->mobile)){
        $admit->smsLog=('SMS invalid mobile number');
	    $admit->save('admit-1402');
        continue;
    }
    $admit->mobile = +$admit->mobile;
    if ($admit->mobile<9000000000 || $admit->mobile>10000000000){
        $admit->smsLog=('SMS invalid mobile number');
	    $admit->save('admit-1402');
	    continue;
    }
    $message = $admit->message;
    $sms = new SMS();

    if ($admit->stateID == 20){  // وضعیت 20 جواب آماده شده است
//        continue; // فعلا که لینک نمیسازه
        @define('resultSeparator', "لينک دانلود جواب:");
        if ($admit->debt > 0){
//            $message = substr($message, 0, strpos($message, resultSeparator));
//            $message .= "\nشما مبلغ $admit->debt ریال بدهی دارید، جهت پرداخت آن و دریافت جواب با داخلی 17 تماس حاصل فرمایید.";
//            $result = $sms->send_pattern($admit->mobile, smsAnswerPattern, ["name" => $message]);

            $link = "جواب آزمایش شما آماده است\nشما مبلغ $admit->debt ریال بدهی دارید، جهت پرداخت آن و دریافت جواب با داخلی 17 تماس حاصل فرمایید.";;
            $result = $sms->send_pattern($admit->mobile, smsOtherPattern, ["id" => $admit->billID, "link" => $link]);
        }
        elseif ($admit->log == "Downloaded"){
            $message .= " https://dl.miladlab.ir/r/$admit->pdfCode";
            $result = $sms->send_pattern($admit->mobile, smsAnswerPattern, ["name" => $message]);
        }
        else{
//            $message = substr($message, 0, strpos($message, resultSeparator));
//            $result = $sms->send_pattern($admit->mobile, smsAnswerPattern, ["name" => $message]);
        }
    }
    elseif ($admit->stateID == 2){
//        continue;
        // به خاطر آپدیت پارسیک دیگه فاییل قبض در دسترس نیست
        @define('separator', "به آزمايشگاه پاتوبيولوژي ميلاد خوش آمديد!");
        $name = substr($message, 0, strpos($message, separator));
        $link = substr($message, strpos($message, separator)+strlen(separator));
        if ($admit->log == "Downloaded")
            $link .= "دانلود قبض: https://dl.miladlab.ir/b/$admit->pdfCode";
        $result = $sms->send_pattern($admit->mobile, smsBillPattern, ["name" => $name, "link" => $link]);
    }
    else {
        $link = $message;
        $result = $sms->send_pattern($admit->mobile, smsOtherPattern, ["id" => $admit->billID, "link" => $link]);
    }

    if (!$result){
        $admit->smsLog = ('SMS send message');
        $admit->smsSendTs = (time());
	    $admit->save('admit-1402');
    }
    else{
        $admit->smsLog=('SMS send failed: '.$result);
	    $admit->save('admit-1402');
    }
//    break;
}


$sendSurvey = true;
$apiUrl = 'https://result.miladlab.ir/api/survey/patient';
$surveyApiKey = '429f730df512833bc7ceff57e9cb107d';
$shortLinkApi = "qwrfd-asdtg-hfdsx-cvgfr-fs55s";
if ($sendSurvey){
    $admits = Admit::get(['smsLog'=> "SMS send message", 'stateID'=>20, 'surveyLink'=>""])->table('admit-1402')->do;
    foreach ($admits as $i){
        try {
            $admit = new Admit();
            $admit->load($i);
            // ایجاد لینک نظرسنجی
            $data = array('admitID' => $admit->billID);
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'apiKey: ' . $surveyApiKey
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            curl_close($ch);
            if (!$response)
                throw new Exception('Fail api connect');
            $response = json_decode($response);

//            var_dump($response);
            if (!$response) // کد 0 جدید و کد منفی تکراری
                throw new Exception('Fail api connect');
            if ($response->errorCode>0)
                throw new Exception('Error api connect');
            $surveyCode = $response->code;

            $result = get_page("http://deepdream.ir/l/api/create?apikey=".$shortLinkApi."&shortLink=$surveyCode&link=https://result.miladlab.ir/survey?code=$surveyCode");
            $result = json_decode($result);
            var_dump($result);
            if (!$result || $result->errorCode>0) // کد 0 جدید و کد منفی تکراری
                throw new Exception('Fail create short link');
            $shortLinkUri = substr($result->result->shortLink, strlen("http://deepdream.ir/l/"));
            $link = "https://s.miladlab.ir/$shortLinkUri";
            $sms = new SMS();
            $result = $sms->send_pattern($admit->mobile, smsSurveyPattern, ["link" => $link]);
            if (!$result){
                $admit->surveyLink=($link);
                $admit->surveySendTs=(time());
	            $admit->save('admit-1402');
            }
            else
                throw new Exception('Fail send sms');

        }catch (Exception $e){
            $admit->surveyLink=("Fail");
            $admit->surveySendTs=($e);
	        $admit->save('admit-1402');
        }
    }
}