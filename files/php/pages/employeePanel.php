<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Header
Functions::htmlHeader(340);

# Logout button
if (isset($_GET['page'])){
    if ($_GET['page'] == 'logout') {
        session_destroy();
        header("Location: ".Functions::dynamicPathFromIndex()."index.php");
    }
}

# Dynamic POST Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ======== Declaring Variables ========

    // ======== Start of POST Request ========
    switch ($currentPage) {
        # Form pages (deleting the data)
        case ConfigData::$employeePanelPages['menu']:
            // ==== Declaring Variables ====
            # == Strings ==
            # POST
            $_POST['deleteDishID'] = $_POST['deleteDishID'] ?? '';

            # API
            $arrPushedDishData = [
                'roleID' => $_SESSION['role'],
                ConfigData::$dbKeys[ConfigData::$dbTables['dishes']]['id'] => $_POST['deleteDishID'],
            ];

            // ==== Start of Case ====
            # Processing the POST request and media via the api
            $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/deleteDish', ConfigData::$userAPIAccessToken, $arrPushedDishData);
            if ($arrAPIReturn[0] != 200) {
                Functions::echoByStatusCode($arrAPIReturn[0]);
                header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['menu']."");
            }
            else {
                # Header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Item is verwijderd!</div>";

                # Redirecting to the menu page
                header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['menu']."");
            }
            break;

        # Form pages (updating the data)
        case ConfigData::$employeePanelPages['additem']:
            // ==== Declaring Variables ====
            # == Strings ==
            # ConfigData strings
            $employeeID = ConfigData::$dbKeys['employeeUsers']['id'];
            $roleID = ConfigData::$dbKeys['employeeUsers']['roleID'];
            $strTableName = ConfigData::$dbTables['dishes'];

            # POST
            $_POST['nameName'] = $_POST['nameName'] ?? '';
            $_POST['namePrice'] = $_POST['namePrice'] ?? '';
            $_POST['nameDiscountPercentage'] = $_POST['nameDiscountPercentage'] ?? '';
            $_POST['nameDiscountPrice'] = $_POST['nameDiscountPrice'] ?? '';
            $_POST['nameSpicyRating'] = $_POST['nameSpicyRating'] ?? '';

            $_POST['defaultToppings'] = $_POST['defaultToppings'] ?? '';

            # Media
            $tempPath = $_FILES['nameMainMedia']['tmp_name'];
            $filePath = Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath.$_POST['nameName'];

            # Arrays
            $arrPushedDishData = [
                $employeeID => $_SESSION[$employeeID],
                $roleID => $_SESSION['role'],
                ConfigData::$dbTables['defaultToppingRelations'] => $_POST['defaultToppings'],
                ConfigData::$dbTables['dishes'] => [
                    ConfigData::$dbKeys[$strTableName]['name'] => $_POST['nameName'],
                    ConfigData::$dbKeys[$strTableName]['price'] => $_POST['namePrice'],
                    ConfigData::$dbKeys[$strTableName]['discountPercentage'] => $_POST['nameDiscountPercentage'],
                    ConfigData::$dbKeys[$strTableName]['ratingSpicy'] => $_POST['nameSpicyRating'],
                ],
                ConfigData::$dbTables['media'] => [
                    # File info
                    'fileFolderName' => $_POST['nameName'],
                    'fileName' => $_FILES['nameMainMedia']['name'],
                    'mediaGroup' => $_FILES['nameMainMedia']['type'],
                ],
            ];

            // ==== Start of Case ====
            # Processing the POST request and media via the api
            $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/addDish', ConfigData::$userAPIAccessToken, $arrPushedDishData);

            # Moving the file to the right folder
            if ($arrAPIReturn[0] != 200) {
                Functions::echoByStatusCode($arrAPIReturn[0]);
                header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['additem']."");
            }
            else {
                # Creating the folder if it doesn't exist
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                # Moving the file
                move_uploaded_file($tempPath, $filePath.'/'.$_FILES['nameMainMedia']['name']);

                # Header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Item is toegevoegd!</div>";

                # Redirecting to the menu page
                header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['menu']."");
            }
            break;

        # Actual pages
        case ConfigData::$employeePanelPages['account']:
            // ==== Declaring Variables ====
            # Bools
            $boolIsPasscodeLoggedIn = $_SESSION['employeePasscodeLoggedIn'] ?? false;

            // ==== Start of Case ====
            # Logging in with passcode (assume it is a login attempt)
            if (!$boolIsPasscodeLoggedIn) {
                // ==== Declaring Variables ====
                # == Arrays ==
                # POST
                $_POST['employeeID'] = $_SESSION['employeeID'] ?? '';

                # API
                $apiReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/employeePasscodeLogin', ConfigData::$userAPIAccessToken, $_POST);

                // ==== Start of If ====
                if ($apiReturn[0] != 200) {
                    Functions::echoByStatusCode($apiReturn[0]);
                    header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['account']."");
                }
                else {
                    $_SESSION['employeePasscodeLoggedIn'] = true;
                }
            }
            # Changing the user info (assume it's an editing attempt)
            else {
                // ==== Declaring Variables ====
                # == Arrays ==
                # POST
                $_POST['employeeID'] = $_SESSION['employeeID'] ?? '';

                # API
                $arrPushedUserData = [
                    ConfigData::$dbKeys['employeeUsers']['id'] => $_SESSION[ConfigData::$dbKeys['employeeUsers']['id']],
                    ConfigData::$dbTables['employeeUsers'] => [
                        'name' => $_POST['nameName'],
                        'email' => $_POST['nameEmail'],
                        'birthDate' => $_POST['nameBirthDate'],
                        'phoneNumber' => $_POST['namePhoneNumber'],
                        'passcode' => $_POST['namePasscode'],
                    ],
                ];
                $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/updateEmployeeUser', ConfigData::$userAPIAccessToken, $arrPushedUserData);

                // ==== Start of If ====
                // Setting the session variables to the new name
                $_SESSION['name'] = $arrAPIReturn[1]['data']['name'];

                // Making the header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Gegevens zijn gewijzigd!</div>";

                // Redirecting to the login page
                header("Location: ./employeePanel.php?page=".ConfigData::$employeePanelPages['account']."");
            }
            break;
        default:
            // ==== Declaring Variables ====
            # Strings
            break;
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    # Form pages (updating the data)
    case ConfigData::$employeePanelPages['additem']:
        // ==== Declaring Variables ====
        # == Strings ==
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
            $toppingID = $topping[ConfigData::$dbKeys['toppings']['id']];
            $toppingName = $topping[ConfigData::$dbKeys['toppings']['name']];
            $toppingPrice = $topping[ConfigData::$dbKeys['toppings']['price']];

            // ==== Start of Loop ====
            $selectDefaultToppingsHTML .= "
                <input type='checkbox' class='form-check-input' name='defaultToppings[$toppingID]' id='idDefaultToppings$toppingID' value='$toppingID'>
                <label class='form-check-label' for='idDefaultToppings$toppingID'>$toppingName</label> <br/>
            ";
        }
        $selectDefaultToppingsHTML .= "</div>";

        // ==== Start of Case ====
        # Making the form to add an item
        $mainPage = "
        <div class='container p-0'>
            <div class='row justify-content-center'>
                <div class='col-7 mb-4'>
                    <h4>Item toevoegen</h4>
                </div>
                <div class='col-7'>
                <form method='POST' enctype='multipart/form-data'>
                    <!-- Dish Name -->
                    <label for='idName' class='form-label'>Item naam</label>
                    <input type='text' class='form-control mb-3' name='nameName' id='idName' required>
                    
                    <!-- Dish Price -->
                    <label for='idPrice' class='form-label'>Prijs</label>
                    <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='namePrice' id='idPrice' required>
                    
                    <!-- Discount in percentage -->
                    <div class='container p-0'>
                        <div class='row'>
                            <div class='col-6'>
                                <label for='idDiscountPercentage' class='form-label'>Korting in %</label>
                                <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='nameDiscountPercentage' id='idDiscountPercentage' required>
                            </div>
                            <div class='col-6'>
                                <label for='idDiscountPrice' class='form-label'>Korting in €</label>
                                <input type='number' pattern='[0-9]+([\.,][0-9]+)?' step='0.01' class='form-control mb-3' name='nameDiscountPrice' id='idDiscountPrice' required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Spicy rating (1-5) -->
                    <label for='idSpicyRating' class='form-label'>Pittigheid (1-5)</label><br/>
                    <span id='idSpicyValue'></span>
                    <input type='range' class='form-range mb-3' name='nameSpicyRating' id='idSpicyRating' min='1' max='5' required>
                    
                    <!-- Media picker (Hoofdfoto) -->
                    <label for='idMainMedia' class='form-label'>Hoofdfoto</label>
                    <input type='file' class='form-control mb-3' name='nameMainMedia' id='idMainMedia' required>
                    
                    <!-- Image element for preview -->
                    <img class='mb-3' id='idImgPreview' src='' alt='Preview' style='display: none;'/>
                    
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
        $mainPage .= "<script src='".Functions::dynamicPathFromIndex()."files/js/employeePanel.js'></script>";

        break;

    # Actual pages
    case ConfigData::$employeePanelPages['menu']:
        // ==== Declaring Variables ====
        # == Ints ==
        $breakCounter = 0;
        # == Strings ==
        # SQL
        $sql = "SELECT * FROM ".ConfigData::$dbTables['dishes'].";";

        # == Arrays ==
        $dishes = PizzariaSopranosDB::pdoSqlReturnArray($sql);

        # == HTML ==
        # Add item button
        $mainPage = "
        <a class='text-decoration-none' href='./employeePanel.php?page=".ConfigData::$employeePanelPages['additem']."'>
            <button class='p-0 buttonNoOutline d-flex'>
                <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
                <h4 class='align-self-center m-0 ms-1'>Item toevoegen</h4>
            </button>
        </a>
        <hr/>
        ";

        // ==== Start of Case ====

        foreach ($dishes as $dish) {
            // ==== Declaring Variables ====
            # SQL (Dish media)
            $sql = "SELECT * FROM ".ConfigData::$dbTables['media']." WHERE ".ConfigData::$dbKeys['media']['dishID']." = ".$dish[ConfigData::$dbKeys['dishes']['id']].";";

            # Media objects
            $media = PizzariaSopranosDB::pdoSqlReturnArray($sql);

            # filePaths
            $thumbPath = '';
            if (!empty($media)) {
                $thumbPath = Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath.$dish[ConfigData::$dbKeys['dishes']['name']]."/{$media[0]['fileName']}";
            }

            # Discounted price
            $discountPercentage = $dish[ConfigData::$dbKeys['dishes']['discountPercentage']];
            $discountedPrice = $dish[ConfigData::$dbKeys['dishes']['price']] - ($dish[ConfigData::$dbKeys['dishes']['price']] * ($discountPercentage / 100));
            $discountedPriceHTML = '';
            if ($discountedPrice != $dish[ConfigData::$dbKeys['dishes']['price']]) {
                $discountedPriceHTML = "
                <div class='d-flex'>
                    <!-- Discounted price -->
                    <p class='card-text text-danger me-4'>€ $discountedPrice</p>
                    
                    <!-- Discount percentage -->
                    <p class='card-text text-danger'>$discountPercentage%</p>
                </div>
                ";
            }

            // ==== Start of Loop ====
            # Checking if the counter is divisible by 3 so it can make a new row
            if ($breakCounter % 3 == 0) {
                $mainPage .= "<div class='row mb-3'>";
            }

            # Making the card
            $mainPage .= "
            <div class='col-lg-4 col-md-12 col-sm-12'>
                <div class='card mb-4'>
                    <img class='card-img-top' src='$thumbPath' alt='Dish Image'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$dish[ConfigData::$dbKeys['dishes']['name']]."</h5>
                        <p class='card-text'>€ ".$dish[ConfigData::$dbKeys['dishes']['price']]."</p>
                        $discountedPriceHTML
                        <div class='d-flex flex-wrap'>
                            <button class='btn btn-primary me-2'>Aanpassen</button>
                            <form method='POST'>
                                <input type='hidden' name='deleteDishID' value='".$dish[ConfigData::$dbKeys['dishes']['id']]."'>
                                <input type='submit' class='btn btn-danger' value='Verwijderen'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            ";

            # Adding to the counter
            $breakCounter++;

            # Checking if the counter is divisible by 3 so it can close the row
            if ($breakCounter % 3 == 0) {
                $mainPage .= "</div>";
            }
        }

        if ($breakCounter % 3 != 0) {
            $mainPage .= "</div>";
        }
        break;
    case ConfigData::$employeePanelPages['toppings']:
        // ==== Declaring Variables ====
        # == Strings ==
        # SQL
        $sql = "SELECT * FROM ".ConfigData::$dbTables['toppings'].";";

        # == Arrays ==
        $toppings = PizzariaSopranosDB::pdoSqlReturnArray($sql);

        # == HTML ==
        # Add item button
        $mainPage = "
        <button class='p-0 button buttonNoOutline d-flex'>
            <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
            <h4 class='m-0 ms-1 align-self-center'>Item toevoegen</h4>
        </button>
        <hr/>
        ";

        // ==== Start of Case ====
        # Just one big list without any images and the change / delete buttons
        foreach ($toppings as $topping) {
            $mainPage .= "
            <div class='row mb-3'>
                <div class='col-12 col-sm-6 col-md-4'>
                    ".$topping[ConfigData::$dbKeys['toppings']['name']]."
                </div>
                <div class='col-12 col-sm-6 col-md-2'>
                € ".$topping[ConfigData::$dbKeys['toppings']['price']]."
                </div>
                <div class='col-12 col-sm-12 col-md-6'>
                    <div class='d-flex justify-content-end'>
                        <button class='btn btn-primary me-2'>Aanpassen</button>
                        <button class='btn btn-danger'>Verwijderen</button>
                    </div>
                </div>
            </div>
            ";
        }
        break;
    case ConfigData::$employeePanelPages['account']:
        // ==== Declaring Variables ====
        # Bools
        $boolIsPasscodeLoggedIn = $_SESSION['employeePasscodeLoggedIn'] ?? false;

        # Strings
        $idPasscodeInput = 'idPasscode';
        $namePasscodeInput = 'namePasscode';

        // ==== Start of Case ====
        if (!$boolIsPasscodeLoggedIn) {
            // ==== Start of If ====
            # Making form for the passcode verification
            $mainPage = "
                <div class='container'>
                    <div class='row justify-content-center'>
                        <!-- Form -->
                        <div class='col-12 col-lg-3 col-sm-8 col-md-5 '>
                            <form method='post' id='idFormPasscodeLogin'>
                                <input type='password' id='$idPasscodeInput' name='$namePasscodeInput' class='form-control mb-3' placeholder='Code' required/>
                            </form>
                        </div>
                        
                        <!-- Submit button -->
                        <div class='col-12 col-lg-3 col-sm-8 col-md-5'>
                            <button class='btn btn-primary w-100' form='idFormPasscodeLogin'>Inloggen</button>
                        </div>
                    </div>
                    <div class='row justify-content-center'>
                        <!-- Numberpad -->
                        <div class='col-12 col-lg-6 col-sm-8 col-md-10 col- '>
                            ".Functions::htmlNumberPad($idPasscodeInput)."
                        </div>
                    </div>
                </div>
            ";
        }
        else {
           // ==== Declaring Variables ====
            # == Strings ==
            $strEmployeeTableName = Configdata::$dbTables['employeeUsers'];

            # == Arrays ==
            # API
            $arrNeededUserData = [
                'employeeID' => $_SESSION['employeeID'],
                $strEmployeeTableName => [
                    'name' => ConfigData::$dbKeys[$strEmployeeTableName]['name'],
                    'email' => ConfigData::$dbKeys[$strEmployeeTableName]['email'],
                    'birthDate' => ConfigData::$dbKeys[$strEmployeeTableName]['birthDate'],
                    'phoneNumber' => ConfigData::$dbKeys[$strEmployeeTableName]['phoneNumber'],
                    'passcode' => ConfigData::$dbKeys[$strEmployeeTableName]['passcode'],
                ]
            ];
            $apiReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/getEmployeeData', ConfigData::$userAPIAccessToken, $arrNeededUserData);

            // ==== Start of If ====
            # Making it into a form
            if ($apiReturn[0] == 200) {
                $employeeData = $apiReturn[1]['data'][$strEmployeeTableName][0];

                $mainPage = "
                <div class='container'>
                    <div class='row justify-content-center'>
                        <div class='col-6'>
                            <form method='POST' id='idFormEmployeeData'>
                                <div class='mb-3'>
                                    <label for='idName' class='form-label'>Naam</label>
                                    <input type='text' id='idName' name='nameName' class='form-control' value='$employeeData[name]' required/>
                                </div>
                                <div class='mb-3'>
                                    <label for='idEmail' class='form-label'>Email</label>
                                    <input type='email' id='idEmail' name='nameEmail' class='form-control' value='$employeeData[email]' required/>
                                </div>
                                <div class='mb-3'>
                                    <label for='idBirthDate' class='form-label'>Geboortedatum</label>
                                    <input type='date' id='idBirthDate' name='nameBirthDate' class='form-control' value='$employeeData[birthDate]' required/>
                                </div>  
                                <div class='mb-3'>
                                    <label for='idPhoneNumber' class='form-label'>Telefoonnummer</label>
                                    <input type='tel' id='idPhoneNumber' name='namePhoneNumber' class='form-control' value='$employeeData[phoneNumber]' required/>
                                </div>
                                <div class='mb-3'>
                                    <label for='idPasscode' class='form-label'>Code</label>
                                    <input type='password' id='idPasscode' name='namePasscode' class='form-control' value='$employeeData[passcode]' required/>
                                </div>
                                <button class='btn btn-primary w-100' form='idFormEmployeeData'>Opslaan</button>
                            </form>
                        </div>
                    </div>
                </div>
                ";
            }
            else {
                header("Location: ".Functions::dynamicPathFromIndex()."index.php");
            }
        }
        break;

    default:
        // ==== Declaring Variables ====
        $mainPage = 'hi';
        break;
}

# ==== Body ====
# Main page
echo("
<div class='container mb-3'>
    <div class='row justify-content-center mb-3'>
    <!-- Account Navbar -->
        <div class='col-8 mb-3'>
            ".Functions::htmlEmployeeNavbar()."
        </div>
    </div>
    <div class='row justify-content-center'>
        <div class='col-10 col-lg-8 col-md-8 col-sm-10'>
            $mainPage
        </div>
    </div>
</div>
");

# Scripts
echo("
    <script src='".Functions::dynamicPathFromIndex()."files/js/employeePanel.js'></script>
");