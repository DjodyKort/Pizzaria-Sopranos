<?php 

// ============ Imports ============
# Internally
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
    if (empty($_POST['nameEmailInput']) || empty($_POST['namePasswordInput'])) {
        echo("Niet alle velden zijn ingevuld! Zorg ervoor dat alle velden zijn ingevuld.");
        $boolTrue = False;
    }

    # Sending to API
    if ($boolTrue) {
        # Send form to API
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/loginUser', ConfigData::$userAPIAccessToken, $_POST);
        # Check if it's done
        if ($arrAPIReturn[0] == 200) {
            // Putting the data in the session
            $_SESSION['userID'] = $arrAPIReturn[1]['data']['userID'];
            $_SESSION['name'] = $arrAPIReturn[1]['data']['name'];
            $_SESSION['loggedIn'] = True;
            if (empty($_SESSION['userID']) || empty($_SESSION['name'])) {
                echo("Er is iets fout gegaan!");
            }
            else {
                // Making the header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>Ingelogd in het account!</div>";

                // Redirecting to the login page
                header("Location: ".Functions::dynamicPathFromIndex()."index.php");
            }
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
        <label for='nameEmailInput'>Email: </label><br/>
        <input type='email' id='idEmailInput' name='nameEmailInput'><br/>
        <br/>
        
        <label for='namePasswordInput'>Wachtwoord: </label><br/>
        <input type='password'  class='inputPassword' id='idPasswordInput' name='namePasswordInput'><br/>
        <br/>
             
        <input class='btn-primary btn' type='submit' value='Verzenden'>
    </form>
</div>
");

# Footer
Functions::htmlFooter();
