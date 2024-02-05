<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Header
Functions::htmlHeader(300);

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
        case 'addresses':
            $mainPage = "
            <table class='table'>
                <tr class='tr'>
                    <td class='td'>Addressen</td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Vroedschapstraat 1 6445BH</td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Vroedschapstraat 2 6445BH</td>
                </tr>
            <table/>
        "; break;
        case 'orders':
            break;
        default:
            # Checking if all the fields are filled in
            if (empty($_POST['nameName']) || empty($_POST['nameEmail']) || empty($_POST['nameBirthDate']) || empty($_POST['namePhoneNumber'])) {
                echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
                $boolTrue = False;
            }

            // ==== Declaring Variables ====
            # Arrays
            $arrPushedUserData = [
                'userID' => $_SESSION['userID'],
                'users' => [
                    'name' => $_POST['nameName'],
                    'email' => $_POST['nameEmail'],
                    'birthDate' => $_POST['nameBirthDate'],
                    'phoneNumber' => $_POST['namePhoneNumber'],
                ],
            ];

            // ==== Start of Program ====
            $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/updateUser', ConfigData::$userAPIAccessToken, $arrPushedUserData);

            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Gegevens zijn gewijzigd!</div>";

            // Redirecting to the login page
            header("Location: ./userSettings.php");
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    case 'addresses':
        $mainPage = "
            <h3>Bezorgadressen</h3>
            <table class='table'>
                
            <table/>
        "; break;
    case 'orders':
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

        // ==== Start of Program ====
        $userData = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/getUserData', ConfigData::$userAPIAccessToken, $arrUserNeededData);

        if ($userData[0] != 200) {
            Functions::echoByStatusCode($userData[0]);
            var_dump($userData);
        }
        else {
            $mainPage = "
                <form method='POST' class='container'>
                    <label for='nameName'>Naam: </label><br/>
                    <input type='text' id='idName' name='nameName' placeholder='Naam' value='".$_SESSION['name']."'><br/>
                    <br/>
                    <label for='nameEmail'>Email: </label><br/>
                    <input type='email' id='idEmail' name='nameEmail' placeholder='Email' value='".$userData[1]['data']['users'][0]['email']."'><br/>
                    <br/>
                    <label for='nameBirthDate'>Geboortedatum: </label><br/>
                    <input type='date' id='idBirthDate' name='nameBirthDate' placeholder='Geboortedatum' value='".$userData[1]['data']['users'][0]['birthDate']."'><br/>
                    <br/>
                    <label for='namePhoneNumber'>Telefoonnummer: </label><br/>
                    <input type='tel' pattern='[0-9]{10}' id='idPhoneNumber' name='namePhoneNumber' placeholder='Telefoonnummer' value='".$userData[1]['data']['users'][0]['phoneNumber']."'><br/>
                    <br/>
                    <input type='submit' value='Wijzigen'>
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
        <div class='col-10 col-lg-6 col-md-7 col-sm-10'>
            $mainPage
        </div>
    </div>
</div>
");