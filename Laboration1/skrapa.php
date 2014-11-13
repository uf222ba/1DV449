<?php
/**
 * Student Id:  uf222ba
 * Name:        Ulrika Falk
 * Mail:        uf222ba@student.lnu.se
 * Date:        2014-11-11
 * Laboration:  Laboration 1, Webbteknik II
 */
include('simplehtmldom_1_5/simple_html_dom.php');
require_once("Course.php");

main();

function main() {
    curl_login();
    //curl_get_data("https://coursepress.lnu.se/kurser/?bpage=4");

    echo '<a href="skrapa_utaninlogg.php">H&auml;mta senaste datat fr&aring;n Coursepress</a></br></br>';
    echo '<a href="coursepress.json">Visa JSON-filen</a></br></br></br>';

// Kolla om det har gått fem minuter från senaste körningen
    $waitTime = 300; // 5 minuter
    $infoArr = getInfoFromJsonFile();
    if(isset($infoArr)) {
        echo "Det finns " . $infoArr["numofcourses"] . " kurser sparade</br></br>";

        if ($infoArr["timediff"] < $waitTime) {
            echo "Det m&aring;ste g&aring; mer &auml;n 5 minuter (300 sekunder) innan en ny k&ouml;rning kan g&ouml;ras!</br>";
            echo "Det har g&aring;tt " . $infoArr["timediff"] . " sekunder sedan den senaste k&ouml;rningen.</br>";
            die();
        }
    }

    $startPageUrl = 'https://coursepress.lnu.se';
    //$pageUrl = 'https://coursepress.lnu.se/kurser/';
    //https://coursepress.lnu.se/kurser/?bpage=2
    $pageUrl = 'https://coursepress.lnu.se/kurser/?bpage=4';
    $courseObjects = array();

    while ($pageUrl) {
        //$html = file_get_html($pageUrl);
        $html = str_get_html(curl_get_data($pageUrl));
        if(isset($html)){
            try {
                foreach ($html->find('div[class="item-title"]') as $div) {
                    foreach ($div->find('a[href^="https://coursepress.lnu.se/kurs/"]') as $a) {

                        $o = new Course(fixString($a->innertext), $a->href);
                        $courseObjects[] = $o;
                    }
                }
            } catch (Exception $e) {
                echo "Error: " . $e;
            }
        }

        $pageUrl = $html->find('a[class="next page-numbers"]', 0)->href;
        if (strlen($pageUrl) > 0) {
            $pageUrl = $startPageUrl . $pageUrl;
        } else {
            $pageUrl = FALSE;
        }
    }

    foreach ($courseObjects as $o) {
        setCourseInfo($o);
    }
    writeToJsonFile($courseObjects);

    $html->clear();
    unset($html);
}

function setCourseInfo(Course $o) {
    //$courseHtml = file_get_html($o->getCourseUrl());
    $courseHtml = str_get_html(curl_get_data($o->getCourseUrl()));

    $coursePlanUrl = $courseHtml->find('a[href^="http://prod.kursinfo.lnu.se/utbildning/GenerateDocument.ashx"]', 0)->href;
    $o->setCoursePlanUrl(fixString($coursePlanUrl));
    //echo "Kursplan: " . $o->getCoursePlanUrl() . "</br>";

    $courseCode = $courseHtml->find('a[title="HT14"]', 0)->plaintext;
    $o->setCourseCode(fixString($courseCode));
    //echo "Kurskod: " . $o->getCourseCode() . "</br>";

    $ingress = $courseHtml->find('div[class="entry-content"] p', 0)->plaintext;
    $o->setIngress(fixString($ingress));
    //echo "Ingress: " . $o->getIngress() . "</br>";

    $latestPostHeader = $courseHtml->find('h1[class="entry-title"]', 0)->plaintext;
    $o->setLatestPostHeader(fixString($latestPostHeader));
    //echo "LatestHeader: " . $o->getLatestPostHeader() . "</br>";

    $author = $courseHtml->find('p[class="entry-byline"] strong', 0)->plaintext;
    $o->setLatestPostAuthor(fixString($author));
    //echo "Author: " . $o->getLatestPostAuthor() . "</br>";

    $dateTime = $courseHtml->find('p[class="entry-byline"]', 0)->plaintext;
    $arr = explode(" ", trim($dateTime));
    $dateTime = $arr[1] . " " . $arr[2];
    $o->setPostPublished(fixString($dateTime));
    //echo "Datum och tid: " . $o->getPostPublished() . "</br>";

    $courseHtml->clear();
    unset($courseHtml);
}

function fixString($str) {
    $str = str_replace("\r", "", $str);
    $str = str_replace("\n", " ", $str);
    if(strlen(trim($str)) <= 0)
    {
        return "no information";
    }
    return utf8_decode($str);
}

function writeToJsonFile($arrOfObjects) {
    $file = fopen("coursepress.json", "w") or die("Unable to open file!");
    $count = 0;
    $comma = ",";

    fwrite($file, '{"timestamp":"' .time() . '", "numberofcourses":"' . count($arrOfObjects) . '", "coursepress":[' . "\n");
    //skriv in alla objekten
    foreach ($arrOfObjects as $o) {
        $count++;
        if (count($arrOfObjects) == $count)
            $comma = "";

        fwrite($file, '{');
        fwrite($file, '"courseName":"' . $o->getCourseTitle() . '", ');
        fwrite($file, '"courseUrl":"' . $o->getCourseUrl() . '", ');
        fwrite($file, '"courseCode":"' . $o->getCourseCode() . '", ');
        fwrite($file, '"coursePlanUrl":"' . $o->getCoursePlanUrl() . '", ');
        fwrite($file, '"ingress":"' . $o->getIngress() . '", ');
        fwrite($file, '"latestPostHeader":"' . $o->getLatestPostHeader() . '", ');
        fwrite($file, '"latestPostAuthor":"' . $o->getLatestPostAuthor() . '", ');
        fwrite($file, '"postPublished":"' . $o->getPostPublished() . '"}' . $comma . "\n");
    }
    fwrite($file, ']}');
    fclose($file);
}

function getInfoFromJsonFile() {
    $jsonData = @file_get_contents("coursepress.json");
    if ($jsonData === false) {
        $timeDiff = 500;
        $numOfCourses = 0;
    }
    else {
        $arr = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $jsonData), true);
        $timeStamp = $arr["timestamp"];
        $timeDiff = (time() - $timeStamp);

        $numOfCourses = $arr["numberofcourses"];
    }
    $info = array("timediff" => $timeDiff, "numofcourses" => $numOfCourses);
    return $info;
}

function curl_login() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://coursepress.lnu.se/kurser/wp-login.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $post_arr = array(
        "log" => "xxx",
        "pwd" => "xxx"
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_arr);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'uf222ba');

    // curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
    $data = curl_exec($ch);
    curl_close($ch);
}

function curl_get_data($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'uf222ba');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, "cookie.txt");
    $data = curl_exec($ch);
    //var_dump($data);
    curl_close($ch);
    return $data;
}
