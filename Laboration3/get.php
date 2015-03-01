<?php
/**
 * Student Id:  uf222ba  
 * Name:        Ulrika Falk
 * Mail:        uf222ba@student.lnu.se 
 * Date:        2015-02-16
 * Laboration:  Laboration 3, Webbteknik II 1DV449
 */
$url = "http://api.sr.se/api/v2/traffic/messages?format=json&pagination=false";
$localPath = "data/events.json";
$timeFile = "data/timestamp.txt";

$maxTime = 300; // 5 minuter
$timeDiff = (time() - $timestamp);
$timestamp = file_get_contents($timeFile);
try {
    if($timeDiff > $maxTime) {
        $trafficEventsFromSR = @file_get_contents($url);
        if($json === FALSE) {
            throw new Exception("Unable to get traffic events from SR!");
        } else {
            file_put_contents($localPath, $trafficEventsFromSR);
            file_put_contents($timeFile, time());
        }
    }
} catch(Exception $e) {
    echo $e->getMessage();
}

try {
    $localTrafficEvents = @file_get_contents($localPath);
    if($localTrafficEvents === FALSE)
        throw new Exception("Error: local file doesn't contain any traffic events");
} catch(Exception $e) {
    echo $e->getMessage();
}

echo($localTrafficEvents);
