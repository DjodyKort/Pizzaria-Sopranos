<?php
// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== API (PHP) ====
    public static function checkRolePermission($roleID, $permission): bool {
        // ======== Declaring Variables ========
        # ConfigData
        $permissionKey = ConfigData::$dbKeys['employeeRoles']['permissions'];

        # SQL
        $queryGetRolePermissions = "SELECT $permissionKey FROM ".ConfigData::$dbTables['employeeRoles']." WHERE ".ConfigData::$dbKeys['employeeRoles']['id']." = ?";

        # Arrays
        $arrRolePermissions = PizzariaSopranosDB::pdoSqlReturnArray($queryGetRolePermissions, [$roleID])[0][$permissionKey];

        // ======== Start of Program ========
        # Check if the role has the permission
        return in_array($permission, explode(',', $arrRolePermissions));
    }
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
    public static function toIndexIfLoggedIn(): void {
        if (isset($_SESSION['loggedIn']) and $_SESSION['loggedIn']) {
            header("Location: ".Functions::dynamicPathFromIndex()."index.php");
        }
    }
    public static function toIndexIfNotEmployee(): void {
        if (!isset($_SESSION['role'])) {
            header("Location: ".Functions::dynamicPathFromIndex()."index.php");
        }
    }
    public static function toIndexIfNotUser(): void {
        if ((!isset($_SESSION['loggedIn']) and !$_SESSION['loggedIn']) or isset($_SESSION['role'])) {
            header("Location: ".Functions::dynamicPathFromIndex()."index.php");
        }
    }

    public static function addAddressToDB($currentPage, $arrPushedUserData): void {
        // ======== Declaring Variables ========
        # GET
        $returnTo = $_GET['returnTo'] ?? '';

        // ======== Start of Program ========
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/addAddress', ConfigData::$userAPIAccessToken, $arrPushedUserData);

        if ($arrAPIReturn[0] != 200) {
            Functions::echoByStatusCode($arrAPIReturn[0]);
            header("Location: ./userSettings.php?page=$currentPage");
        }
        else {
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Adres is toegevoegd!</div>";

            // Redirect
            if (!empty($returnTo)) {
                header("Location: ".Functions::dynamicPathFromIndex()."files/php/pages/$returnTo");
            }
            else {
                header("Location: ./userSettings.php?page=addresses");
            }
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
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Adres is gewijzigd!</div>";

            // Redirecting to the account page
            header("Location: ./userSettings.php?page=addresses");
        }
    }

    # ==== PHP ====
    public static function moveFileToFolder($file, $folder): void {
        // ======== Declaring Variables ========
        # Strings
        $strFileName = $file['name'];
        $strFileTmpName = $file['tmp_name'];

        // ======== Start of Program ========
        // Move the file to the folder
        move_uploaded_file($strFileTmpName, $folder.$strFileName);
    }
    public static function replaceExtension($strFileName, $strNewExtension): string {
        // ======== Declaring Variables ========
        # Strings
        $strExtension = pathinfo($strFileName, PATHINFO_EXTENSION);
        $strFileName = str_replace('.'.$strExtension, '', $strFileName);

        // ======== Start of Program ========
        return $strFileName.'.'.$strNewExtension;
    }
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
    # Shopping cart
    public static function makeCartItems(): string {
        // ======== Declaring Variables ========
        $returnString = "";

        // ==== Start of Program ===
        if (isset($_SESSION['cart']) or !empty($_SESSION['cart'])) {
            # Making the shopping cart
            foreach ($_SESSION['cart'] as $cartItemIndex => $cartItem) {
                // ==== Declaring Variables ====
                # == Strings ==
                # Item info
                $strDishID = $cartItem['dishID'];

                # SQL
                $queryGetDishMedia = "SELECT * FROM " . ConfigData::$dbTables['media'] . " WHERE " . ConfigData::$dbKeys['media']['dishID'] . " = ?";
                $queryGetDishSizeData = "SELECT * FROM " . ConfigData::$dbTables['dishSizes'] . " WHERE " . ConfigData::$dbKeys['dishSizes']['id'] . " = ?";
                $queryGetDishSauceData = "SELECT * FROM " . ConfigData::$dbTables['dishSauces'] . " WHERE " . ConfigData::$dbKeys['dishSauces']['id'] . " = ?";

                # == Arrays ==
                $arrDishMedia = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishMedia, [$strDishID]);
                $arrSizeData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSizeData, [$cartItem['size']])[0];
                $arrSauceData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSauceData, [$cartItem['sauce']])[0];

                # == HTML ==
                # Toppings
                $htmlToppings = "";
                foreach ($cartItem['toppings'] as $toppingID => $toppingAmount) {
                    // ==== Declaring Variables ====
                    # == Arrays ==
                    $arrToppingData = PizzariaSopranosDB::pdoSqlReturnArray("SELECT * FROM " . ConfigData::$dbTables['toppings'] . " WHERE " . ConfigData::$dbKeys['toppings']['id'] . " = ?", [$toppingID]);

                    # == Strings ==
                    $strToppingName = $arrToppingData[0][ConfigData::$dbKeys['toppings']['name']];
                    $strToppingPrice = $arrToppingData[0][ConfigData::$dbKeys['toppings']['price']];

                    // ==== Start of Program ===
                    $htmlToppings .= "<p><i>x$toppingAmount</i> $strToppingName - €" . ($toppingAmount * $strToppingPrice) . "</p>";
                }

                // ==== Start of Loop ===
                $returnString .= "
                <div class='row'>
                    <div class='col-12'>
                        <div class='card mb-3'>
                            <div class='card-body'>
                                <h5 class='card-title text-center'>" . $cartItem['name'] . "</h5>
                                <hr/>
                                <div class='container-fluid p-0'>
                                    <div class='row mb-3'>
                                        <div class='col-6'>
                                            <!-- Size -->
                                            <p class='card-text'><strong>Grootte:</strong> " . $arrSizeData[ConfigData::$dbKeys['dishSizes']['name']] . "</p>
                                        </div>
                                        <div class='col-6'>
                                            <!-- Sauce -->
                                            <p class='card-text text-right'><strong>Saus:</strong> " . $arrSauceData[ConfigData::$dbKeys['dishSauces']['name']] . "</p>
                                        </div>
                                    </div>
                                    <div class='row mb-2'>
                                        <!-- Toppings -->
                                        <div class='col-12'>
                                            <p><strong>Toppings:</strong></p>
                                            $htmlToppings
                                        </div>
                                    </div>
                                    <div class='row mb-2'>
                                        <!-- Price -->
                                        <div class='col-12'>
                                            <p class='card-text text-right'><strong>Prijs:</strong> €" . $cartItem['dishTotal'] . "</p>
                                        </div>
                                    </div>
                                </div> <hr/>
                                
                                <!-- Edit & Remove buttons -->
                                <div class='container-fluid'>
                                    <div class='row'>
                                        <div class='col-6'>
                                            <a href='./menu.php?page=" . ConfigData::$mainMenuPages['customizedish'] . "&dishID=$strDishID&cartItemIndex=$cartItemIndex' class='btn btn-primary w-100'>
                                                Aanpassen
                                            </a>
                                        </div>
                                        <div class='col-6'>
                                            <form method='POST' action='./menu.php'>
                                                <input type='hidden' name='removeCartItem' value='$cartItemIndex'>
                                                <input type='submit' class='btn btn-danger w-100' value='Verwijderen'>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
        }

        return $returnString;
    }

    # Global
    public static function htmlNumberPad($strIdInput): string {
        // ======== Declaring Variables ========
        # ==== Strings ====
        # Numberpad info
        $backButtonValue = '⌫';
        $strClearButtonValue = 'C';

        # HTML
        $strHTML = "<div class='container p-0'><div class='row'>";

        # Arrays
        $arrNumbers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        // ======== Start of Program ========
        // Create the number pad with the numbers
        for ($i = 0; $i < 9; $i++) {
            if ($i % 3 == 0) {
                $strHTML .= "</div><div class='row mt-3'>"; // End of row and start of new row with margin-top
            }
            // Add number button with increased font size and padding
            $strHTML .= "<div class='col-4 col-sm-4 col-md-4'><button class='btn btn-primary numberButton w-100 py-2' style='font-size: 1.5em;' onclick='addValueToPasscodeInput(\"$strIdInput\", this.innerHTML)'>{$arrNumbers[$i]}</button></div>";
        }

        // Add the last row with 'Back', '0' and 'Clear' buttons
        $strHTML .= "</div><div class='row mt-3'>
            <div class='col-4 col-sm-4 col-md-4'><button class='btn btn-secondary numberButton w-100 py-2' style='font-size: 1.5em;' onclick='removeLastValueFromPasscodeInput(\"$strIdInput\")'>$backButtonValue</button></div>
            <div class='col-4 col-sm-4 col-md-4'><button class='btn btn-primary numberButton w-100 py-2' style='font-size: 1.5em;' onclick='addValueToPasscodeInput(\"$strIdInput\", this.innerHTML)'>{$arrNumbers[9]}</button></div>
            <div class='col-4 col-sm-4 col-md-4'><button class='btn btn-secondary numberButton w-100 py-2' style='font-size: 1.5em;' onclick='clearPasscodeInput(\"$strIdInput\")'>$strClearButtonValue</button></div>
        </div></div>"; // End of row and container

        // Return the number pad
        return $strHTML;
}

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
        ob_start();
        session_start();

        # ==== Strings ====
        # GET
        $page = $_GET['page'] ?? '';

        # Current uri
        $phpFileName = basename($_SERVER['PHP_SELF']);

        # ==== Arrays ====
        # ConfigData
        $backButtonArrayOrString = ConfigData::backButtonRedirects()[$phpFileName];

        # ==== HTML ====
        # Non changing HTML
        $headerMessage = '';

        # Dynamic HTML
        $htmlBackButton = '';
        if (!empty($page)) {
            # If $backButtonArrayOrString is string
            if (is_array($backButtonArrayOrString)) {
                $backButtonPage = $backButtonArrayOrString[$page];
                if (is_array($backButtonPage)) {
                    $backButtonPage = 'index'; // default to 'index' if the value is an array
                } elseif (strpos($backButtonPage, '/') !== false) {
                    list($phpFileName, $backButtonPage) = explode('/', $backButtonPage);
                    $phpFileName .= '.php'; // append .php extension
                }
            }
            else {
                $backButtonPage = is_string($backButtonArrayOrString) ? $backButtonArrayOrString : 'index';
                if (strpos($backButtonPage, '/') !== false) {
                    list($phpFileName, $backButtonPage) = explode('/', $backButtonPage);
                    $phpFileName .= '.php'; // append .php extension
                }
            }
        }
        else {
            $backButtonPage = is_string($backButtonArrayOrString) ? $backButtonArrayOrString : 'index';
            if (strpos($backButtonPage, '/') !== false) {
                list($phpFileName, $backButtonPage) = explode('/', $backButtonPage);
                $phpFileName .= '.php'; // append .php extension
            }
        }

