<?php

//! add return types to funcs?
//! change to last php version docker?
//! removes usless echos
//! chnage all the quotes to "
// ! change name of $fname
//! make this work with this kind of req -> curl -F 'kmi=<-' localhost:8000
//! rename this file and/or cut this into several files?

//? service.php
//! deleteOlderFile oldest file
// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
// ! test on docker too -> and phone what works better?
function deleteOlderFile() {
    $files = glob('uploads/*');
    if (count($files) > 999) {
        // usort($files, function($a, $b) {
        //     return filemtime($b) - filemtime($a);
        // });
        // echo json_encode($files);

        // $files = scandir($directory);

        // Sort files by modified time, latest to earliest
        // Use SORT_ASC in place of SORT_DESC for earliest to latest
        array_multisort(
        array_map( 'filemtime', $files ),
        SORT_NUMERIC,
        SORT_ASC,
        $files
        );

        echo $files[0], PHP_EOL;
        unlink($files[0]);
    }
}

//! instead of using die could use the fwrite STDERR and exit
//! you will check if content is max 512k
// ! status code and echo should be done in controller so in router
function handleFileUpload($content) {
    deleteOlderFile();
    // ! here use content_len isntead of strlen + fix to save utf8 and read utf 8!
    $size = (int) $_SERVER['CONTENT_LENGTH'];
    echo $size;
    if (is_dir("uploads/") == false) {
        // ! if mkdir fails? and remove this echo
        echo "created", PHP_EOL;
        mkdir("uploads", 0755);
    }
    $fname = createUuid();
    $myfile = file_put_contents("uploads/" . $fname, $content);
    if ($myfile == false) {
        // fwrite(2, "An error occurred.\n"); //! test fwrite to STDERR and exit (1)?
        //! add status code 500 somehow
        http_response_code(500);
        die("Error creating file". PHP_EOL);
    }
    return $fname;
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

//? router - (controller).php
//! get /IAmExample will echo the content of the file
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SERVER["REQUEST_URI"] === "/paste") {
    $content = $_POST["kmi"];
    $fname = handleFileUpload($content);
    http_response_code(201);
    //! add here my domain name maybe use .env?
    echo $fname, PHP_EOL;
} else if ($_SERVER["REQUEST_METHOD"] === "GET" && strlen($_SERVER["REQUEST_URI"]) === 11) {
    //! move all of this to a function
    $fname = "uploads/" . substr($_SERVER['REQUEST_URI'], 1);
    $content = file_get_contents($fname);
    // ! would love to find a way to return 404 without going into this else if*
    if ($content == false) { //! This function may return Boolean false, but may also return a non-Boolean value
        http_response_code(404);
        echo "404 Not Found", PHP_EOL;
        return;
    }
    http_response_code(200);
    echo $content, PHP_EOL; //! DONT ADD EOL HERE, remove when done
} else {
    //! return status code of 404 or what else? just 404 or 409? 405??
    http_response_code(404);
    echo "404 Not Found", PHP_EOL;
    return;
}