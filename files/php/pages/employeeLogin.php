<?php
// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
# Header
Functions::htmlHeader(320);
Functions::toIndexIfLoggedIn();

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
        $arrAPIReturn = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/employeeLogin', ConfigData::$userAPIAccessToken, $_POST);
        # Check if it's done
        if ($arrAPIReturn[0] == 200) {
            // Putting the data in the session
            $_SESSION['employeeID'] = $arrAPIReturn[1]['data']['employeeID'];
            $_SESSION['name'] = $arrAPIReturn[1]['data']['name'];
            $_SESSION['loggedIn'] = True;
            $_SESSION['role'] = $arrAPIReturn[1]['data']['role'];

            if (empty($_SESSION['employeeID']) || empty($_SESSION['name']) || empty($_SESSION['role'])) {
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
            header("Location: ./employeelogin.php");
        }
    }
}

# Body
echo("
<div class='container'>
    <div class='row justify-content-center'>
        <div class='col-lg-5 col-md-8 col-sm-10 col-10 border border-dark rounded'>
            <form class='container-fluid mt-4 pl-5' method='post'>
                <div class='row'>
                    <div class='col-lg-8 col-md-12 col-sm-12'>
                        <label for='nameEmailInput'>E-mailadres </label><br/>
                        <input class='w-100 mb-3' type='email' id='idEmailInput' name='nameEmailInput'>

                        <label for='namePasswordInput'>Wachtwoord </label><br/>
                        <input class='w-100 mb-3 inputPassword' type='password' id='idPasswordInput' name='namePasswordInput'>
                    </div>
                </div>
                <div class='row mt-4 mb-4'>
                    <div class='col-12 col-lg-12 justify-content-center'>
                        <button type='submit' class='buttonIndexSubmit d-flex justify-content-center align-items-center btn w-100'>
                            <p style='margin: auto;'>Inloggen</p>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
");

# Footer
Functions::htmlFooter();