if ($backButtonPage == 'index') {
    $htmlBackButton = "<a href='".Functions::dynamicPathFromIndex()."' class='text-decoration-none'><h4 class='text-muted'>&lt; Terug</h4></a>";
} else {
    $htmlBackButton = "<a href='".Functions::dynamicPathFromIndex()."files/php/pages/{$phpFileName}.php?page={$backButtonPage}' class='text-decoration-none'><h4 class='text-muted'>&lt; Terug</h4></a>";
}

if ($backButtonPage == 'index') {
    $htmlBackButton = "<a href='".Functions::dynamicPathFromIndex()."' class='text-decoration-none'><h4 class='text-muted'>&lt; Terug</h4></a>";
} else {
    $htmlBackButton = "<a href='".Functions::dynamicPathFromIndex()."files/php/pages/{$phpFileName}?page={$backButtonPage}' class='text-decoration-none'><h4 class='text-muted'>&lt; Terug</h4></a>";
}

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
            // Checking if the user is normal user or employee
            if (isset($_SESSION['role'])) {
                $accountButtons = "<a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/employeePanel.php'><h4>{$_SESSION['name']}</h4></a>";
            }
            else {
                $accountButtons = "<a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/userSettings.php'><h4>{$_SESSION['name']}</h4></a>";
            }
        }
        else {
            $accountButtons = "
            <a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/register.php'><h4 class='me-3 text-muted'>Registreren</h4></a>
            <div class='pt-2 pb-2 align-middle'><div class='vr h-100'></div></div>
            <a class='text-decoration-none' href='".self::dynamicPathFromIndex()."files/php/pages/login.php'><h4 class='ms-3 text-muted'>Inloggen</h4></a>
            ";
        }

        // ======== Start of Program ========
        # Checking if on employeePanel account page or not
        if (!isset($_GET['page']) or $_GET['page'] != ConfigData::$employeePanelPages['account']) {
            # Putting the logged in on false
            $_SESSION['employeePasscodeLoggedIn'] = false;
        }

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
                <div class='row'>
                    <div class='col-12'>
                        <!-- Return button -->
                        $htmlBackButton
                    </div>
                </div>
            </div>
        ");
    }

    # Global footer
    public static function htmlFooter(): void {
        // ======== Start of Program ========
        # Footer echo
        echo("
                <div class='mt-5'></div>
                </body>
            </html>
        ");

        # Sessions
        ob_end_flush();
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
    public static function htmlEmployeeNavbar(): string {
        // ======== Declaring Variables ========
        # Strings
        $currentPage = $_GET['page'] ?? '';

        // ======== Start of Program ========
        $string = "<div class='container-fluid'><div class='row'><div class='col-12 d-flex justify-content-center'>";

        foreach(ConfigData::$employeeSettingLinks as $key => $value) {
            // Check if logout
            if ($key == 'logout') {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/employeePanel.php?page=logout' class='buttonUserSettings textLogout btn me-2'><p class='mb-0'>$value</p></a>";
                continue;
            }

            // Check if current page
            if ($key == $currentPage or ($key == 'account' and $currentPage == '')) {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/employeePanel.php?page=$key' class='buttonUserSettings buttonUserSettingsActive btn me-2'>$value</a>";
            }
            else {
                $string .= "<a href='".self::dynamicPathFromIndex()."files/php/pages/employeePanel.php?page=$key' class='buttonUserSettings btn me-2'>$value</a>";
            }
        }

        $string .= "</div></div></div>";

        return $string;
    }

    # == Dishes ==
    # Adding or changing dishes
    public static function htmlAddOrChangeDishes($strTitle, $strTableName=''): string {
        // ==== Declaring Variables ====
        # == Dynamic variables ==
        if (!empty($strTableName)) {
            # == Strings ==
            # SQL
            $getDishSQL = "SELECT * FROM $strTableName WHERE ".ConfigData::$dbKeys['dishes']['id']." = ?;";
            $getDishDefaultToppingsSQL = "SELECT ".ConfigData::$dbKeys['defaultToppingRelations']['toppingID']." FROM ".ConfigData::$dbTables['defaultToppingRelations']." WHERE ".ConfigData::$dbKeys['defaultToppingRelations']['dishID']." = ?;";
            $getDishMedia = "SELECT ".ConfigData::$dbKeys['media']['fileFolderName'].", ".ConfigData::$dbKeys['media']['fileName']." FROM ".ConfigData::$dbTables['media']." WHERE ".ConfigData::$dbKeys['media']['id']." = ?;";

            # == Arrays ==
            $arrDish = PizzariaSopranosDB::pdoSqlReturnArray($getDishSQL, [$_GET['idDish']])[0];
            $arrDefaultToppingsIds = PizzariaSopranosDB::pdoSqlReturnArray($getDishDefaultToppingsSQL, [$_GET['idDish']]);
            # Making the array with the default toppings
            $arrDefaultToppingsIds = array_column($arrDefaultToppingsIds, ConfigData::$dbKeys['defaultToppingRelations']['toppingID']);

            $arrMediaInfo = PizzariaSopranosDB::pdoSqlReturnArray($getDishMedia, [$arrDish[ConfigData::$dbKeys['dishes']['id']]])[0];

            # == Strings ==
            # Dish information
            $dishID = $arrDish[ConfigData::$dbKeys['dishes']['id']];
            $dishName = $arrDish[ConfigData::$dbKeys['dishes']['name']];
            $dishPrice = $arrDish[ConfigData::$dbKeys['dishes']['price']];
            $dishDiscountPercentage = $arrDish[ConfigData::$dbKeys['dishes']['discountPercentage']];
            $dishSpicyRating = $arrDish[ConfigData::$dbKeys['dishes']['ratingSpicy']];
            $dishMediaFileName = $arrMediaInfo[ConfigData::$dbKeys['media']['fileName']];
            $dishMediaFilePath = Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath.$arrMediaInfo[ConfigData::$dbKeys['media']['fileFolderName']].'/'.$dishMediaFileName;

            # == HTML ==
            # Image HTML
            $htmlImagePreview = "<img class='mb-3' width='400px' id='idImgPreview' src='$dishMediaFilePath' alt='Preview' style='display: block;'/>";
            # Required HTML
            $htmlRequired = '';
        }
        else {
            # == Strings ==
            # Dish information
            $dishID = '';
            $dishName = '';
            $dishPrice = '';
            $dishDiscountPercentage = '';
            $dishSpicyRating = '';
            $dishMediaFileName = '';
            $dishMediaFilePath = '';

            # == Arrays ==
            $arrDefaultToppingsIds = [];

            # == HTML ==
            # Image HTML
            $htmlImagePreview = "<img class='mb-3' width='400px' id='idImgPreview' src='' alt='Preview' style='display: none;'/>";
            # Required HTML
            $htmlRequired = 'required';
        }

        # == Strings ==
        # HTML
        $returnHTML = '';

        # SQL
        $getAllToppingsSQL = "SELECT * FROM ".ConfigData::$dbTables['toppings'].";";

        # == Arrays ==
        # Toppings
        $arrToppings = PizzariaSopranosDB::pdoSqlReturnArray($getAllToppingsSQL);

        # == HTML ==
        # Default topping selector
        $selectDefaultToppingsHTML = "<div class='mb-3'> <h5>Standaard toppings</h5>";
        foreach ($arrToppings as $topping) {
            // ==== Declaring Variables ====
            # == Strings ==
            # Topping information
            $toppingID = $topping[ConfigData::$dbKeys['toppings']['id']];
            $toppingName = $topping[ConfigData::$dbKeys['toppings']['name']];
            $toppingPrice = $topping[ConfigData::$dbKeys['toppings']['price']];

            # HTML
            $htmlToppingSelected = in_array($toppingID, $arrDefaultToppingsIds) ? 'checked' : '';

            // ==== Start of Loop ====
            $selectDefaultToppingsHTML .= "
                <input type='checkbox' class='form-check-input' name='defaultToppings[$toppingID]' id='idDefaultToppings$toppingID' value='$toppingID' $htmlToppingSelected>
                <label class='form-check-label' for='idDefaultToppings$toppingID'>$toppingName</label> <br/>
            ";
        }
        $selectDefaultToppingsHTML .= "</div>";

        // ==== Start of Case ====
        # Making the form to add an item
        $returnHTML .= "
        <div class='container p-0'>
            <div class='row justify-content-center'>
                <div class='col-7 mb-4'>
                    <h4>$strTitle</h4>
                </div>
                <div class='col-7'>
                <form method='POST' enctype='multipart/form-data'>
                    <!-- Dish Name -->
                    <label for='idName' class='form-label'>Item naam</label>
                    <input type='text' class='form-control mb-3' name='nameName' id='idName' value='$dishName' required>
                    
                    <!-- Dish Price -->
                    <label for='idPrice' class='form-label'>Prijs</label>
                    <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='namePrice' id='idPrice' value='$dishPrice' required>
                    
                    <!-- Discount in percentage -->
                    <div class='container p-0'>
                        <div class='row'>
                            <div class='col-6'>
                                <label for='idDiscountPercentage' class='form-label'>Korting in %</label>
                                <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='nameDiscountPercentage' id='idDiscountPercentage' value='$dishDiscountPercentage' required>
                            </div>
                            <div class='col-6'>
                                <label for='idDiscountPrice' class='form-label'>Korting in €</label>
                                <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='nameDiscountPrice' id='idDiscountPrice' $htmlRequired>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Spicy rating (1-5) -->
                    <label for='idSpicyRating' class='form-label'>Pittigheid (1-5)</label><br/>
                    <span id='idSpicyValue'></span>
                    <input type='range' class='form-range mb-3' name='nameSpicyRating' id='idSpicyRating' min='1' max='5' value='$dishSpicyRating' required>
                    
                    <!-- Media picker (Hoofdfoto) -->
                    <label for='idMainMedia' class='form-label'>Hoofdfoto</label>
                    <input type='file' class='form-control mb-3' name='nameMainMedia' id='idMainMedia' $htmlRequired>
                    
                    <!-- Hidden field to store the current media file name -->
                    <input type='hidden' name='currentMediaFileName' value='$dishMediaFileName'>
                    
                    <!-- Image element for preview -->
                    $htmlImagePreview
                    
                    <!-- Selecting the default toppings for the dish -->
                    $selectDefaultToppingsHTML
                    
                    <!-- Submit button -->
                    <button class='btn btn-primary w-100'>Toevoegen</button>
                </form>
                </div>
            </div>
        </div>
        ";

        # Scripts
        $returnHTML .= "<script src='".Functions::dynamicPathFromIndex()."files/js/employeePanel.js'></script>";

        # Return the HTML
        return $returnHTML;
    }

    # == Toppings ==
    public static function htmlAddOrChangeToppings($strTitle, $strTableName=''): string
    {
        // ==== Declaring Variables ====
        # == Dynamic variables ==
        if (!empty($strTableName)) {
            # == Strings ==
            # SQL
            $getToppingSQL = "SELECT * FROM $strTableName WHERE " . ConfigData::$dbKeys['toppings']['id'] . " = ?;";

            # == Arrays ==
            $arrTopping = PizzariaSopranosDB::pdoSqlReturnArray($getToppingSQL, [$_GET['idTopping']])[0];

            # == Strings ==
            # Topping information
            $toppingID = $arrTopping[ConfigData::$dbKeys['toppings']['id']];
            $toppingName = $arrTopping[ConfigData::$dbKeys['toppings']['name']];
            $toppingPrice = $arrTopping[ConfigData::$dbKeys['toppings']['price']];
        }
        else {
            # == Strings ==
            # Topping information
            $toppingID = '';
            $toppingName = '';
            $toppingPrice = '';
        }

        # == Strings ==
        # HTML
        $returnHTML = '';

        # == HTML ==
        # Making the form to add an item
        $returnHTML .= "
        <div class='container p-0'>
            <div class='row justify-content-center'>
                <div class='col-7 mb-4'>
                    <h4>$strTitle</h4>
                </div>
                <div class='col-7'>
                <form method='POST'>
                    <!-- Topping Name -->
                    <label for='idName' class='form-label'>Topping naam</label>
                    <input type='text' class='form-control mb-3' name='nameName' id='idName' value='$toppingName' required>
                    
                    <!-- Topping Price -->
                    <label for='idPrice' class='form-label'>Prijs</label>
                    <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='namePrice' id='idPrice' value='$toppingPrice' required>
                    
                    <!-- Submit button -->
                    <button class='btn btn-primary w-100'>Toevogen</button>
                </form>
                </div>
            </div>
        </div>
        ";

        # Return the HTML
        return $returnHTML;
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
                        <a class='text-decoration-none' href='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=createFAddress'>
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


            //get data from the billingAddress
            $query = "SELECT * FROM ".ConfigData::$dbTables['billingAddresses']." WHERE userID = ".$_SESSION['userID']."";
            $billingAddress = PizzariaSopranosDB::pdoSqlReturnArray($query);

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

                $addAsBillingAddressButton = "";
                
                //check if query wasnt empty
                if(!empty($billingAddress)){
                    // Check if the billing address is the same as the current address in the loop
                    if ($billingAddress[0]['streetName'] == $strStreetName &&
                    $billingAddress[0]['houseNumber'] == $strHouseNumber &&
                    $billingAddress[0]['houseNumberAddition'] == $strHouseNumberAddition &&
                    $billingAddress[0]['postalCode'] == $strPostalCode &&
                    $billingAddress[0]['city'] == $strCity) {
                    }else{
                        $addAsBillingAddressButton .= "  
                        <input type='submit' class='btn btn-sm btn-outline-warning mt-2' value='Factuuradres'>
                        <input type='hidden' name='streetName' value='$strStreetName'>
                        <input type='hidden' name='houseNumber' value='$strHouseNumber'>
                        <input type='hidden' name='houseNumberAddition' value='$strHouseNumberAddition'>
                        <input type='hidden' name='postalCode' value='$strPostalCode'>
                        <input type='hidden' name='city' value='$strCity'>
                        "; 
                    }
                }
                

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
                                <form method='POST' action='".Functions::dynamicPathFromIndex()."files/php/pages/userSettings.php?page=updateFAddress'>
                                    $addAsBillingAddressButton
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

    # Adding or changing addresses
    public static function htmlAddOrChangeAddress($strTitle, $strButtonSubmitName, $strTableName=''): string {
        // ======== Declaring Variables ========
        if (!empty($strTableName)) {
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
        }
        else {
            $intID = 0;
            $strStreetName = '';
            $strHouseNumber = '';
            $strHouseNumberAddition = '';
            $strPostalCode = '';
            $strCity = '';
        }

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
                                <input class='btn btn-outline-danger' type='submit' value='$strButtonSubmitName'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                           
       ";
    }
}