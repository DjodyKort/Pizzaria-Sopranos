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
                    echo("
                    nope
                    ");
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
        <button class='p-0 buttonNoOutline d-flex'>
            <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
            <h4 class='m-0 ms-1 align-self-center'>Item toevoegen</h4>
        </button>
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
            if (!empty($media)) {
                $completeFileName = $media[0]['fileName'].$media[0]['fileExtension'];
                $thumbPath = Functions::dynamicPathFromIndex()."files/images/dishes/".$dish[ConfigData::$dbKeys['dishes']['name']]."/$completeFileName";
            }

            // ==== Start of Loop ====
            if ($breakCounter % 3 == 0) {
                $mainPage .= "<div class='row mb-3'>";
            }

            $mainPage .= "
            <div class='col-lg-4 col-md-12 col-sm-12'>
                <div class='card mb-4'>
                    <img class='card-img-top' src='$thumbPath' alt='Dish Image'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$dish[ConfigData::$dbKeys['dishes']['name']]."</h5>
                        <p class='card-text'>€ ".$dish[ConfigData::$dbKeys['dishes']['price']]."</p>
                        <div class='d-flex flex-wrap'>
                            <button class='btn btn-primary me-2'>Aanpassen</button>
                            <button class='btn btn-danger'>Verwijderen</button>
                        </div>
                    </div>
                </div>
            </div>
            ";

            $breakCounter++;

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