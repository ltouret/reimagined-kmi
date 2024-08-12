<?php

//! add return types to funcs?
//! change to last php version docker?
//! removes usless echos
//! chnage all the quotes to "
//! rename this file and/or cut this into several files?
//! ($_SERVER["REQUEST_URI"] === "/last") { //? this must be removed made to debug ***

//! ERASE ME WHEN DONE ****
function printLatestFile() {
    $files = glob("uploads/*");
        array_multisort(
        array_map( "filemtime", $files ),
        SORT_NUMERIC,
        SORT_DESC,
        $files
        );
        $content = file_get_contents($files[0]);
        // ! would love to find a way to return 404 without going into this else if*
        if ($content == false) { //! This function may return Boolean false, but may also return a non-Boolean value
            http_response_code(404);
            echo "404 Not Found", PHP_EOL;
            return;
        }
        http_response_code(200);
        echo $content, PHP_EOL; //! DONT ADD EOL HERE, remove when done
}

//? service.php
//! deleteOlderFile oldest file
// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
// ! test on docker too -> and phone what works better?
//! dont delete IAmExample >:) -> so we have a max of 1001 files
function deleteOlderFile() {
    $files = glob("uploads/*");
    if (count($files) > 1000) {
        // usort($files, function($a, $b) {
        //     return filemtime($b) - filemtime($a);
        // });
        // echo json_encode($files);

        // $files = scandir($directory);

        // Sort files by modified time, latest to earliest
        // Use SORT_ASC in place of SORT_DESC for earliest to latest
        array_multisort(
        array_map( "filemtime", $files ),
        SORT_NUMERIC,
        SORT_ASC,
        $files
        );
        // echo json_encode($files);
        // print_r($files);

        //! remove second oldest file as first one is IAmExample 
        echo $files[1], PHP_EOL; //! remove me
        unlink($files[1]);
    }
}

function handleFileUpload($content) {
    deleteOlderFile();
    $content_size = strlen($content);
    echo $content_size, PHP_EOL;
    if ($content_size > 512000) { //! Change max to 512kb and min 1?
        throw new Exception("Max size is 512Kb");
    }
    if ($content_size === 0) {
        //! change to something funny? -> header('Location: '.$newURL); to IAmExample
        // throw new Exception("Failed to create directory");
        $content = ""; // what to do hereeeeee? -> kmi just redirects to a random file
    }
    if (is_dir("uploads/") == false) {
        if (!mkdir("uploads/", 0755)) {
            throw new Exception("Failed to create directory");
        }
    }
    $fileName = createUuid();
    $myfile = file_put_contents("uploads/" . $fileName, $content);
    if ($myfile == false) {
        throw new Exception("Error creating file");
    }
    return $fileName;
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


// echo $_SERVER["REQUEST_METHOD"], " ", $_SERVER["REQUEST_URI"], PHP_EOL;
//? all reponses are text plain
header('Content-Type: text/plain');

//? router - (controller).php
//! get /IAmExample will echo the content of the file
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SERVER["REQUEST_URI"] === "/paste") {
    try {
        $content = $_POST["kmi"];
        $fileName = handleFileUpload($content);
        http_response_code(201);
        //! add here my domain name maybe use .env?
        echo $fileName, PHP_EOL;
    } catch (Exception $e) {
        switch ($e->getMessage()) {
            case "Max size is 512Kb":
                http_response_code(413); // Payload Too Large
                break;
            default:
                http_response_code(500); // Default to Internal Server Error for unknown issues
                break;
        }
        echo $e->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] === "GET" && mb_strlen($_SERVER["REQUEST_URI"]) === 11) {
    //! move all of this to a function
    $fileName = "uploads" . $_SERVER["REQUEST_URI"];
    $content = file_get_contents($fileName);
    // ! would love to find a way to return 404 without going into this else if*
    if ($content == false) { //! This function may return Boolean false, but may also return a non-Boolean value
        http_response_code(404);
        echo "404 Not Found", PHP_EOL;
        return;
    }
    http_response_code(200);
    echo $content, PHP_EOL; //! DONT ADD EOL HERE, remove when done 
} else if ($_SERVER["REQUEST_URI"] === "/last") { //? this must be removed made to debug ***
    printLatestFile(); //! REMOVE THIS
} else {
    //! return status code of 404 or what else? just 404 or 409? 405??
    http_response_code(404);
    echo "404 Not Found", PHP_EOL;
    return;
}