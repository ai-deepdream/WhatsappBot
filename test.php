<?php
$data = [
    'id'=> "milad",
    'isLegacy'=> false
];
$ch = curl_init("http://185.110.190.86:8070/sessions/add");
if ($data){
    $payload = json_encode($data);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
}
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

$result = json_decode($result);
var_dump($result);
echo "\n______________________\n";
$result = $result->data->qr;

$result = str_replace('data:image/png;base64,', '', $result);
$result = str_replace(' ', '+', $result);
$result = base64_decode($result);
$success = file_put_contents("QR-Code.jpg", $result);
exec("QR-Code.jpg");