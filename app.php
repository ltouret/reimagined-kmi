<?php

//! add return types to funcs?
//! change to last php version docker?
//! removes usless echos
//! chnage all the quotes to "
//! rename this file and/or cut this into several files?
//! ($_SERVER["REQUEST_URI"] === "/last") { //? this must be removed made to debug ***
//! add real domain in POST echo using .env in docker and for phone in a bash file

//? service.php
// ! test on docker too -> and phone what works better?
function deleteOlderFile() {
    $files = glob("uploads/*");
    if (count($files) > 1000) {
        array_multisort(
        array_map( "filemtime", $files ),
        SORT_NUMERIC,
        SORT_ASC,
        $files
        );

        //! remove second oldest file as first one is IAmExample 
        unlink($files[1]);
    }
}

function createUuid() {
    $allChars = [
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M",
        "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m",
        "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
    ];      
    shuffle($allChars);

    $uuid = "";
    for ($x = 0; $x < 10; $x++) {
        $uuid .= $allChars[$x];
    }
    return $uuid;
}

function handleFileUpload($content) {
    deleteOlderFile();
    $content_size = strlen($content);
    if ($content_size > 512000) {
        throw new Exception("Max size is 512Kb", 413);
    }
    if ($content_size === 0) {
        throw new Exception("The KMI god expects a string, without a string he cant do magic!", 400);
    }
    if (is_dir("uploads/") === false) {
        if (!mkdir("uploads/", 0755)) {
            throw new Exception("Failed to create directory", 500);
        }
    }
    $fileName = createUuid();
    $myfile = @file_put_contents("uploads/" . $fileName, $content);
    if ($myfile == false) {
        throw new Exception("Error creating file", 500);
    }
    return $fileName;
}

function serveFileFromUri($fileName) {
    $fileName = "uploads" . $fileName;
    $content = @file_get_contents($fileName);
    if ($content == false) {
        throw new Exception("File not found", 404);
    }
    return $content;
}

//? all reponses are text plain
header("Content-Type: text/plain");
echo PHP_EOL;

//? router - (controller).php
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SERVER["REQUEST_URI"] === "/paste") {
    try {
        $domain = getenv("DOMAIN");
        $content = $_POST["kmi"];
        $userLink = $domain . handleFileUpload($content);
        http_response_code(201);
        echo $userLink, PHP_EOL;
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo $e->getMessage(), PHP_EOL;
    }
} else if ($_SERVER["REQUEST_METHOD"] === "GET" && mb_strlen($_SERVER["REQUEST_URI"]) === 11) {
    try {
        $fileName = $_SERVER["REQUEST_URI"];
        $content = serveFileFromUri($fileName);
        http_response_code(200);
        echo $content;
    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo $e->getMessage(), PHP_EOL;
    }
} else if ($_SERVER["REQUEST_URI"] === "/teapot") {
        http_response_code(418);
        echo "I'm a teapot", PHP_EOL;
} else {
    //! return status code of 404 or what else? just 404 or 409? 405??
    http_response_code(404);
    echo "File Not Found", PHP_EOL;
}