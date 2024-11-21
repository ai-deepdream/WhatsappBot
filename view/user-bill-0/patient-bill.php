<?php
$shareFolder = "C:/Users/LEGION/Desktop/shared/";
$script = Setting::option('barcode_reader');

$dir = scandir($shareFolder);
array_shift($dir);
array_shift($dir);

$dir = array_filter($dir, fn($v)=>@pathinfo($v)['extension']=="jpg");
$dir = array_values($dir);
$submitted=[];
foreach ($dir as $file){
    $file = $shareFolder.$file;
    $code = system($script." ".str_replace(' ', '+', $file));
    $code = trim($code);
    if ($code && $code != "None"){
        @rename($file, __DIR__."/../PDF/bill/$code.jpg");
        $code = @explode('-', $code)[1];
        if ($code && !in_array($code, $submitted)){
            $patient = new Patient();
            $patient->rn = $code;
            $patient->save();
            $submitted[] = $code;
        }
    }else{
        @unlink($file);
    }
}

