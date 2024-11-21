<?php /* In the name of Allah == بسم اللّه الرّحمن الرّحیم */

function router(string $name, $value="", string $address=""): string
{
    if (!$value)
        $value = $name;
    if (!$address)
        $address = $name.".php";
    define($name, $value);
    return $address;
}
function enter_to_br($text): string
{
    return str_replace("\n","<br/>",str_replace("\r","<br/>",str_replace("\r\n","<br/>",$text)));
}
function enter_to_null($text): string
{
    return str_replace("\n","",str_replace("\r","",str_replace("\r\n","",$text)));
}
function determiner($string, $separator = "\n"): array
{
    if ($separator == "\r\n"){$separator="\n";}
    if ($separator == "\r"){$separator="\n";}
    $string = str_replace("\r\n","\n",$string);
    $string = str_replace("\r","\n",$string);
    return array_filter(explode($separator,$string));
}
function search_2D($key,$needle,array $array,$DD=false,$reIndex=false):array
{
    if (!$key)
        $key='ID';
    //$DD     2D Export (all of existing rows)
    $keys = array_keys(array_column($array, $key), $needle);
    $array = array_intersect_key($array, array_flip($keys));
    if (!$DD)
        return $array[array_key_first($array)];
    if ($reIndex)
        return array_values($array);
    return $array;
}
use function search_2D as search_2D_array; // Create aliases function

function checkbox_null_to_0(&$form, ...$vars):void
{
    foreach ($vars as $var){
        if (!@$form[$var]) $form[$var]="0";
    }
}
function is_english(...$vars):bool
{
    $vars = array_map(function($var){
        if(preg_match('/[^0-9a-zA-Z]/', $var)){
            return false;
        }
        return true;
    }, $vars);
    $vars = array_unique($vars);
    if ($vars[0]==true && count($vars)==1)
        return true;
    return false;
}
function num_to_persian($str, $momayez = '٫'): array|string
{
    $num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
    $key_a = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $momayez);
    return str_replace($num_a, $key_a, $str);
}
function persianNum_to_english($string) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨','٩'];

    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
    return $englishNumbersOnly;
}
function show_input_image(string $image):string
{
    if (is_file('view/'.$image)){
        return base.'/view/'.$image;
    }
    return $image;
}
function redirect($dist,$delay=0):void
{
    if ($delay){
        header("Refresh: $delay;url=".base."/".$dist);
    }else{
        header("Location: ".base."/".$dist);
    }
}
function sum(...$nums):int
{
    if (is_array(@$nums[0]))
        $nums = array_merge(...$nums);
    return array_sum($nums);
}

function rand_string(int $length = 10, string $mode='combine'): string
{
    $mode = match ($mode){
        'combine'=> '0123456789abcdefghijklmnopqrstuvwxyz',
        'letter'=> 'abcdefghijklmnopqrstuvwxyz',
        'upperCase'=> 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'number'=> '0123456789',
    };
    return substr(str_shuffle(str_repeat($mode, mt_rand(1,$length))), 1, $length);
}
function upload_image($file_uploaded,$place,$replace_with=null,$name=null,$rand_name=null,$max_space=1048576):string
{
    if (isset($file_uploaded)&&($file_uploaded['size'])>"0") {
        $image = $file_uploaded;
        $image['name'] = strtolower($image['name']);
        $img_end = array("png", "jpg", "gif", "jpeg", "webp", "svg");
        $explode = explode(".", $image['name']);
        $end = end($explode);
        if ($image['size'] > $max_space) {
            return "b";$error=1;
        }
        if (!in_array($end, $img_end)) {
            return "c";$error=1;
        }
        if (empty($name)){
            if (empty($rand_name)){//نام خود فایل
                $file_name=$image['name'];
            }else{ // نام رندم
                $file_name="";
                for ($a=1;$a<=$rand_name;$a++){
                    $file_name = $file_name.rand_string(5)."-";
                }
                $file_name = substr($file_name,0,-1);
            }
        }else{
            $file_name = $name;
        }
        if (!@$error) {
            if (is_file($replace_with)) {
                unlink($replace_with);
            }
            if (is_dir($place)){
                move_uploaded_file($image["tmp_name"], "$place"."/".$file_name.".".$end);
                return "$place"."/".$file_name.".".$end;
            }else{return "d";}
        }
    }
    return "a";
    //a= فایل موجود نیست
    //b= حجم بیش از اندازه مجاز است
    //c= پسوند غیر مجاز است
    //d= پوشه موجود نیست
}
function upload_file($file_uploaded,$place,$replace_with=null,$name=null,$rand_name=null,$accept_ext=null,$max_space=1048576):string
{
    if (isset($file_uploaded)&&($file_uploaded['size'])>"0") {
        $image = $file_uploaded;
        $image['name'] = strtolower($image['name']);
        $explode = explode(".", $image['name']);
        $end = end($explode);
        if ($image['size'] > $max_space) {
            return "b";$error=1;
        }
        if ($accept_ext){
            if (!in_array(strtolower($end), $accept_ext)) {
                return "c";$error=1;
            }
        }
        if (in_array(strtolower($end), array("php","php5","php7","php8","phtml"))) {
            return "c";$error=1;
        }
//
        if (empty($name)){
            if (empty($rand_name)){//نام خود فایل
                $file_name = basename($image['name'],".".$end);
            }else{ // نام رندم
                $file_name = "";
                for ($a=1;$a<=$rand_name;$a++){
                    $file_name = $file_name.rand_string(5)."-";
                }
                $file_name = substr($file_name,0,-1);
            }
        }else{
            $file_name = $name;
        }
        if (!@$error) {
            if (is_file($replace_with)) {
                unlink($replace_with);
            }
            if (is_dir($place)){
                move_uploaded_file($image["tmp_name"], "$place"."/".$file_name.".".$end);
                return "$place"."/".$file_name.".".$end;
            }else{return "d";}
        }
    }
    return "a";
    //a= فایل موجود نیست
    //b= حجم بیش از اندازه مجاز است
    //c= پسوند غیر مجاز است
    //d= پوشه موجود نیست
}
function add_xml($url,$xml_file,$priority,$changeFreq):void
{
    $file = simplexml_load_file($xml_file);
    $file ->addChild("url","");
    $n = count($file);
    $n--;
    $today = date("Y-m-d");
    $end = $file->url[$n];
    $end ->addChild("loc",$url);
    $end ->addChild("priority",$priority);
    $end ->addChild("changefreq",$changeFreq);
    $end ->addChild("lastmod",$today);
    $file->saveXML($xml_file);
}
function add_to_sitemap($file,$url,$priority,$changeFreq,$ts):void
{
    $file ->addChild("url","");
    $n = count($file);
    $n--;
    $day = date("Y-m-d",$ts);
    $end = $file->url[$n];
    $end ->addChild("loc",$url);
    $end ->addChild("priority",$priority);
    $end ->addChild("changefreq",$changeFreq);
    $end ->addChild("lastmod",$day);
}
function array_to_xml($array, $rootElement = null, $xml = null, $newKey=null): bool|string
{
    $_xml = $xml;
    // If there is no Root Element then insert root
    if ($_xml === null)
        $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');
    foreach ($array as $k => $v) {
        if (is_array($v))
            array_to_xml($v, $k, $_xml->addChild($newKey?:$k));
        else
            $_xml->addChild($k, $v);
    }
    return $_xml->asXML();
}

