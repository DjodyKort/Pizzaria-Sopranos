<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
$arrUserNeededData = [
    'users' => [
        'email' => ConfigData::$dbKeys['users']['email'],
        'billingAdress' => ConfigData::$dbKeys['users']['billingAdress'],
        'birthDate' => ConfigData::$dbKeys['users']['birthdate'],
        'phoneNumber' => ConfigData::$dbKeys['users']['phoneNumber'],
    ]
];
$userData = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/loginUser', ConfigData::$userAPIAccessToken, $_POST);
echo($userData[0]);
var_dump($userData[1]);

// ============ Start of Program ============
Functions::htmlHeader();
$url = parse_url($_SERVER['REQUEST_URI']);
echo("
    <table>
        <tr>
        <td class='" . (str_contains($url['path'], "/addresses") ? 'selected' : '') . "'><a href='/Pizzaria-Sopranos/files/php/pages/userSettings.php/addresses'>addressen</a></td>
        </tr>
        <tr>
            <td class='" . (strpos($url['path'], "/personInformation") !== false ? 'selected' : '') . "'><a href='/Pizzaria-Sopranos/files/php/pages/userSettings.php/personInformation'>Personel information</a></td
        </tr>
        <tr>
            <td class='" . (strpos($url['path'], "/pastOrders") !== false ? 'selected' : '') . "'><a href='/Pizzaria-Sopranos/files/php/pages/userSettings.php/pastOrders'>past orders</a></td>
        </tr>
        <tr>
            <form method='post'>
                <td>
                    <input type='submit' name='submit' value='uitloggen' />
                </td>
            </form>
        </tr>
    </table>    
");
if(isset($_POST['submit'])){
    unset($_SESSION['loggedIn']);
    header('Location: ../../../../index.php');
}
switch (true) {
    case strpos($url['path'], "/addresses") !== false:
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
        ");
        break;
    case strpos($url['path'], "/personInformation") !== false:
        echo("
            <table class='table'>
                <tr class='tr'>
                    <td class='td'>Name</td>
                    <td class='td'><input type='text' /></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Email</td>
                    <td class='td'><input type='email' /></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>billing Address</td>
                    <td class='td'><input type='text' /></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Birth Date</td>
                    <td class='td'><input type='date' /></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>PhoneNumber</td>
                    <td class='td'><input type='phonenumber' /></td>
                </tr>
                
            </table>
        
        ");
        break;
    case strpos($url['path'], "/pastOrders") !== false:
        echo("
        <table class='table'>
          <tr class='tr'>
            <td class='td'>orderInformation</td>
            <td class='td'>orderAddress</td>
            <td class='td'>orderTimeStart</td>
            <td class='td'>orderTimeEnd</td>
          </tr>
      </table>
        ");
        break;
    default:
        echo "Invalid path.";
        break;
}
# Footer
Functions::htmlFooter();