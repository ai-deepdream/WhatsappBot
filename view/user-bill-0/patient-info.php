<?php
$patient = new Patient();
$patients = Patient::get(log: '')->do;

foreach ($patients as $i){
    $patient->clear();
    $patient->load($i);
    $data = get_list($patient->rn, "patient");
    if (@$data){
        $patient->load($data[0]);
        $patient->log = "Get data";
        $patient->save();
    }else
        $patient->log("Not found");

    var_dump($data);
}
//var_dump($patients);