<?php
if (isset($_SERVER['SERVER_PROTOCOL'])){
    ob_start();
    session_start();
}
ini_set("memory_limit", "2048M");
//ini_set("max_execution_time","3600");
date_default_timezone_set("asia/tehran");
if (!defined("areaCore")){header("location:../404"); exit();}
if (PHP_MAJOR_VERSION!=8){
    echo "PHP version is not supported. (".PHP_VERSION.")".EOL;
    exit();
}
include('suf.php');
include('db_functions.php');
include('jdf.php');
include('mail.php');
include('public.php');
include('ZipArchiver.php');
include('PDFRotate/vendor/autoload.php');
//include('PhpSpreadsheet(excel).php');
$get = $_GET;
$post = $_POST;
?>