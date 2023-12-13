<?php
// ============ Imports ============

// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== API (HTML/JSON) ====
    public static function sendFormToAPI(string $strAPIURL, string $strAPIAccessToken, array $arrPOSTData): bool {
        // ======== Imports ========
        require_once($_SERVER["DOCUMENT_ROOT"].'/files/php/classes.php');

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

        return curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200;
    }
    public static function checkAuthentication(string $strGivenKey, string $strCorrectKey): bool {
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
    public static function pathToURL($file, $protocol='https://'): string {
        // ======== Declaring Variables ========
        $projectRoot = '/'.explode('/', $_SERVER['PHP_SELF'])[1];

        if (str_contains($_SERVER['HTTP_HOST'], 'localhost')) {
            $protocol = 'http://';
            $projectRoot = $protocol . $_SERVER['HTTP_HOST'] . $projectRoot;
        } else {
            $projectRoot = $protocol . $_SERVER['HTTP_HOST'];
        }

        // ======== Start of Program ========
        // Get the absolute path of the file
        $filePath = realpath($file);

        // Get the document root
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);

        // Get the relative path to the file
        $relativePath = str_replace($documentRoot, '', $filePath);

        // Replace any directory separators with URL separators
        $relativeUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        // Combine the base URL with the relative URL
        $url = $projectRoot . $relativeUrl;

        return $url;
    }
    public static function pathUntilIndex(): string{
        // ======== Declaring Variables ========
        $currentPath = $_SERVER['PHP_SELF'];

        // ======== Start of Function ========
        # Check if index.php is in the path
        if (str_contains($currentPath, 'index.php')) {
            $strPath = './';
        }
        else {
            # Get the amount of slashes in the path
            $intSlashCount = substr_count($currentPath, '/');
            if (str_contains($_SERVER['HTTP_HOST'], 'localhost')) {
                # Create the path
                $strPath = str_repeat('../', $intSlashCount-2);
            } else {
                # Create the path
                $strPath = str_repeat('../', $intSlashCount-1);
            }
        }
        return($strPath);
    }

    # ==== HTML ====
    public static function htmlHeader(): void {
        // ======== Declaring Variables ========

        // ======== Start of Program ========
        echo("
        <!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Pizzaria Sopranos</title>
                    
                    <!-- CSS Imports -->
                    <link rel='stylesheet' href='".self::pathUntilIndex()."files/css/bootstrap.min.css'>
                    <link rel='stylesheet' href='".self::pathUntilIndex()."files/css/style.css'>
                    
                    <!-- JS Imports -->
                    <script src='".self::pathUntilIndex()."files/js/jquery-3.7.1.min.js'></script>
                    <script src='".self::pathUntilIndex()."files/js/bootstrap.bundle.min.js'></script> 
                </head>
                <body>
        ");
    }

    public static function htmlFooter(): void {
        echo("
                </body>
            </html>
        ");
    }
}