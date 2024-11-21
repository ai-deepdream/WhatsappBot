<?php
 /*
 @@@ based on https://github.com/ookamiiixd/baileys-api
 @@@ document: https://documenter.getpostman.com/view/18988925/UVeNni36
 */
class Whatsapp extends core
{
    static $proxy;
    public $ID;
    public $session;
    public ?string $reciver=null;
    public $type;
    public $text;
    public $file;
    public $state;
    public $time;
    public $log;

    protected $api;

    public function __construct($api, $session)
    {
        $this->api=$api;
        $this->session=$session;
    }
    public function resend()
    {
        $this->send($this->reciver, $this->type, $this->text, $this->file,
            ($mime = mime_content_type(__DIR__.substr($this->file,  strlen(protocol.siteAddress))))!='directory'?$mime:'',
            (str_contains($this->file, 'answer')?'جواب'.basename($this->file):
                (str_contains($this->file, 'bill')?'قبض'.basename($this->file):'')
            )
            ,false
        );
    }
    public function send(string $reciver, $type, $text='', $file='', $mimeType='', $fileName='', $save=true)
    {
        $this->reciver=$reciver;
        $this->type=$type;
        $this->text=$text;
        $this->file=$file;
        switch ($type){
            case "text":
                $pattern = json_decode('{"receiver": "**********", "message": { "text": "hello!" } }', 1);
                $pattern['receiver'] = $reciver;
                $pattern['message']['text'] = $text;
                $res = $this->api_curl("/chats/send?id=$this->session", $pattern);
                if ($res===false){
                    $this->state = "api failed";
                    $this->log = "can`t connect api";
                    break;
                }
                $res = json_decode($res, 1);
                if ($res['success'])
                    $this->state = "success";
                else
                    $this->state = "failed";
                $this->log = $res['message'];
                break;
            case "image":
                $pattern = json_decode('{ "receiver": "**********", "message": { "image": { "url": "" }, "caption": "" } }', 1);
                $pattern['receiver'] = $reciver;
                $pattern['message']['caption'] = $text;
                $pattern['message']['image']['url'] = $file;
                if (!$file){
                    $this->state = "file not set";
                    $this->log = "set file address is require";
                    break;
                }
                $res = $this->api_curl("/chats/send?id=$this->session", $pattern);
                if ($res===false){
                    $this->state = "api failed";
                    $this->log = "can`t connect api";
                    break;
                }
                $res = json_decode($res, 1);
                if ($res['success'])
                    $this->state = "success";
                else
                    $this->state = "failed";
                $this->log = $res['message'];
                break;
            case "video":
                $pattern = json_decode('{ "receiver": "**********", "message": { "video": { "url": "" }, "caption": "" } }', 1);
                $pattern['receiver'] = $reciver;
                $pattern['message']['caption'] = $text;
                $pattern['message']['video']['url'] = $file;
                if (!$file){
                    $this->state = "file not set";
                    $this->log = "set file address is require";
                    break;
                }
                $res = $this->api_curl("/chats/send?id=$this->session", $pattern);
                if ($res===false){
                    $this->state = "api failed";
                    $this->log = "can`t connect api";
                    break;
                }
                $res = json_decode($res, 1);
                if ($res['success'])
                    $this->state = "success";
                else
                    $this->state = "failed";
                $this->log = $res['message'];
                break;
            case "document":
                $pattern = json_decode('{ "receiver": "**********", "message": { "document": { "url": "" }, "": "application/pdf", "fileName": "" } }', 1);
                $pattern['receiver'] = $reciver;
                $pattern['message']['document']['url'] = $file;
                if ($mimeType) $pattern['message']['mimetype'] = $mimeType;
                else unset($pattern['message']['mimetype']);
                if ($fileName) $pattern['message']['fileName'] = $fileName;
                else unset($pattern['message']['fileName']);

                if (!$file){
                    $this->state = "file not set";
                    $this->log = "set file address is require";
                    break;
                }
                $res = $this->api_curl("/chats/send?id=$this->session", $pattern);
                if ($res===false){
                    $this->state = "api failed";
                    $this->log = "can`t connect api";
                    break;
                }
                $res = json_decode($res, 1);
                if ($res['success'])
                    $this->state = "success";
                else
                    $this->state = "failed";
                $this->log = $res['message'];
                break;
        }
        $this->time=time();
        if ($save)
            $this->save();
        return $this->state=="success"?$this->state:$this->log;
    }
    
    public function conversation_list()
    {
        return json_decode($this->api_curl("/chats?id=".$this->session), 1);
    }
    public function chat_messages($reciver='')
    {
        if (!$reciver) $reciver=$this->reciver;
        return json_decode($this->api_curl("/chats/$reciver@s.whatsapp.net?id=$this->session&limit=25"), 1);
    }
    public function count_of_last_sends($reciver=''):int    // تعداد ردیفهای ارسال پیام برای این کاربر
    {
        if (!$reciver) $reciver=$this->reciver;
        return self::count_rows(reciver: $reciver)->do;
    }
    private function api_curl($url, $data=[]): bool|string
    {
        $ch = curl_init($this->api.$url);
        if ($data){
            $payload = json_encode($data);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
        }
        if (self::$proxy)
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Connection: keep-alive'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return $result;
    }
}
