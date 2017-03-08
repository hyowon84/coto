<?php

function getPrettyTimeFromEbayTime($eBayTimeString){
    // Input is of form 'PT12M25S'
    $matchAry = array(); // initialize array which will be filled in preg_match
    $pattern = "#P([0-9]{0,3}D)?T([0-9]?[0-9]H)?([0-9]?[0-9]M)?([0-9]?[0-9]S)#msiU";
    preg_match($pattern, $eBayTimeString, $matchAry);

    $days  = (int) $matchAry[1];
    $hours = (int) $matchAry[2];
    $min   = (int) $matchAry[3];    // $matchAry[3] is of form 55M - cast to int
    $sec   = (int) $matchAry[4];

    $retnStr = '';
    if ($days)  { $retnStr .= "$days 일"   ;  }
    if ($hours) { $retnStr .= " $hours 시" ; }
    if ($min)   { $retnStr .= " $min 분";   }
    if ($sec)   { $retnStr .= " $sec 초";   }

    return $retnStr;
} // function

function getPrettyTimeFromEbayTimeSec($eBayTimeString){
    // Input is of form 'PT12M25S'
    $matchAry = array(); // initialize array which will be filled in preg_match
    $pattern = "#P([0-9]{0,3}D)?T([0-9]?[0-9]H)?([0-9]?[0-9]M)?([0-9]?[0-9]S)#msiU";
    preg_match($pattern, $eBayTimeString, $matchAry);

    $days  = (int) $matchAry[1]*86400;
    $hours = (int) $matchAry[2]*3600;
    $min   = (int) $matchAry[3]*60;    // $matchAry[3] is of form 55M - cast to int
    $sec   = (int) $matchAry[4];

    $retnStr = $days+$hours+$min+$sec;

    return $retnStr;
} // function

function pluralS($intIn) {
    // if $intIn > 1 return an 's', else return null string
    if ($intIn > 1) {
        return '초';
    } else {
        return '';
    }
} // function


?>

