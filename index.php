<?php

require "config.php";
require "class/index.php";
require "area/index.php";
global $uri;
$uri = @$_SERVER['REQUEST_URI'];
if ($uri){
    $uri = substr($uri, strlen(base));
    $uri = strtolower($uri);
    $uri = substr($uri, 0, strpos($uri, '?')?:strlen($uri));
    while (str_contains($uri, "//"))
        $uri = str_replace("//","/",$uri);
    $uri = explode("/", $uri);
}else
    $uri = $argv;
$url = "";

include __DIR__."/view/functions.php";
switch (@$uri[1]) {
    case "contractor-list": $url="contractor-bill/contractor-get-list.php";break;
    case "contractor-pdf": $url="contractor-bill/contractor-save-pdf.php";break;

    case "list": $url="user-answer/get-list.php";break;
    case "list-all":
        while (1){include __DIR__."/view/user-answer/get-list.php";}
    case "pdf": $url="user-answer/save-pdf.php";break;
    case "send": $url="user-answer/send-whatsapp.php";break;
    case "sms": $url="user-answer/send-sms.php";break;

    case "patient": $url="user-bill/patient-bill.php";break;
    case "info": $url="user-bill/patient-info.php";break;
    case "sendbill": $url="user-bill/patient-send.php";break;

    case "reply-list": $url="reply/conversation-list.php";break;
    case "read-list": $url="reply/update-read-state.php";break;
    case "all":
        while (1){
            echo EOL."START>>>>>>>>>>>>>>>>".EOL;
            include __DIR__."/view/user-answer/get-list.php";
            include __DIR__."/view/user-answer/save-pdf.php";
//            include __DIR__."/view/user-answer/send-whatsapp.php";
            echo $db->DB_ERRORS;
            include __DIR__."/view/user-answer/send-sms.php";
            echo EOL."NOW>>>>>>>>>>>>>>>>";
            sleep(30);
            echo EOL."END>>>>>>>>>>>>>>>>";
        }
	case "last": // پارسال
        while (1){
            echo EOL."START>>>>>>>>>>>>>>>>".EOL;
            include __DIR__."/view/user-answer/last-year/get-list.php";
            include __DIR__."/view/user-answer/last-year/save-pdf.php";
//            include __DIR__."/view/user-answer/send-whatsapp.php";
            echo $db->DB_ERRORS;
            include __DIR__."/view/user-answer/last-year/send-sms.php";
            echo EOL."NOW>>>>>>>>>>>>>>>>";
            sleep(30);
            echo EOL."END>>>>>>>>>>>>>>>>";
        }
    case "reply":
        while (1){
            echo "___________________________________".EOL;
            include __DIR__."/view/reply/conversation-list.php";
            sleep(20);
        }
    case "read":
        while (1){
            echo "______READ______READ______READ______".EOL;
            include __DIR__."/view/reply/update-read-state.php";
            echo "______END_______END_______END_______".EOL;
            sleep(20);
        }
        break;

    case "resend-api": $url="resend-api.php";break;
    case "r": $url="user-r.php";break;
    case "b": $url="user-b.php";break;
    case "test": $url="test.php";break;

//default: header("location:".baseDir."/404");
}

switch (@$_SERVER['REDIRECT_STATUS']) {
    case "404": $url="404.php";header("HTTP/2 404 Not Found");break;
    case "403": $url="403.php";header("HTTP/2 403 Access Denied");break;
}
if (!$url){$url="404.php";@header("HTTP/2 404 Not Found");}

if (!@include("view/" . $url)) {echo "Can`t open page";}

if (0){ // Develop mode
    Admit::table()->update();
    Contractor::table()->update();
    Setting::table()->update();
    Whatsapp::table()->update();
    Conversation::table()->update();
    Patient::table()->update();
    SMS::table()->update();
}