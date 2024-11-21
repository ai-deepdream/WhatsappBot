<?php
//const siteAddress = "192.168.1.100/cov-whatsappBot";
const siteAddress = "dl.miladlab.ir";

const base = "/cov-bime";
const panel = base . "/admin";
const siteVersion = "v0.0.2";
const areaCore = "v0.0.3";
const _ = null;
if (isset($_SERVER['SERVER_PROTOCOL'])){
    define('EOL', '<br>');
    define("protocol", $_SERVER['REQUEST_SCHEME']."://");
}
else{
    define('EOL', PHP_EOL);
    define("protocol", __DIR__);
}

$DB_Config = array(
    'mode'=>'mysql',
    'hostname'=>'localhost',
    'port'=>'3306',
    'username'=>'root',
    'password'=>'',
    'dbname'=>'cov-whatsappBot'
);

//$DB_Config = array(
//    'mode'=>'mysql',
//    'hostname'=>'whatsapp.deepdream.ir',
//    'port'=>'3307',
//    'username'=>'root',
//    'password'=>'whs1',
//    'dbname'=>'whatsappBot'
//);