<?php
global $apiLink;
$whatsapp = new Whatsapp($apiLink, botSession);
$admit = new Admit();
$admits = Admit::get(['sendTs>'=>time()-24*3600, 'read!='=>'✅✅'])->reverse()->do;

//var_dump($admits);
//exit();
foreach ($admits as $i){
    $admit->load($i);
    $mobile = +$admit->mobile;
    $chats = $whatsapp->chat_messages('98'.$mobile);
//    var_dump($chats);
//    exit();
    if (!$chats['success'])
        continue;
    $chats = $chats['data'];
//    print_r($chats);
    if (@end($chats)['key']['fromMe']){  // آخرین پیام از ربات
        switch (end($chats)['status']){
            case 'PENDING': //تک تیک ارسال
                $message = @end($chats)['message']['conversation']?:@end($chats)['message']['extendedTextMessage']['text'];
                // $admit->read('☑');
                //*** سعی ارسال مجدد
                if ($admit->sendTs<time()-3600 && $admit->sendCount == 1 && !str_contains($message, 'به ربات واتساپ آزمایشگاه میلاد خوش آمدید.') && Admit::count_rows(mobile: $admit->mobile)->do == 1){ // پیام ارسال نشده
                    echo EOL."********************** $mobile ************************".EOL;
                    if (($admit->stateID==20 && file_exists(__DIR__ . "/../PDF/answer/$admit->billID.pdf"))
                        || ($admit->stateID==2 && file_exists(__DIR__ . "/../PDF/bill/$admit->billID.pdf")) ){
                        $admit->log('Downloaded');
                    }
                    else{
                        $admit->log('');
                    }
                    $admit->read('');
                }
                //*** پایان سعی ارسال مجدد
                break;
            case 'SERVER_ACK':
                $admit->read('☑☑');
                break;
            case 'DELIVERY_ACK': // رسید سین نزد
                $admit->read('☑☑');
                break;
            case 'READ':
                $admit->read('✅✅');
                break;
        }
    }else{ // آخرین پیام از کاربر
        $admit->read('✅✅');
        krsort($chats);
        foreach ($chats as $chat){
            if ($chat['key']['fromMe']){
                switch (end($chats)['status']){
                    case 'PENDING': //تک تیک ارسال
                        $message = end($chats)['message']['extendedTextMessage'];
//                        $admit->read('☑');
                        break;
                    case 'SERVER_ACK':
                        $admit->read('☑☑');
                        break;
                    case 'DELIVERY_ACK': // رسید سین نزد
                        $admit->read('☑☑');
                        break;
                    case 'READ':
                        $admit->read('✅✅');
                        break;
                }

                break; // ادامه پیام ها را چک نکن
            }
        }
    }

}