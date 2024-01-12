<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
# Header
Functions::htmlHeader(300);

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
            header("Location: ./register.php");
        }
    }
}

# Body
echo("
<div class='container'>
    <div class='row justify-content-center'>
        <div class='col-lg-6 col-md-8 col-sm-10 border border-dark rounded'>
            <div class='container-fluid mt-4 pl-5'>
                <form method='post'>
                    <div class='row'>
                        <div class='col-lg-6 col-md-12 col-sm-12'>
                            <label for='nameNameInput'>Naam </label><br/>
                            <input class='w-100 mb-3' type='text' id='idNameInput' name='nameNameInput'><br/>
                            
                            <label for='nameEmailInput'>E-mailadres     </label><br/>
                            <input class='w-100 mb-3' type='email' id='idEmailInput' name='nameEmailInput'><br/>
                            
                            <label for='namePasswordInput'>Wachtwoord </label><br/>
                            <input class='w-100 mb-3 inputPassword' type='password' id='idPasswordInput' name='namePasswordInput'><br/>
                            
                            <label for='namePasswordRepeatInput'>Wachtwoord herhalen </label><br/>
                            <input class='w-100 mb-3' type='password' id='idPasswordRepeatInput' name='namePasswordRepeatInput'><br/>
                        </div>
                    </div>
                    <div class='row mt-4 mb-4'>
                        <div class='col-12 justify-content-center'>
                            <button type='submit' class='buttonIndexSubmit d-flex justify-content-center align-items-center btn w-100'>
                                <p style='margin: auto;'>Registreren</p>
                            </button>
                        </div>
                    </div>
                </form>
            </div>   
        </div>
    </div>
</div>
");

# Footer
Functions::htmlFooter();