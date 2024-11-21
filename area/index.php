<?php
if (!defined("areaCore")){header("location:../404"); exit();}
require_once "core.php";
require_once "ORM.php";
require_once "ORM_table.php";
require_once "PDO_SQL.php";

spl_autoload_register(function ($class) {
    if (str_contains($class, "\\"))
        $class = str_replace("\\", "/", $class);
    @include __DIR__.'/../model/'.strtolower($class).'.php';
});

global $DB_Config;
$db = new PDO_SQL($DB_Config);
$db->connect() || exit();
ORM::$DB = $db;
