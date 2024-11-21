<?php
$admit = new Admit();
$admit->set()->table('admit-1402')->reverse();
$last = $admit->systemID?:0;
echo "search for add after $last".EOL;
$data = get_list($last)?:[];
$c=0;
foreach ($data as $i){
    $admit->set(systemID: $i['systemID']);
    if ($admit->check())
        continue;
    $c++;
    $admit->clear();
    $admit->load($i);
    $admit->billID = trim($admit->billID);
    $admit->incomTs = time();
    $admit->save('admit-1402');
}

echo "added $c rows from ".@count($data)." rows".EOL;