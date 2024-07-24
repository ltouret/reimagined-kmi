<?php

//! add return types?
//! change to last php version?
//! removes echos
//! if direcotry doesnt exists crashes -> add func that checks and creates directory at the start if doesnt exist
//! chnage all the quotes to "
//! add setup functions that will init everything needed.

function setupUploads() {
    if (is_dir("uploads/") == false) {
        echo "created", PHP_EOL;
        mkdir("uploads", 0755);
    }
}

//! deleteOlderFile oldest file
// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
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

// ! for now doesnt create direcotry uploads if doesnt exist its broken >:)
function handleFiles($fname, $txt) {
    deleteOlderFile();
    $fname = "uploads/" . $fname;
    $myfile = fopen($fname, "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}

//! do i keep guidv4 or allChars?
function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.'
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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
    // Shuffle the arraysetupUploads();

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
    setupUploads(); //? change this to init function here its called after each curl -> like this if you erase uploads in the middle of the process it wont crash >:)
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