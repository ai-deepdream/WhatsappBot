<?php
////////////////////////////////////////////////////////////
function check_db($needle, $db, $col, $rows=0): int
{
        if ($rows==0){
            if (isset($db[0][0])){$rows=$db[0][0];}
        }
        $exit=false;
        for ($n=1;$n<=$rows;$n++){
            if ($needle == @$db[$n][$col]){
                return $n;
            }
        }
        return 0;
    }
    function check_db_each($needle,$db,$col): int|string
    {
        foreach ($db as $item=>$value){
            if ($needle == @$value[$col]){
                return $item;
            }
        }
        return -1;
    }
    function negative_check_db($needle,$db,$col,$rows=0)
    {
        if ($rows==0){
            if (isset($db[0][0])){$rows=$db[0][0];}
        }
        $exit=false;
        for ($n=$rows;$n>"0";$n--){
            if ($needle == $db[$n][$col]){
                return $n;
            }
        }
        return 0;
    }
    function count_db($needle,$db,$col,$rows=0): int
    {
        if ($rows==0){
            if (isset($db[0][0])){$rows=$db[0][0];}
        }
        $count=0;
        for ($n=1;$n<=$rows;$n++){
            if ($needle == $db[$n][$col]){
                $count++;
            }
        }
        return $count;
    }
?>

