<?php

require_once "./config.php";

//! add return types?
//! change to last php version?
//! removes echos
//! if direcotry doesnt exists crashes -> add func that checks and creates directory at the start if doesnt exist
//! chnage all the quotes to "
//! add setup functions that will init everything needed.

setupUploads();

function setupUploads() {
    echo "created", PHP_EOL;
    if (is_dir("uploads/") == false) {
        echo "created", PHP_EOL;
        mkdir("uploads", 0755);
    }
}

//! deleteOlderFile oldest file
// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
function deleteOlderFile() {
    $files = glob('uploads/*');
    // echo json_encode($files);
    if (count($files) > 999) {
        // usort($files, function($a, $b) {
        //     return filemtime($b) - filemtime($a);
        // });

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

// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
function handleFiles($fname, $txt) {
    deleteOlderFile();
    $fname = "uploads/" . $fname;
    $myfile = fopen($fname, "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}

//! do i keep test or allChars?
function createUuid () {
    $uppercase = range('A', 'Z');
    $lowercase = range('a', 'z');
    $numbers = range(0, 9);

    $allChars = array_merge($uppercase, $lowercase, $numbers);
    $test = [
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M",
        "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m",
        "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
    ];      

    // Print the array (optional)
    // echo json_encode($test);
    // Shuffle the array();

    shuffle($allChars);

    // Create a string with all characters in the array
    $uuid = '';
    for ($x = 0; $x < 10; $x++) {
        $uuid .= $allChars[$x];
    }
    // echo $uuid, PHP_EOL;
    return $uuid;
}

// Define a function to handle the root route ("/")
function handleRootRoute() {
    $fname = createUuid();
    handleFiles($fname, "test file lol"); // <- receive from the user and save it in uploads folder
    echo "Hello W! You just uploaded a file from your vanilla PHP API! ", $fname, PHP_EOL;
}

// Register the route with a simple if-else statement -> add get here, and post here
//! get /IAmExample will echo the content of the file
if ($_SERVER['REQUEST_URI'] === '/') {
    handleRootRoute();
} else {
    // Handle other routes (optional)
    // You can add additional if-else or switch statements to handle
    // other routes based on the request URI.
    echo "404 Not Found", PHP_EOL;
    return;
}
?>