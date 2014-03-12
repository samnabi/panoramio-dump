<?php

function setGeolocation(
        $pelSubIfdGps, $latitudeDegreeDecimal, $longitudeDegreeDecimal) {
    $latitudeRef = ($latitudeDegreeDecimal >= 0) ? 'N' : 'S';
    $latitudeDegreeMinuteSecond
            = degreeDecimalToDegreeMinuteSecond(abs($latitudeDegreeDecimal));
    $longitudeRef= ($longitudeDegreeDecimal >= 0) ? 'E' : 'W';
    $longitudeDegreeMinuteSecond
            = degreeDecimalToDegreeMinuteSecond(abs($longitudeDegreeDecimal));

    $pelSubIfdGps->addEntry(new PelEntryAscii(
            PelTag::GPS_LATITUDE_REF, $latitudeRef));
    $pelSubIfdGps->addEntry(new PelEntryRational(
            PelTag::GPS_LATITUDE, 
            array($latitudeDegreeMinuteSecond['degree'], 1), 
            array($latitudeDegreeMinuteSecond['minute'], 1), 
            array(round($latitudeDegreeMinuteSecond['second'] * 1000), 1000)));
    $pelSubIfdGps->addEntry(new PelEntryAscii(
            PelTag::GPS_LONGITUDE_REF, $longitudeRef));
    $pelSubIfdGps->addEntry(new PelEntryRational(
            PelTag::GPS_LONGITUDE, 
            array($longitudeDegreeMinuteSecond['degree'], 1), 
            array($longitudeDegreeMinuteSecond['minute'], 1), 
            array(round($longitudeDegreeMinuteSecond['second'] * 1000), 1000)));
}

function degreeDecimalToDegreeMinuteSecond($degreeDecimal) {
    $degree = floor($degreeDecimal);
    $remainder = $degreeDecimal - $degree;
    $minute = floor($remainder * 60);
    $remainder = ($remainder * 60) - $minute;
    $second = $remainder * 60;
    return array('degree' => $degree, 'minute' => $minute, 'second' => $second);
}

?>