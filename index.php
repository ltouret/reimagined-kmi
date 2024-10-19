<?php
//? services - (model)
function deleteOlderFile() {
    $files = glob("uploads/*");
    if (count($files) > 1000) {
        array_multisort(
        array_map( "filemtime", $files ),
        SORT_NUMERIC,
        SORT_ASC,
        $files
        );

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
    $path = "uploads/" . $fileName;
    $myfile = @file_put_contents($path, $content);
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

function handleError($code, $message) {
    http_response_code($code);
    echo $message, PHP_EOL;
}

function handleTerminalResponse() {
    $reset = "\033[0m";
    $bold = "\033[1m";
    $underline = "\033[4m";
    $italic = "\033[3m";
    $cyan = "\033[96m";
    $green = "\033[92m";
    $yellow = "\033[93m";

    echo <<<EOT
    {$bold}NAME{$reset}
        {$cyan}reimagined-kmi{$reset} - a pure PHP Implementation of command line pastebin, anonymous, fast

    {$bold}SYNOPSIS{$reset}
        {$underline}<command>{$reset} | curl -F 'kmi=<-' {$yellow}{$domain}{$reset}

    {$bold}DESCRIPTION{$reset}
        Reimagined-KMI is a project developed in pure PHP, aiming to replicate the functionality of a command line pastebin service.
        It enables users to quickly share text snippets without the need for registration.

        {$bold}Features:{$reset}
        - {$green}Anonymous posting:{$reset} No registration required to share your snippets.

    {$bold}LIMITS{$reset}
        - Storage time: Unlimited, but data may be pruned at any time.
        - Maximum post size: Limited to 512KB.

    {$bold}EXAMPLES{$reset}
        To upload a file named `hello-world.c`:
        {$underline}~$ cat hello-world.c | curl -F 'kmi=<-' {$yellow}{$domain}{$reset}
        Output: {$yellow}{$domain}IAmExample{$reset}

        To view the uploaded snippet with curl:
        {$underline}~$ curl {$yellow}{$domain}IAmExample{$reset}

    EOT;
}

header("Content-Type: text/plain");

//? router - (controller)
switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if ($_SERVER["REQUEST_URI"] === "/") {
            $domain = getenv("DOMAIN");
            http_response_code(200);
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false) {
                $reset = "\033[0m";
                $bold = "\033[1m";
                $underline = "\033[4m";
                $italic = "\033[3m";
                $cyan = "\033[96m";
                $green = "\033[92m";
                $yellow = "\033[93m";

                echo <<<EOT
                {$bold}NAME{$reset}
                    {$cyan}reimagined-kmi{$reset} - a pure PHP Implementation of command line pastebin, anonymous, fast

                {$bold}SYNOPSIS{$reset}
                    {$underline}<command>{$reset} | curl -F 'kmi=<-' {$yellow}{$domain}{$reset}

                {$bold}DESCRIPTION{$reset}
                    Reimagined-KMI is a project developed in pure PHP, aiming to replicate the functionality of a command line pastebin service.
                    It enables users to quickly share text snippets without the need for registration.

                    {$bold}Features:{$reset}
                    - {$green}Anonymous posting:{$reset} No registration required to share your snippets.

                {$bold}LIMITS{$reset}
                    - Storage time: Unlimited, but data may be pruned at any time.
                    - Maximum post size: Limited to 512KB.

                {$bold}EXAMPLES{$reset}
                    To upload a file named `hello-world.c`:
                    {$underline}~$ cat hello-world.c | curl -F 'kmi=<-' {$yellow}{$domain}{$reset}
                    Output: {$yellow}{$domain}IAmExample{$reset}

                    To view the uploaded snippet with curl:
                    {$underline}~$ curl {$yellow}{$domain}IAmExample{$reset}

                EOT;
            } else {
                echo <<<EOT
                NAME
                    reimagined-kmi - a pure PHP Implementation of command line pastebin, anonymous, fast

                SYNOPSIS
                    <command> | curl -F 'kmi=<-' {$domain}

                DESCRIPTION
                    Reimagined-KMI is a project developed in pure PHP, aiming to replicate the functionality of a command line pastebin service.
                    It enables users to quickly share text snippets without the need for registration.

                    Features:
                    - Anonymous posting: No registration required to share your snippets.

                LIMITS
                    - Storage time: Unlimited, but data may be pruned at any time.
                    - Maximum post size: Limited to 512KB.

                EXAMPLES
                    To upload a file named `hello-world.c`:
                    ~$ cat hello-world.c | curl -F 'kmi=<-' {$domain}
                    Output: {$domain}IAmExample

                    To view the uploaded snippet with curl:
                    ~$ curl {$domain}IAmExample

                EOT;
            }
        } else {
            try {
                $fileName = $_SERVER["REQUEST_URI"];
                $content = serveFileFromUri($fileName);
                http_response_code(200);
                echo $content;
            } catch (Exception $e) {
                handleError($e->getCode(), $e->getMessage());
            }
        }
        break;

    case "POST":
        if ($_SERVER["REQUEST_URI"] === "/") {
            try {
                $domain = getenv("DOMAIN");
                $content = $_POST["kmi"];
                $fileUri = $domain . handleFileUpload($content);
                http_response_code(201);
                echo $fileUri, PHP_EOL;
            } catch (Exception $e) {
                handleError($e->getCode(), $e->getMessage());
            }
        } else {
            handleError(400, "Bad Request");
        }
        break;

    default:
        handleError(405, "Method Not Allowed");
        break;
}
?>