<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Checking if the logout button is pressed
if(isset($_GET['page'])){
    if ($_GET['page'] == 'logout') {
        session_destroy();
        header("Location: ".Functions::dynamicPathFromIndex()."index.php");
    }
}

# ==== POST Requests ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ======== Declaring Variables ========


    // ======== Start of POST Request ========
    # Checking if all the fields are filled in
    if (empty($_POST['nameEmailInput']) || empty($_POST['namePasswordInput'])) {
        echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
        $boolTrue = False;
    }
}

# Header
Functions::htmlHeader(320);

# ==== Dynamic HTML ====
$mainPage = '';
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
                <div class='container'>
                    <label for='nameName'>Naam: </label><br/>
                    <input type='text' id='idName' name='nameName' value='".$_SESSION['name']."'><br/>
                    <br/>
                    <label for='nameEmail'>Email: </label><br/>
                    <input type='email' id='idEmail' name='nameEmail' value='".$userData[1]['data']['users'][0]['email']."'><br/>
                    <br/>
                    <label for='nameBirthDate'>Geboortedatum: </label><br/>
                    <input type='date' id='idBirthDate' name='nameBirthDate' value='".$userData[1]['data']['users'][0]['birthDate']."'><br/>
                    <br/>
                    <label for='namePhoneNumber'>Telefoonnummer: </label><br/>
                    <input type='tel' id='idPhoneNumber' name='namePhoneNumber' value='".$userData[1]['data']['users'][0]['phoneNumber']."'><br/>
                    <br/>
                    <input type='submit' value='Wijzigen'>
                <div>
            ";
        }
        break;
}

# ==== Body ====
# Main page
echo("
<div class='container'>
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