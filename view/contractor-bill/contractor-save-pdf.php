<?php
$contractor = new Contractor();
$contractors = $contractor::get()->has('data')->do;

foreach ($contractors as $i){
    $contractor->load($i);
    $data = $contractor->data;
    $data = substr($data, strlen('<?xml version="1.0" standalone="yes"?>')+2);

//    $data = simplexml_load_string($data);
//    $data = json_decode(json_encode($xml),1);
//    $array = XML2Array($xml);
//    $array = array($xml->getName() => $array);

    echo ($data);
/*    $admit->load($i);
    if ($admit->state != "ت آزمايشگاه")
        continue;
    echo "Download pdf for ID: $admit->ID".EOL;
    $resultPage = link_to_pdf($admit->link);
    echo $resultPage.EOL;
    if ($dl = download_pdf($resultPage, $admit->rn)){
        $admit->log($dl);
        @unlink(__DIR__."/../PDF/user/$admit->rn.pdf");
    }else{
        $admit->log("Downloaded");
    }*/
    break;
}

function XML2Array(SimpleXMLElement $parent)
{
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
        && (1 === count($node) ? $node = array($node) : 1)
        && $node = & $node[];

        $node = $element->count() ? XML2Array($element) : trim($element);
    }

    return $array;
}


/*
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj/kunMOd0CCLDP+CeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=&App=1
/AccountUMM/RemoteResultShowWithSurvey/35886?remoteAuthentication=AWx%2F9lfWpQ%2B%2F%2B6DxctV5Rxjoz997Lx85U2gFJ8K7AHzMKDHMmF9nYj%2FkunMOd0CCLDP%2BCeEJnvxfqkAJ8RRtLPTRDRWSSBBdUGB8LB3A9OPMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k%3D&App=1

https://my.miladlab.ir/AccountUMM/Remote?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
/AccountUMM/RemoteResultShow?remoteAuthentication=AWx/9lfWpQ+/+6DxctV5R26JInJx8UsYWBNwj1AVxXHtdkL1ZEnUOD/kunMOd0CCLDP+CeEJnvyfFMKXvBlHd/TRDRWSSBBdrZ0h4ZrFd8DMHIkEJg2aiQKa48Yc4NQKkek9kCmxz8k=
*/