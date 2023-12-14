<?php
// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== API (HTML/JSON) ====
    public static function sendFormToAPI(string $strAPIURL, string $strAPIAccessToken, array $arrPOSTData): bool {
        // ======== Declaring Variables ========
        $curl = curl_init();
        echo($strAPIURL); echo "<br/>";

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
        echo(curl_getinfo($curl, CURLINFO_HTTP_CODE));

        return curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200;
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
    public static function htmlHeader(): void {
        // ======== Declaring Variables ========
        session_start();
        
        // ======== Start of Program ========
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
                <div class='htmlHeader'>
                        <div class='headerDivs'>
                        <a href=".self::dynamicPathFromIndex().">
                            <img src='".self::dynamicPathFromIndex()."files/images/logo.jpg' class='image'>
                        </a>
                        </div>
                        ");

                        if(!isset($_SESSION['loggedIn'])){
                        echo("
                            <div class='headerDivs'>
                                <p class='login'><a href='".self::dynamicPathFromIndex()."files/php/pages/login.php'>Login</a></p>
                                <p class='signUp'><a href='".self::dynamicPathFromIndex()."files/php/pages/register.php'>Registreer</a></p>
                            </div>
                        ");
                        }else if ($_SESSION['loggedIn']){
                            echo("
                            <div class='headerDivs'>
                                <p class='username'><a href=''>". $_SESSION['username'] ."</a></p>
                            </div>
                        ");
                        }
                        echo("
                    </div>
        ");
    }

    public static function htmlFooter(): void {
        echo("
                </body>
            </html>
        ");
    }
}