function get_form_data($method="post"): array
{
    $form = match (strtolower($method)){
        "post"=>$_POST,
        "get"=>$_GET,
        "files"=>$_FILES,
        "request"=>$_REQUEST
    };
    return array_map(function ($value){
        return  trim(htmlspecialchars($value));
    },$form);
}
function get_form_data_html($method="post"): array
{
    return match (strtolower($method)){
        "post"=>$_POST,
        "get"=>$_GET,
        "files"=>$_FILES,
        "request"=>$_REQUEST
    };
}

function zip_dir($dirPath,$zipPath):bool
{
    $zipper = new ZipArchiver;
    return $zipper->zipDir($dirPath, $zipPath);
}
function unzip_dir($zipPath,$dirPath):bool
{
    $zip = new ZipArchive;
    $res = $zip->open($zipPath);
    if ($res === TRUE) {
        $zip->extractTo($dirPath);
        $zip->close();
        return true;
    } else {
        echo 'failed, code:' . $res;
        return false;
    }
}

function array_to_JS_list(array $data=array() , $mark = '"' ):string
{
    $str = '[';
    foreach ($data as $item){
        $str .= $mark.$item.$mark.', ';
    }
    $str = rtrim($str,', ');
    $str .= ']';
    return $str;
}
if (!function_exists('str_contains')){
    function str_contains($haystack, $needle):bool
    {
        if (strpos($haystack, $needle) !== false){
            return true;
        }
        return false;
    }
}
function delete_folder($path):void
{
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ($file <> "." AND $file <> "..") {
                if (is_file($path . '/' . $file)) {
                    @unlink($path . '/' . $file);
                }
                if (is_dir($path . '/' . $file)) {
                    delete_folder($path . '/' . $file);
                    @rmdir($path . '/' . $file);
                }
            }
        }
    }
}
function get_page($url):string
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL verification
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function size_unit_format($bytes, $decimal=2, $unit=''):string
{
    if ($unit){
        switch ($unit){
            case 'TB': $bytes = number_format($bytes / 1099511627776, $decimal).' TB';break;
            case 'GB': $bytes = number_format($bytes / 1073741824, $decimal).' GB';break;
            case 'MB': $bytes = number_format($bytes / 1048576, $decimal).' MB';break;
            case 'KB': $bytes = number_format($bytes / 1024, $decimal).' KB';break;
            case 'byte':
                if ($bytes>0){
                    $bytes = $bytes . ' bytes';
                }else{
                    $bytes = $bytes . ' byte';
                }
        }
    }else{
        if ($bytes >= 1099511627776)
        {
            $bytes = number_format($bytes / 1073741824, $decimal).' TB';
        }
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, $decimal).' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, $decimal).' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, $decimal).' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
    }
    return $bytes;
}

function test_request_times():void
{
    //این تابع برسی می‌کند که با هر بار فراخوانی، چند بار اجرا می‌شود.
    //فایل‌های 404 تعداد ریکوپست را زیاد می‌کند.
    file_put_contents("req.txt",file_get_contents("req.txt")+1);
}