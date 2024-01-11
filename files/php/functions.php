<?php
// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== API (HTML/JSON) ====
    public static function sendFormToAPI(string $strAPIURL, string $strAPIAccessToken, array $arrPOSTData): mixed {
        // ======== Declaring Variables ========
        $curl = curl_init();

        // ======== Start of Program ========
        curl_setopt_array($curl, array(
            CURLOPT_URL => $strAPIURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: ".$strAPIAccessToken,
                "Content-Type: application/x-www-form-urlencoded"
            ),
            CURLOPT_POSTFIELDS => http_build_query($arrPOSTData),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return [curl_getinfo($curl, CURLINFO_HTTP_CODE), (json_decode($response, true) ?? [])];
    }
    public static function isEqual(string $strGivenKey, string $strCorrectKey): bool {
        return $strGivenKey === $strCorrectKey;
    }
    public static function setHTTPResponseCode(int $intCode): void {
        http_response_code($intCode);
    }
    public static function returnJson(array $arrData): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($arrData);
    }
    public static function echoByStatusCode(int $statusCode): void {
        // ======== Start of Program ========
        # Check if the status code is in the ConfigData array
        if (array_key_exists($statusCode, ConfigData::$statusCodes)) {
            // ==== Declaring Variables ====
            $message = ConfigData::$statusCodes[$statusCode][0];
            $color = ConfigData::$statusCodes[$statusCode][1];

            // ==== Start of IF ====
            # Echo the message
            $_SESSION['headerMessage'] = "<div class='alert alert-danger' role='alert'>$message</div>";
        }
        else {
            // ==== Start of Else ====
            # Echo the message
            $_SESSION['headerMessage'] = "<div class='alert alert-danger' role='alert'>Er is iets fout gegaan, probeer het later opnieuw!</div>";
        }
    }

    # ==== JS ====
    static function hidePasswordByName($name): void {
        echo("
            <script>
                // ======== Declaring Variables ========
                let passwordTextField = document.getElementsByName('$name')[0];
                
                // ======== Functions ========
                function showPassword() {
                    if (passwordTextField.type === 'password') {
                        passwordTextField.type = 'text';
                        document.getElementsByName('$name')[0].type = 'text';
                        document.getElementsByClassName('eye')[0].src = '".self::dynamicPathFromIndex()."files/images/eye-slash.svg';
                    }
                    else {
                        passwordTextField.type = 'password';
                        document.getElementsByClassName('eye')[0].src = '".self::dynamicPathFromIndex()."files/images/eye.svg';
                    }
                }
                
                // ======== Start of Function ========
                // Hide password
                passwordTextField.type = 'password';
                
                // Creating a test button
                button = document.getElementsByName('$name')[0].insertAdjacentHTML('afterend', '<img width=\"37px\" src=\"".self::dynamicPathFromIndex()."files/images/eye.svg\" alt=\"Show Password\" class=\"eye\" onclick=\"showPassword()\">');
                // Giving the button a cursor pointer
                document.getElementsByClassName('eye')[0].style.cursor = 'pointer';
                // A bit of margin to the right
                document.getElementsByClassName('eye')[0].style.marginLeft = '14px';
            </script>
        ");
    }

    # ==== PHP ====
    public static function pathToURL($file, $protocol = 'https://'): string {
        // ======== Declaring Variables ========
        # ==== Strings ====
        $documentRoot = realpath(self::dynamicPathFromIndex(__FILE__));
        if (str_contains($_SERVER['HTTP_HOST'], 'localhost')) {
            // Getting the name of folder
            $folderName = '/'.basename($documentRoot);
            $protocol = 'http://';
        }
        else {
            $folderName = '';
        }

        // ======== Start of Program ========
        // Get the absolute path of the file
        $filePath = realpath($file);

        // Get the relative path to the file
        $relativePath = str_replace($documentRoot, '', $filePath);

        // Replace any directory separators with URL separators
        $relativeUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        // Ensure that the relative URL does not start with '/'
        $relativeUrl = ltrim($relativeUrl, '/');

        // Combine the base URL with the relative URL
        $url = "{$protocol}{$_SERVER['HTTP_HOST']}{$folderName}/{$relativeUrl}";
        return $url;
    }

    public static function dynamicPathFromIndex(): string {
        // ======== Declaring Variables ========
        # Strings
        $currentPath = $_SERVER['PHP_SELF'];

        # Ints
        $intSubFromPathDepth = str_contains($_SERVER['HTTP_HOST'], 'localhost') ? 2 : 1;

        // ======== Start of Function ========
        # Checking if the current path is not the index
        $pathSegments = explode('/', $currentPath);
        $filteredSegments = array_filter($pathSegments); // Remove empty segments

        $intPathDepth = count($filteredSegments);

        // If you want the path as a string, you can use implode
        $strPath = str_repeat('../', $intPathDepth - $intSubFromPathDepth);

        if ($strPath == '') {
            // If the current path is the index, return './'
            return './';
        }
        return $strPath;
    }

    # ==== HTML ====
    # Global header
    public static function htmlHeader($logoHeight): void {
        // ======== Declaring Variables ========
        # Sessions
        session_start();

        # Strings
        $_SESSION['headerMessage'] = '';

        # HTML
        $headerMessage = "<div class='container-sm'>{$_SESSION['headerMessage']}</div>" ?? '';

        // ======== Start of Program ========
        # Check if user is logged in or not
        if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
            # Check if on index
            if (!self::dynamicPathFromIndex() == './') {
                header("Location: ".self::dynamicPathFromIndex()."index.php");
            }
        }

        # Body
        echo("
        <!DOCTYPE html>
        <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Pizzaria Sopranos</title>
                
                <!-- CSS Imports -->
                <link rel='stylesheet' href='".self::dynamicPathFromIndex()."files/css/bootstrap.min.css'>
                <link rel='stylesheet' href='".self::dynamicPathFromIndex()."files/css/style.css'>
                
                <!-- JS Imports -->
                <script src='".self::dynamicPathFromIndex()."files/js/jquery-3.7.1.min.js'></script>
                <script src='".self::dynamicPathFromIndex()."files/js/bootstrap.bundle.min.js'></script> 
            </head>
            <body>
            <!-- Header Message -->
            $headerMessage
            
            <!-- Navbar -->
            <nav class='

            </nav>
        ");
    }

    # Global footer
    public static function htmlFooter(): void {
        echo("
                </body>
            </html>
        ");
    }

    # == Accounts page ==
    # Account navbar
    public static function htmlAccountNavbar(): void {
        // ======== Declaring Variables ========
        # Strings
        $currentPage = $_GET['page'] ?? '';

        // ======== Start of Program ========
        echo("
            <table class='table-responsive'>
                <tr> ");
                    foreach (ConfigData::$userSettingLinks as $key => $value) {
                        if ($key == $currentPage) {
                            echo("<td class='pe-3 selected'><a href='?page=$key'>$value</a></td>");
                        }
                        else {
                            echo("<td class='pe-3' ><a href='?page=$key'>$value</a></td>");
                        }
                    } echo("
                </tr>
            </table>
        ");
    }
}