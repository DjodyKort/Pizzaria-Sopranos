<?php 

// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
# Header
Functions::htmlHeader();

# POST Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ======== Declaring Variables ========
    # ==== Bools ====
    $boolTrue = True;

    // ======== Start of POST Request ========
    # Checking if all the fields are filled in
    if (empty($_POST['nameNameInput']) || empty($_POST['nameEmailInput']) || empty($_POST['namePasswordInput']) || empty($_POST['namePasswordRepeatInput'])) {
        echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
        $boolTrue = False;
    }
    # Checking if the passwords are the same
    if (!Functions::isEqual($_POST['namePasswordInput'], $_POST['namePasswordRepeatInput'])) {
        echo("De wachtwoorden zijn niet hetzelfde!");
        $boolTrue = False;
    }
    if ($boolTrue) {
        # Send form to API
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/createUser', ConfigData::$userAPIAccessToken, $_POST);
        # Check if it's done
        if ($arrAPIReturn[0] == 200){
            // Making the header message
            $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Het account is aangemaakt!</div>";

            // Redirecting to the login page
            header("Location: ./login.php");
        }
        else {
            Functions::echoByStatusCode($arrAPIReturn[0]);
        }
    }
}

# Body
echo("
<div class='box'>
    <form method='post'>
        <label for='nameNameInput'>Naam: </label><br/>
            <input type='text' id='idNameInput' name='nameNameInput'><br/>
            <br/>
            
            <label for='nameEmailInput'>Email: </label><br/>
            <input type='email' id='idEmailInput' name='nameEmailInput'><br/>
            <br/>
            
            <label for='namePasswordInput'>Wachtwoord: </label><br/>
            <input type='password'  class='inputPassword' id='idPasswordInput' name='namePasswordInput'><br/>
            <br/>
            
            <label for='namePasswordRepeatInput'>Wachtwoord herhalen: </label><br/>
            <input type='password' id='idPasswordRepeatInput' name='namePasswordRepeatInput'><br/>
            <br/>
            
            <input class='btn-primary btn' type='submit' value='Verzenden'>
    </form>
</div>
");

# Footer
Functions::htmlFooter();