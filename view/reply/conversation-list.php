<?php
global $apiLink;
$whatsapp = new Whatsapp($apiLink, botSession);

$list = $whatsapp->conversation_list();
if (!@$list['success']){
    echo "Get list not successful.";
    goto cl1;
}
$list = $list['data'];
$list = array_reduce($list, function($result, $v) {
    $result[explode("@", $v['id'])[0]] = $v['conversationTimestamp'];
    return $result;
}, []);

var_dump($list);
$conversation = new Conversation();
foreach ($list as $mobile=>$time){

    echo $mobile.EOL;
    $conversation->clear();
    $conversation->set(mbile: $mobile)->reverse();
    if ($time <= $conversation->time)
        continue;
    $conversation->mbile = $mobile;
    $conversation->time = $time;

    $conversation->save();
    $whatsapp->reciver = $mobile;
    $chats = $whatsapp->chat_messages();
    if (!@$chats['success'])
        continue;
    var_dump($chats);
    $chats = $chats['data'];
//    krsort($chats);
    if (end($chats)['key']['fromMe']){ // آخرین ارسالی ربات
/*        if ($time <= time()+3600)
            continue;
//    if (end($chats)['key']['fromMe']){ // آخرین ارسالی ربات
        echo "<b style='color: red'>$mobile</b><br>";
        $message = end($chats)['message']['extendedTextMessage'];
        if (end($chats)['status'] == "PENDING" && !str_contains($message, 'به ربات واتساپ آزمایشگاه میلاد خوش آمدید.')){ // پیام ارسال نشده
            if (Admit::count_rows(mobile: "0".substr($mobile, 2))->do == 1) { // اولین و تنها پیام ارسالی از ربات
                echo EOL."********************** $mobile ************************";
                $admit = new Admit();
                $admit->set(mobile:  substr($mobile, 2))->reverse();
                if ($admit->check()){   // دریافت آخرین ردیف این شماره
                    if (($admit->stateID==20 && file_exists(__DIR__ . "/../PDF/answer/$admit->billID.pdf"))
                        || ($admit->stateID==2 && file_exists(__DIR__ . "/../PDF/bill/$admit->billID.pdf")) ){
                        $admit->log('Downloaded');
                    }
                    else
                        $admit->log('');
                }
            }
        }
//      echo "E1".EOL;*/
    }else{ // آخرین ارسالی کاربر
        $message = @end($chats)['message']['conversation']?:@end($chats)['message']['extendedTextMessage']['text'];
        $message = persianNum_to_english($message);
        $charFirst = ["P", "C", "S", "M"];
        if (in_array(strtoupper(substr($message, 0, 1)), $charFirst)  || ($message>0 && $message<10000000)){
            // سرچ شماره پذیرش
            echo EOL."Resend billID: ".$message.EOL.EOL;
            $admit = new Admit();
            $admit->set(billID:  $message)->reverse();
            if ($admit->check()){
                if (is_numeric($admit->mobile) && $admit->mobile>0 && str_ends_with($mobile, ltrim($admit->mobile,"0"))){ // ارسال مجدد آخرین وضعیت
                    echo EOL."Resend for $mobile after 1 hour".EOL;
                    if (($admit->stateID==20 && file_exists(__DIR__ . "/../PDF/answer/$admit->billID.pdf"))
                    || ($admit->stateID==2 && file_exists(__DIR__ . "/../PDF/bill/$admit->billID.pdf")) ){
                        $admit->log('Downloaded');
                        // پیدا کردن پیام قبض اگر باشد
                        if ($admit->stateID==20){
                            $admit2 = new Admit();
                            $admit2->set(['billID'=> $message, 'ID<'=>$admit->ID, 'stateID'=>2])->reverse();
                            if ($admit2->stateID==2 && file_exists(__DIR__ . "/../PDF/bill/$admit2->billID.pdf"))
                                $admit2->log('Downloaded');
                            else
                                $admit2->log('');
                        }
                    }
                    else
                        $admit->log('');
                    $whatsapp->send($mobile, "text", "آخرین وضعیت این آزمایش تا دقایقی دیگر ارسال می‌گردد...\r\nلطفا منتظر بمانید.");
                }else{
                    $whatsapp->send($mobile, "text", "شماره همراه برای این آزمایش نامعتبر است.");
                }
            }else{
                $whatsapp->send($mobile, "text", "آزمایشی با شماره وارد شده یافت نشد. ممکن است هنوز جواب آزمایش شما آماده نشده باشد.");
            }
//            var_dump($admit);
        }else{
            // پیام پیشفرض
            $defaultText = "مراجعه کننده گرامی\r\nبه ربات واتساپ آزمایشگاه میلاد خوش آمدید.\r\nدر صورتی که جواب آزمایش یا متن پیام برایتان نمایش داده نمی‌شود،\r\n\r\n *لطفا فقط شماره پذیرش خود را ارسال نمایید*.\r\n\r\n(در غیر این صورت پاسخ این پیام را ندهید.)\r\n\r\nجهت رسیدگی به امور دیگر با شماره تلفن آزمایشگاه تماس بگیرید یا در واتسپ به شماره زیر پیام دهید.\r\nhttps://wa.me/989138800506";
//            var_dump($mobile);
            $whatsapp->send($mobile, "text", $defaultText);
        }
    }
//    var_dump($chats);
}
echo $db->DB_ERRORS;
cl1: