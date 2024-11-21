<?php
const botSession = "milad";
const smsAnswerPattern = "lbj8s67s1vu4y4v";
const smsBillPattern = "qrce1vk7jpnuyjz";
const smsOtherPattern = "fg163f6pv0dwwn3";
const smsSurveyPattern = "napy5djslzwkels";
$apiLink = Setting::option('whatsappBotApi');
Whatsapp::$proxy = Setting::option('proxy');
function get_list($id, $mode="user"){
    $api = match ($mode){
        "user"=>Setting::option("sms_queue_list"),
        "contractor"=>Setting::option("contractor_bill_list"),
        "patient"=>Setting::option("patient_info"),
    };
    $data = ['id'=>$id];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
//	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'db: DBMilad_0201')); // Add the header here

    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, 1);
}
function link_to_pdf($link){  // For last version
    $link = substr($link, strpos($link, 'remoteAuthentication=')+21);
    $link = 'https://my.miladlab.ir/AccountUMM/RemoteResultShow?remoteAuthentication='.$link;
//    $link = 'https://192.168.0.240/AccountUMM/RemoteResultShow?remoteAuthentication='.$link;
    return $link;
}

function download_pdf($link, $dirAndName): string
{  // For last version
    $file = fopen(__DIR__."/PDF/$dirAndName.pdf", "w+");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_FILE, $file);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    curl_exec($ch);
    if (curl_errno($ch)) {
        $ret = "Download failed: ".curl_error($ch);
    } else {
        $status = curl_getinfo($ch);
        $ret = $status["http_code"]==200 ? "" : "Download failed: " . $status["http_code"];
    }
    curl_close($ch);
    fclose($file);
    return $ret;
}

function base64_PDF($admitID, $dirAndName)
{
    $url = 'https://my.miladlab.ir:8084//Service1.asmx/Web_ResultReport_AllInOneBase64PDF_WithSQLDbID_Json?UserName=AngularWeb&Password=bch%2FYnalIfvI5wgvqBSRIPh3htzofviZS3ccf0mjwm5FBDYMvIhQDxdfPkqCH2Ni&SQLDbID=16&AdmitID='.$admitID.'&app=1&_IncludeInCompletePatient=true&_IncludePrintedPatient=true&_IncludedNotConfirmedPatient=true&_MyCaller=0&_WithoutPayLimitCheck=false&_CheckInterview=true&_requester=0&_SourceLogRemoteDelivery=3';
    $code = get_page($url);
    if (!$code)
        return 0;
    $code = json_decode($code)->Base64PDF;
    $code = base64_decode($code);
    return file_put_contents(__DIR__."/PDF/$dirAndName.pdf", $code);
}