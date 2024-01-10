<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Header
Functions::htmlHeader();

# Checking if the logout button is pressed
if ($_GET['page'] == 'logout') {
    session_destroy();
    header("Location: ".Functions::dynamicPathFromIndex()."index.php");
}

# Account Navbar
Functions::htmlAccountNavbar();

# Body
switch ($currentPage) {
    case 'addresses':
        echo("
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
        "); break;
    case 'orders':
        echo("
        
        "); break;
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
        }
        else {
            echo("
                <div class='container'>
                <form>
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
                    <input type='submit' value='Verzenden'>
                </form>
                <div>
            ");
        }
        break;
}
