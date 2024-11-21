<?php
function read_suf($file):array
{
    $base_dir=getcwd();
    chdir(__DIR__."/../SUF");
    $db= file_get_contents($file.".php");
    $db= substr($db,31);
    $db = unserialize($db);
    chdir($base_dir);
    return $db;
}
function write_suf($db,$file):bool
{
    do{
    $base_dir=getcwd();
    chdir(__DIR__."/../SUF");
    $db = serialize($db);
    $sec = '<?php header("location:../");?>';
    $db = $sec.$db;
    $f = fopen($file.".php","w+");
    fwrite($f,$db);
    }while (!is_array(read_suf($file)));
    chdir($base_dir);
    return true;
}
function delete_suf($file):bool
{
    $base_dir=getcwd();
    chdir(__DIR__."/../SUF");
    unlink($file.".php");
    chdir($base_dir);
    return true;
}

?>

