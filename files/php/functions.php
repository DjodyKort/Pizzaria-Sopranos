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
    # Normal functions
    public static function pre($data): void {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    # Global header
    public static function htmlHeader(string $logoHeight): void {
        // ======== Declaring Variables ========
        # Sessions
        session_start();

        # ==== HTML ====
        # Non changing HTML
        $headerMessage = '';

        # Dynamic HTML
        if (isset($_SESSION['headerMessage'])) {
            $headerMessage = "<div class='container-sm'>{$_SESSION['headerMessage']}</div>" ?? '';
            $_SESSION['headerMessage'] = '';
        }
        if(empty($_SESSION['cart'])){
            $_SESSION['cart'] = [];
        }
        if(empty($_SESSION['total'])){
            $_SESSION['total'] = 0;
        }


        if (isset($_SESSION['loggedIn']) and $_SESSION['loggedIn']) {
            $accountButtons = "<a href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php'><h4>{$_SESSION['name']}</h4></a>";
        }
        else {
            $accountButtons = "
            <a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/register.php'><h4 class='me-3 text-muted'>Registreren</h4></a>
            <div class='pt-2 pb-2 align-middle'><div class='vr h-100'></div></div>
            <a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/login.php'><h4 class='ms-3 text-muted'>Inloggen</h4></a>
            ";
        }

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
            <body class='mt-5'>
            <!-- Header Message -->
            $headerMessage
            
            <!-- Navbar -->
            <div class='container-fluid mb-4'>
                <div class='row'>
                    <!-- Logo -->
                    <div class='col-12 col-md-6 offset-md-3 text-center'>
                        <a href='".self::dynamicPathFromIndex()."index.php'><img width='$logoHeight' class='mw-100' src='".self::dynamicPathFromIndex()."files/images/sopranos-logo.png' alt='Responsive image'></a>
                    </div>
                    
                    <!-- Account -->
                    <div class='col-12 col-md-3 text-md-start text-center'>
                        <div class='d-flex justify-content-center justify-content-md-start mt-3'>
                            $accountButtons
                        </div>
                    </div>
                </div>
            </div>
        ");
    }

    # Global footer
    public static function htmlFooter(): void {
        echo("
                <div class='mt-5'></div>
                </body>
            </html>
        ");
    }

    # == Accounts page ==
    # Account navbar
    public static function htmlAccountNavbar(): string {
        // ======== Declaring Variables ========
        # Strings
        $currentPage = $_GET['page'] ?? '';

        // ======== Start of Program ========
        $string = "<div class='container-fluid'><div class='row'><div class='col-12 d-flex justify-content-center'>";

        foreach(ConfigData::$userSettingLinks as $key => $value) {
            // Check if logout
            if ($key == 'logout') {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=logout' class='buttonUserSettings textLogout btn me-2'><p class='mb-0'>$value</p></a>";
                continue;
            }

            // Check if current page
            if ($key == $currentPage or ($key == 'account' and $currentPage == '')) {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=$key' class='buttonUserSettings buttonUserSettingsActive btn me-2'>$value</a>";
            }
            else {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=$key' class='buttonUserSettings btn me-2'>$value</a>";
            }
        }

        $string .= "</div></div></div>";

        return $string;
    }
}