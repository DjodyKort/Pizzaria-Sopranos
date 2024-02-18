<?php
// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== API (PHP) ====
    public static function checkAccessToken($authToken): void {
        $boolAccessGranted = Functions::isEqual($authToken, ConfigData::$userAPIAccessToken);
        if (!$boolAccessGranted) {
            Functions::setHTTPResponseCode(403);
            Functions::returnJson([
                'error' => 'Invalid access token'
            ]);
            exit();
        }
    }
    public static function checkPostData($postData): void {
        if (empty($postData)) {
            Functions::setHTTPResponseCode(400);
            Functions::returnJson([
                'error' => 'Invalid POST data'
            ]);
            exit();
        }
    }

    # ==== API (HTML/JSON) ====
    public static function sendFormToAPI(string $strAPIURL, string $strAPIAccessToken, array $arrPOSTData): array {
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
            $_SESSION['headerMessage'] = "<div class='alert alert-$color' role='alert'>$message</div>";
        }
        else {
            // ==== Start of Else ====
            # Echo the message
            $_SESSION['headerMessage'] = "<div class='alert alert-danger' role='alert'>Er is iets fout gegaan, probeer het later opnieuw!</div>";
        }
    }

    # ==== JS ====
    static function hidePasswordByName($name): void {
        /** @noinspection JSVoidFunctionReturnValueUsed */
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

    # ==== PHP (efficiency) ====
    public static function addAddressToDB($currentPage, $arrPushedUserData): void {
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/addAddress', ConfigData::$userAPIAccessToken, $arrPushedUserData);

        if ($arrAPIReturn[0] != 200) {
            Functions::echoByStatusCode($arrAPIReturn[0]);
            header("Location: ./userSettings.php?page=$currentPage");
        }
        else {
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Adres is toegevoegd!</div>";

            // Redirecting to the account page
            header("Location: ./userSettings.php?page=addresses");
        }
    }
    public static function deleteAddressFromDB($arrPushedUserData): void {
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/deleteAddress', ConfigData::$userAPIAccessToken, $arrPushedUserData);

        if ($arrAPIReturn[0] != 200) {
            Functions::echoByStatusCode($arrAPIReturn[0]);
        }
        else {
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Adres is verwijderd!</div>";
        }
        // Redirecting to the account page
        header("Location: ./userSettings.php?page=addresses");
    }
    public static function updateAddressInDB($currentPage, $arrPushedUserData): void {
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/updateAddress', ConfigData::$userAPIAccessToken, $arrPushedUserData);

        if ($arrAPIReturn[0] != 200) {
            Functions::echoByStatusCode($arrAPIReturn[0]);
            header("Location: ./userSettings.php?page=addresses");
        }
        else {
            var_dump($arrAPIReturn);
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Adres is gewijzigd!</div>";

            // Redirecting to the account page
            header("Location: ./userSettings.php?page=addresses");
        }
    }


    # ==== PHP ====
    public static function pathToURL($file, $protocol = 'https://'): string {
        // ======== Declaring Variables ========
        # ==== Strings ====
        $documentRoot = realpath(self::dynamicPathFromIndex());
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
        return "{$protocol}{$_SERVER['HTTP_HOST']}{$folderName}/{$relativeUrl}";
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
            $accountButtons = "<a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php'><h4>{$_SESSION['name']}</h4></a>";
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
            <div class='container-fluid'>
                <div class='row'>
                    <!-- Logo -->
                    <div class='col-12 col-md-6 offset-md-3 text-center'>
                        <a href='".self::dynamicPathFromIndex()."index.php'><img class='mw-100' src='".self::dynamicPathFromIndex()."files/images/sopranos-logo.png' height='$logoHeight' alt='Responsive image'></a>
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

    # ==== Accounts page ====
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

    # == Addresses ==
    # Showing addresses
    public static function htmlShowBillingAddresses($arrAddresses): string
    {
        // ======== Checking if empty ========
        if (empty($arrAddresses)) {
            return "<div class='container m-0 mb-3 col-12 col-lg-11 col-md-11'>
                <div class='row border border-black pt-3 pb-3'>
                    <div class='col-6 d-flex align-items-center'>
                        <p class='m-0 ms-1'>Nog niet ingevuld</p>
                    </div>
                    <div class='col-6 d-flex flex-column align-items-end'>
                        <a class='text-decoration-none' href='".Functions::dynamicPathFromIndex()."/files/php/pages/userSettings.php?page=createFAddress'>
                            <button class='btn btn-outline-danger'>Aanmaken</button>
                        </a>
                    </div>
                </div>
            </div>";
        }
        else {
            // ======== Declaring Variables ========
            # Arrays
            $arrAddress = $arrAddresses[0];

            # Int
            $intID = $arrAddress['billingAddressID'];

            # Strings
            $strStreetName = $arrAddress['streetName'];
            $strHouseNumber = $arrAddress['houseNumber'];
            $strHouseNumberAddition = $arrAddress['houseNumberAddition'];
            $strPostalCode = $arrAddress['postalCode'];
            $strCity = $arrAddress['city'];

            // ======== Start of Program ========
            return "<div class='container m-0 mb-3 col-12 col-lg-11 col-md-11'>
                <div class='row border border-black pt-2 pb-2'>
                    <div class='col-9 d-flex align-items-center'>
                        <p class='addressText mw-100 m-0 ms-1'>
                        $strStreetName $strHouseNumber $strHouseNumberAddition<br/>
                        $strPostalCode $strCity
                        </p>
                    </div>
                    <div class='col-3 d-flex flex-column align-items-end'>
                        <div class='text-decoration-none d-flex flex-column'>
                            <a class='btn btn-sm btn-outline-success' href='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=changeBAddress&idAddress=$intID'>Wijzigen</a>
                            <form method='POST' action='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=deleteFAddress'>
                                <input type='hidden' name='idAddress' value='$intID'>
                                <input type='submit' class='btn btn-sm btn-outline-danger mt-2' value='Verwijderen'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }
    public static function htmlShowAddresses($arrAddresses): string
    {
        // ======== Checking if empty ========
        if (empty($arrAddresses)) {
            return "
            <div class='row border border-black pt-4 pb-4'>
                <div class='col-12 d-flex justify-content-center'>
                    <p class='m-0 text-center'>Nog geen adressen toegevoegd</p>
                </div>
            </div>";
        }
        else {
            // ======== Declaring Variables ========
            # Strings
            $string = '';

            // ======== Start of Program ========
            # Adding the start of the div
            $string .= "<div class='row p-0 pe-2 overflow-y-auto' style='height: 210px'>";
            foreach ($arrAddresses as $arrAddress) {
                // ==== Declaring Variables ====
                # Ints
                $intID = $arrAddress['addressID'];

                # Strings
                $strStreetName = $arrAddress['streetName'];
                $strHouseNumber = $arrAddress['houseNumber'];
                $strHouseNumberAddition = $arrAddress['houseNumberAddition'];
                $strPostalCode = $arrAddress['postalCode'];
                $strCity = $arrAddress['city'];

                // ==== Start of Program ====
                $string .= "
                <div class='container m-0 mb-3 col-12 col-lg-12 col-md-12'>
                    <div class='row border border-black pt-2 pb-2'>
                        <div class='col-9 d-flex align-items-center'>
                            <p class='addressText mw-100 m-0 ms-1'>
                            $strStreetName $strHouseNumber $strHouseNumberAddition<br/>
                            $strPostalCode $strCity
                            </p>
                        </div>
                        <div class='col-3 d-flex flex-column align-items-end justify-content-center'>
                            <div class='text-decoration-none d-flex flex-column'>
                                <a class='btn btn-sm btn-outline-success' href='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=changeFAddress&idAddress=$intID'>Wijzigen</a>
                                <form method='POST' action='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=deleteBAddress'>
                                    <input type='hidden' name='idAddress' value='$intID'>
                                    <input type='submit' class='btn btn-sm btn-outline-danger mt-2' value='Verwijderen'>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }

            $string .= '</div>'; // Closing div

            # Return the string
            return $string;
        }
    }

    # Changing addresses
    public static function htmlChangeAddress($strTableName, $strTitle): string
    {
        // ======== Declaring Variables ========
        # ==== Ints ====
        $intID = $_GET['idAddress'] ?? 0;

        # ==== Arrays ====
        # API
        $neededAddressData = [
            'userID' => $_SESSION['userID'],
            $strTableName => $intID
        ];
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/getAddress', ConfigData::$userAPIAccessToken, $neededAddressData);

        # Address
        $arrAddress = $arrAPIReturn[1]['data'][0];

        # ==== Strings ====
        # Static
        $strStreetName = $arrAddress['streetName'];
        $strHouseNumber = $arrAddress['houseNumber'];
        $strHouseNumberAddition = $arrAddress['houseNumberAddition'];
        $strPostalCode = $arrAddress['postalCode'];
        $strCity = $arrAddress['city'];

        // ======== Start of Program ========
        return "
        <div class='container'>
            <div class='row'>
                <h4>$strTitle</h4>
                <div class='container m-0 col-12 col-lg-11 col-md-11'>
                    <form method='POST'>
                        <div class='row'>
                            <div class='col-12 col-lg-6 col-md-6'>
                                <label for='nameStreetName'>Straatnaam: </label>
                                <input class='form-control' type='text' id='idStreetName' name='nameStreetName' value='$strStreetName'>
                                <br/>
                                <label for='nameHouseNumber'>Huisnummer: </label>
                                <input class='form-control' type='text' id='idHouseNumber' name='nameHouseNumber' value='$strHouseNumber'>
                                <br/>
                                <label for='nameHouseNumberAddition'>Huisnummer toevoeging: </label>
                                <input class='form-control' type='text' id='idHouseNumberAddition' name='nameHouseNumberAddition' value='$strHouseNumberAddition'>
                                <br/>
                                <label for='namePostalCode'>Postcode: </label>
                                <input class='form-control' type='text' id='idPostalCode' name='namePostalCode' value='$strPostalCode'>
                                <br/>
                                <label for='nameCity'>Plaats: </label>
                                <input class='form-control' type='text' id='idCity' name='nameCity' value='$strCity'>
                                <br/>
                                <input type='hidden' name='idAddress' value='$intID'>
                                <input class='btn btn-outline-danger' type='submit' value='Wijzigen'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                           
       ";
    }


    public static function htmlAddAddress($strTitle): string {
        return "
        <div class='container'>
            <div class='row'>
                <h4>$strTitle</h4>
                <div class='container m-0 col-12 col-lg-11 col-md-11'>
                    <form method='POST'>
                        <div class='row'>
                            <div class='col-12 col-lg-6 col-md-6'>
                                <label for='nameStreetName'>Straatnaam: </label>
                                <input class='form-control' type='text' id='idStreetName' name='nameStreetName' placeholder='Straatnaam'>
                                <br/>
                                <label for='nameHouseNumber'>Huisnummer: </label>
                                <input class='form-control' type='text' id='idHouseNumber' name='nameHouseNumber' placeholder='Huisnummer'>
                                <br/>
                                <label for='nameHouseNumberAddition'>Huisnummer toevoeging: </label>
                                <input class='form-control' type='text' id='idHouseNumberAddition' name='nameHouseNumberAddition' placeholder='Huisnummer toevoeging'>
                                <br/>
                                <label for='namePostalCode'>Postcode: </label>
                                <input class='form-control' type='text' id='idPostalCode' name='namePostalCode' placeholder='Postcode'>
                                <br/>
                                <label for='nameCity'>Plaats: </label>
                                <input class='form-control' type='text' id='idCity' name='nameCity' placeholder='Plaats'>
                                <br/>
                                <input class='btn btn-outline-danger' type='submit' value='Aanmaken'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        ";
    }
}