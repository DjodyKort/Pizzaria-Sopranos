<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# ==== GET ====
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Header
Functions::htmlHeader(300);
Functions::toIndexIfNotUser();

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
        case 'createFAddress':
            // ==== Declaring Variables ====
            # Bools
            $boolTrue = True;

            // ==== Start of POST Request ====
            # Checking if all the fields are filled in
            if (empty($_POST['nameStreetName']) || empty($_POST['nameHouseNumber']) || empty($_POST['namePostalCode']) || empty($_POST['nameCity'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            if ($boolTrue) {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    'billingAddresses' => [
                        ConfigData::$dbKeys['billingAddresses']['streetName'] => $_POST['nameStreetName'],
                        ConfigData::$dbKeys['billingAddresses']['houseNumber'] => $_POST['nameHouseNumber'],
                        ConfigData::$dbKeys['billingAddresses']['houseNumberAddition'] => $_POST['nameHouseNumberAddition'],
                        ConfigData::$dbKeys['billingAddresses']['postalCode'] => $_POST['namePostalCode'],
                        ConfigData::$dbKeys['billingAddresses']['city'] => $_POST['nameCity'],
                    ],
                ];

                // == Start of Program ==
                Functions::addAddressToDB($currentPage, $arrPushedUserData);
            }
            break;
        case 'deleteFAddress':
            // ==== Start of POST Request ====
            # Checking if the post isn't empty
            if (empty($_POST['idAddress'])) {
                echo("Er is iets fout gegaan! Probeer het later opnieuw.");
                $boolTrue = False;
            }
            else {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    'billingAddresses' => [
                        ConfigData::$dbKeys['billingAddresses']['id'] => $_POST['idAddress'],
                    ],
                ];

                // == Start of Program ==
                Functions::deleteAddressFromDB($arrPushedUserData);
            }


            break;
        case 'changeFAddress':
            // ==== Declaring Variables ====
            # Bools
            $boolTrue = True;

            // ==== Start of POST Request ====
            # Checking if all the fields are filled in
            if (empty($_POST['nameStreetName']) || empty($_POST['nameHouseNumber']) || empty($_POST['namePostalCode']) || empty($_POST['nameCity'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            if ($boolTrue) {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    ConfigData::$dbTables['addresses'] => [
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['id'] => $_POST['idAddress'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['streetName'] => $_POST['nameStreetName'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['houseNumber'] => $_POST['nameHouseNumber'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['houseNumberAddition'] => $_POST['nameHouseNumberAddition'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['postalCode'] => $_POST['namePostalCode'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['addresses']]['city'] => $_POST['nameCity'],
                    ],
                ];

                // == Start of Program ==
                Functions::updateAddressInDB($currentPage, $arrPushedUserData);
            }
            break;
        case 'createBAddress':
            // ==== Declaring Variables ====
            # Bools
            $boolTrue = True;

            // ==== Start of POST Request ====
            # Checking if all the fields are filled in
            if (empty($_POST['nameStreetName']) || empty($_POST['nameHouseNumber']) || empty($_POST['namePostalCode']) || empty($_POST['nameCity'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            if ($boolTrue) {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    'addresses' => [
                        ConfigData::$dbKeys['billingAddresses']['streetName'] => $_POST['nameStreetName'],
                        ConfigData::$dbKeys['billingAddresses']['houseNumber'] => $_POST['nameHouseNumber'],
                        ConfigData::$dbKeys['billingAddresses']['houseNumberAddition'] => $_POST['nameHouseNumberAddition'],
                        ConfigData::$dbKeys['billingAddresses']['postalCode'] => $_POST['namePostalCode'],
                        ConfigData::$dbKeys['billingAddresses']['city'] => $_POST['nameCity'],
                    ],
                ];

                // == Start of Program ==
                Functions::addAddressToDB($currentPage, $arrPushedUserData);
            }
            break;
        case 'deleteBAddress':
            // ==== Start of POST Request ====
            # Checking if the post isn't empty
            if (empty($_POST['idAddress'])) {
                echo("Er is iets fout gegaan! Probeer het later opnieuw.");
                $boolTrue = False;
            }
            else {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    'addresses' => [
                        ConfigData::$dbKeys['addresses']['id'] => $_POST['idAddress'],
                    ],
                ];

                // == Start of Program ==
                Functions::deleteAddressFromDB($arrPushedUserData);
            }
            break;
        case 'changeBAddress':
            // ==== Declaring Variables ====
            # Bools
            $boolTrue = True;

            // ==== Start of POST Request ====
            # Checking if all the fields are filled in
            if (empty($_POST['nameStreetName']) || empty($_POST['nameHouseNumber']) || empty($_POST['namePostalCode']) || empty($_POST['nameCity'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            if ($boolTrue) {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    ConfigData::$dbTables['billingAddresses'] => [
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['id'] => $_POST['idAddress'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['streetName'] => $_POST['nameStreetName'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['houseNumber'] => $_POST['nameHouseNumber'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['houseNumberAddition'] => $_POST['nameHouseNumberAddition'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['postalCode'] => $_POST['namePostalCode'],
                        ConfigData::$dbKeys[ConfigData::$dbTables['billingAddresses']]['city'] => $_POST['nameCity'],
                    ],
                ];

                // == Start of Program ==
                Functions::updateAddressInDB($currentPage, $arrPushedUserData);
            }
            break;

        case 'orders':
            break;
        default:
            // ==== Declaring Variables ====
            # Bools
            $boolTrue = True;

            // ==== Start of POST Request ====
            # Checking if all the fields are filled in
            if (empty($_POST['nameName']) || empty($_POST['nameEmail']) || empty($_POST['nameBirthDate']) || empty($_POST['namePhoneNumber'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            if ($boolTrue) {
                // == Declaring Variables ==
                # Arrays
                $arrPushedUserData = [
                    'userID' => $_SESSION['userID'],
                    ConfigData::$dbTables['users'] => [
                        'name' => $_POST['nameName'],
                        'email' => $_POST['nameEmail'],
                        'birthDate' => $_POST['nameBirthDate'],
                        'phoneNumber' => $_POST['namePhoneNumber'],
                    ],
                ];

                // == Start of Program ==
                $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/updateUser', ConfigData::$userAPIAccessToken, $arrPushedUserData);

                // Setting the session variables to the new name
                $_SESSION['name'] = $arrAPIReturn[1]['data']['name'];

                // Making the header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Gegevens zijn gewijzigd!</div>";

                // Redirecting to the login page
                header("Location: ./userSettings.php");
            }
            break;
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    case 'addresses':
        // ==== Declaring Variables ====
        # Arrays
        $arrUserNeededData = [
            'userID' => $_SESSION['userID'],
            'billingAddresses' => [
                'billingAddressID' => ConfigData::$dbKeys['billingAddresses']['id'],
                'streetName' => ConfigData::$dbKeys['billingAddresses']['streetName'],
                'houseNumber' => ConfigData::$dbKeys['billingAddresses']['houseNumber'],
                'houseNumberAddition' => ConfigData::$dbKeys['billingAddresses']['houseNumberAddition'],
                'postalCode' => ConfigData::$dbKeys['billingAddresses']['postalCode'],
                'city' => ConfigData::$dbKeys['billingAddresses']['city'],
            ],
            'addresses' => [
                'billingAddressID' => ConfigData::$dbKeys['addresses']['id'],
                'streetName' => ConfigData::$dbKeys['addresses']['streetName'],
                'houseNumber' => ConfigData::$dbKeys['addresses']['houseNumber'],
                'houseNumberAddition' => ConfigData::$dbKeys['addresses']['houseNumberAddition'],
                'postalCode' => ConfigData::$dbKeys['addresses']['postalCode'],
                'city' => ConfigData::$dbKeys['addresses']['city'],
            ]
        ];

        // ==== Start of switch case ====
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/getUserData', ConfigData::$userAPIAccessToken, $arrUserNeededData);

        if ($arrAPIReturn[0] != 200) {
            Functions::echoByStatusCode($arrAPIReturn[0]);
            header("Location: ./userSettings.php?page=addresses");
        }
        else {
            // == Declaring Variables ==
            # Adresses
            $arrBillingAddresses = $arrAPIReturn[1]['data']['billingAddresses'];
            $arrAddresses = $arrAPIReturn[1]['data']['addresses'];
            # HTML
            $htmlBillingAddresses = Functions::htmlShowBillingAddresses($arrBillingAddresses);
            $htmlAddresses = Functions::htmlShowAddresses($arrAddresses);

            // == Start of Program ==
            $mainPage = "
            <div class='container'>
                <div class='row'>
                    <h4 class='p-0'>Factuuradres</h4>
                    $htmlBillingAddresses
                    
                    <div class='clearfix mb-1'></div>
                    <hr class='col-12 col-lg-11 col-md-11'/>
                    
                    <h4 class='p-0'>Bezorgadressen</h4>
                    <div class='container m-0 col-12 col-lg-11 col-md-11'>
                        <div class='row'>
                            <div class='col-12 p-0 mt-2 mb-3'>
                                <a class='d-flex align-items-center text-decoration-none text-black' href='./userSettings.php?page=createBAddress' >
                                    <!-- Round button with plus inside -->
                                    <button class='p-0 buttonNoOutline'>
                                        <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
                                    </button>
                                
                                    <!-- Text -->
                                    <p class='m-0 ms-2'>
                                        Adres toevoegen
                                    </p>
                                </a>
                            </div>
                        </div>
                        $htmlAddresses
                    </div>
                </div>
            </div>
        ";
        }
        break;
    case 'createFAddress':
        // ==== Start of switch case ====
        $mainPage = Functions::htmlAddOrChangeAddress('Factuuradres toevoegen', 'Toevoegen');
        break;
    case 'changeFAddress':
        // ==== Start of switch case ====
        $mainPage = Functions::htmlAddOrChangeAddress('Bezorgadres wijzigen', 'Wijzigen', ConfigData::$dbTables['addresses']);
        break;
    case 'createBAddress':
        // ==== Start of switch case ====
        $mainPage = Functions::htmlAddOrChangeAddress('Bezorgadres toevoegen', 'Toevoegen');
        break;
    case 'changeBAddress':
        // ==== Start of switch case ====
        $mainPage = Functions::htmlAddOrChangeAddress('Factuuradres wijzigen', 'Wijzigen', ConfigData::$dbTables['billingAddresses']);
        break;
    case 'orders':
        $mainPage = "";
        $tableOrders = 'orders';
        $tableOrderDishes = 'orderDishes';
        $tableAddresses = 'addresses';

        // Get data from db
        $query = "SELECT * FROM $tableOrders 
        WHERE userID = {$_SESSION['userID']} 
        ORDER BY orderStatus ASC";
        
        $result = PizzariaSopranosDB::pdoSqlReturnArray($query);

        if(empty($result)){
            $mainPage = "Er zijn geen bestellingen gevonden";
            break;
        }
        

        $mainPage .= "<div class='container-fluid'>";
        foreach($result as $row){
            $orderID = $row['orderID'];
            $streetName = $row['streetName'];
            $houseNumber = $row['houseNumber'];
            $postalCode = $row['postalCode'];
            $city = $row['city'];
            if(!$row['isGuest']){
                $query = "SELECT * FROM $tableAddresses WHERE userID = {$row['userID']}";
                $resultAddresses = PizzariaSopranosDB::pdoSqlReturnArray($query);
                $streetName = $resultAddresses[0]['streetName'];
                $houseNumber = $resultAddresses[0]['houseNumber'];
                $postalCode = $resultAddresses[0]['postalCode'];
                $city = $resultAddresses[0]['city'];
            }
            // Add a row to the table for each order
            $mainPage .= "
            <div class='accordion' id='orders' >
                <div class='accordion-item'>
                    <h2 class='accordion-header'>
                        <button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapse{$row['orderID']}' aria-expanded='true' aria-controls='collapseOne'>
                           $orderID Address:   $streetName $houseNumber $postalCode $city
                        </button>
                    </h2>
                
                <div id='collapse{$row['orderID']}' class='accordion-collapse collapse' data-bs-parent='#orders'>
                    <div class='accordion-body'>
                        <div class='row'>
                            <div class='col-8 col'>
                                <table class='table' >
                                    <thead>
                                        <tr>
                                            <th scope='col'>Pizza Naam</th>
                                            <th scope='col'>Toppings</th>
                                        </tr>
                                    </thead>";

                $query = "SELECT orderDishes.*, dishes.name
                FROM $tableOrderDishes AS orderDishes 
                JOIN dishes ON orderDishes.dishID = dishes.dishID 
                WHERE orderDishes.orderID = {$row['orderID']}";
                $resultOrdersDishes = PizzariaSopranosDB::pdoSqlReturnArray($query);
                foreach($resultOrdersDishes as $rowOrderDishes){
                    $mainPage .= "
                    <tbody>
                        <tr>
                            <td>{$rowOrderDishes['name']}</td>
                            <td>{$rowOrderDishes['toppings']}</td>
                        </tr>
                    </tbody>
                    ";
                }
                

                $mainPage .= "
                </table>
                    </div>
                    <div class='col-4 col'>
                    <p>Payment method: {$row['paymentMethod']}</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
            ";
        }
        break;
    default:
        // ==== Declaring Variables ====
        # Arrays
        $arrUserNeededData = [
            'userID' => $_SESSION['userID'],
            'users' => [
                'email' => ConfigData::$dbKeys['users']['email'],
                'birthDate' => ConfigData::$dbKeys['users']['birthDate'],
                'phoneNumber' => ConfigData::$dbKeys['users']['phoneNumber'],
            ],
            'billingAddresses' => [
                'streetName' => ConfigData::$dbKeys['billingAddresses']['streetName'],
                'houseNumber' => ConfigData::$dbKeys['billingAddresses']['houseNumber'],
                'houseNumberAddition' => ConfigData::$dbKeys['billingAddresses']['houseNumberAddition'],
                'postalCode' => ConfigData::$dbKeys['billingAddresses']['postalCode'],
                'city' => ConfigData::$dbKeys['billingAddresses']['city'],
            ]
        ];

        // ==== Start of switch case ====
        $userData = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/getUserData', ConfigData::$userAPIAccessToken, $arrUserNeededData);

        if ($userData[0] != 200) {
            Functions::echoByStatusCode($userData[0]);
            var_dump($userData);
        }
        else {
            $mainPage = "
                <form method='POST' class='container'>
                    <label for='nameName'>Naam: </label><br/>
                    <input class='form-control' type='text' id='idName' name='nameName' placeholder='Naam' value='".$_SESSION['name']."'>
                    <br/>
                    <label for='nameEmail'>Email: </label><br/>
                    <input class='form-control' type='email' id='idEmail' name='nameEmail' placeholder='Email' value='".$userData[1]['data']['users'][0]['email']."'>
                    <br/>
                    <label for='nameBirthDate'>Geboortedatum: </label>
                    <br/>
                    <input class='form-control' type='date' id='idBirthDate' name='nameBirthDate' placeholder='Geboortedatum' value='".$userData[1]['data']['users'][0]['birthDate']."'><br/>
                    <label for='namePhoneNumber'>Telefoonnummer: </label>
                    <br/>
                    <input class='form-control' type='tel' pattern='[0-9]{10}' id='idPhoneNumber' name='namePhoneNumber' placeholder='Telefoonnummer' value='".$userData[1]['data']['users'][0]['phoneNumber']."'><br/>
                    <br/>
                    <input class='btn btn-outline-danger' type='submit' value='Wijzigen'>
                <form>
            ";
        }
        break;
}

# ==== Body ====
# Main page
echo("
<div class='container mb-3'>
    <div class='row justify-content-center mb-3'>
    <!-- Account Navbar -->
        <div class='col-6 mb-3'>
            ".Functions::htmlAccountNavbar()."
        </div>
    </div>
    <div class='row justify-content-center'>
        <div class='col-10 col-lg-5 col-md-7 col-sm-10 border border-black rounded-4 pt-3 pb-4'>
            $mainPage
        </div>
    </div>
</div>
");

# Footer
Functions::htmlFooter();