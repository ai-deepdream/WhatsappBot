<?php
$res = [
    'status'=> "successful",
    'errorCode'=> 0,
    'message'=> ""
];
$billID = $get['ID'];
if (!$billID){
    $res['errorCode'] = 1;
    $res['message'] = "invalid ID";
    goto result;
}
$admit = new Admit();
$admit->set(billID: $billID, stateID: 20)->reverse();
if (!$admit->check()){
    $res['errorCode'] = 1;
    $res['message'] = "invalid admit";
    goto result;
}

if (file_exists(__DIR__ . "/PDF/answer/$admit->billID.pdf"))
    $admit->log('Downloaded');
else
    $admit->log('');

$admit->smsLog('');

$res['message'] = "resend request submitted";
goto result;

result:
if (@$res['errorCode'] > 0)
    $res['status'] = "failed";
echo json_encode($res);
header("content-type: application/json; charset=utf-8");
