<?php
$contractor = new Contractor();
$contractor->set()->reverse();
$last = $contractor->systemID?:780;
echo "Contractor: search for add after $last".EOL;
$data = get_list($last, "contractor");
$c=0;
foreach ($data as $i){
    $contractor->set(systemID: $i['systemID']);
    if ($contractor->check())
        continue;
    $c++;
    $contractor->clear();
    $contractor->load($i);
    $contractor->save();
}

echo "Contractor: added $c rows from ".count($data)." rows".EOL;
