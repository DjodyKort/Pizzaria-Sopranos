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
//$userData = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/loginUser', ConfigData::$userAPIAccessToken, $_POST);
// ============ Start of Program ============
Functions::htmlHeader();

# POST Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ======== Declaring Variables ========
    # ==== Bools ====
    $boolTrue = True;

    // ======== Start of POST Request ========
    # Checking if all the fields are filled in
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['billingAddress']) || empty($_POST['birthDate']) || empty($_POST['phoneNumber'])) {
        echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
        $boolTrue = False;
    }

    # Sending to API
    if ($boolTrue && isset($_POST['submitUpdate'])) {
        # Send form to API
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/updateUser', ConfigData::$userAPIAccessToken, $_POST);
        # Check if it's done
        if ($arrAPIReturn[0] == 200) {
            // Putting the data in the session
            $_SESSION['name'] = $arrAPIReturn[1]['data']['name'];
            $_SESSION['loggedIn'] = True;
            
            if (empty($_SESSION['name'])) {
                echo("Er is iets fout gegaan!");
            }
            else {
                // Making the header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Updated Account</div>";

                // Redirecting to the login page
                header("Location: ".Functions::dynamicPathFromIndex()."index.php");
            }
        }
        else {
            Functions::echoByStatusCode($arrAPIReturn[0]);
        }
    }

}

if(isset($_POST['logout'])){
    unset($_SESSION['loggedIn']);
    session_destroy();
    header('Location: ../../../../index.php');
}

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
                        <input type='submit' name='logout' value='uitloggen' />
                    </td>
                </form>
                </tr>
    </table>    
");




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
        <input type='hidden' value='".$_SESSION['userID']." name='userID''>
            <table class='table'>
                <tr class='tr'>
                    <td class='td'>Name</td>
                    <td class='td'><input type='text' name='name'/></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Email</td>
                    <td class='td'><input type='email' name='email'/></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>billing Address</td>
                    <td class='td'><input type='text' name='billingAddress'/></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>Birth Date</td>
                    <td class='td'><input type='date' name='birthDate'/></td>
                </tr>
                <tr class='tr'>
                    <td class='td'>PhoneNumber</td>
                    <td class='td'><input type='phonenumber' name='phoneNumber'/></td>
                </tr>
                
            </table>
            <input type='submit' name='submitUpdate'>
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