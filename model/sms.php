<?php

class SMS extends core
{
    public $ID;
    public $receiver;
    public $text;
    public $ts;
    public $state;
    private $smsSetting;

    public function __construct()
    {
        $setting = Setting::get(type: 'sms')->do;
        $this->smsSetting = array_combine(array_column($setting, 'name'), array_column($setting, 'value'));

    }

    public function send($receiver, $text, $timeLimit=false)
    {
        $this->receiver = $receiver;
        $this->text = $text;

        if (!$this->state && $timeLimit && $this->smsSetting['minimumSendTime']) {
            $timeLimit = $this->smsSetting['minimumSendTime'];
            $lastSms = $this->get_object_data(['receiver'=>$receiver,'state' => 'success','~'=>null])[0];
            if ($lastSms) {
                if ($lastSms['ts'] + $timeLimit*60 > time()) {
                    $this->state = "Canceled by time limit";
                }
            }
        }
        if (!$this->state){
            switch ($this->smsSetting['mode']){
                case 'faraz sms':
                    $url = "https://ippanel.com/services.jspd";
                    $rcpt_nm = array($receiver);
                    $param = array
                    (
                        'uname'=>$this->smsSetting['username'],
                        'pass'=>$this->smsSetting['password'],
                        'from'=>$this->smsSetting['senderNumber'],
                        'message'=>$text,
                        'to'=>json_encode($rcpt_nm),
                        'op'=>'send'
                    );
                    $handler = curl_init($url);
                    curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
                    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                    $response2 = curl_exec($handler);
                    curl_close($handler);
                    $response2 = json_decode($response2);
                    $res_code = $response2[0];
                    $res_data = $response2[1];

                    $this->state = $res_data;
                    break;
                default:$this->state = "Mode not defined.";
            }
            $this->ts = time();
            $this->save_object_data();
        }
        if ($this->state == "Success")
            return false;
        return $this->state;
    }
    public function send_pattern($receiver, $pattern, $vars, $timeLimit=false)
    {
        $this->receiver = $receiver;
        $this->text = $pattern."\n".implode(", ", $vars);

        if (!$this->state && $timeLimit && $this->smsSetting['minimumSendTime']) {
            $timeLimit = $this->smsSetting['minimumSendTime'];
            $lastSms = $this->get_object_data(['receiver'=>$receiver,'state' => 'success','~'=>null])[0];
            if ($lastSms) {
                if ($lastSms['ts'] + $timeLimit*60 > time()) {
                    $this->state = "Canceled by time limit";
                }
            }
        }
        if (!$this->state){
            switch ($this->smsSetting['mode']){
                case 'faraz sms pattern':
                    $from = $this->smsSetting['senderNumber'];
                    $to = array($receiver);
                    $url = "https://ippanel.com/patterns/pattern?username=" . $this->smsSetting['username'] . "&password=" . urlencode($this->smsSetting['password']) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($vars)) . "&pattern_code=$pattern";
                    $handler = curl_init($url);
                    curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($handler, CURLOPT_POSTFIELDS, $vars);
                    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($handler);
                    curl_close($handler);
                    if (is_array($response)){
                        $response = json_decode($response);
                        $this->state = $response[1];
                    } elseif ($response>0)
                        $this->state = "Success";
                    else
                        $this->state = "Can not connect api";
                    break;
                default:$this->state = "Mode not defined.";
            }
            $this->ts = time();
            $this->save();
        }
        if ($this->state == "Success")
            return false;
        return $this->state;
    }
}