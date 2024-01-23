<?php
// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # Send form to API
    $boolSuccess = Functions::sendFormToAPI(Functions::pathToURL(Functions::dynamicPathFromIndex().'files/php/api/userAPI.php').'/createUser', ConfigData::$userAPIAccessToken, $_POST);

    # Check if it's done
    if ($boolSuccess){
        echo("Het account is aangemaakt!");
    }
    else {
        echo("Er is iets fout gegaan, probeer het later opnieuw!");
    }
}
Functions::htmlHeader(320);
echo("
<div>
    <h1 class='text-center'>Test Page</h1>
    
    <div class='container-fluid'>
        <form method='POST'>
            <label for='nameNameInput'>Naam: </label><br/>
            <input type='text' id='idNameInput' name='nameNameInput'><br/>
            <br/>
            
            <label for='nameEmailInput'>Email: </label><br/>
            <input type='text' id='idEmailInput' name='nameEmailInput'><br/>
            <br/>
            
            <label for='namePasswordInput'>Wachtwoord: </label><br/>
            <input type='text' id='idPasswordInput' name='namePasswordInput'><br/>
            <br/>
            
            <label for='namePasswordRepeatInput'>Wachtwoord herhalen: </label><br/>
            <input type='text' id='idPasswordRepeatInput' name='namePasswordRepeatInput'><br/>
            <br/>
            
            <input class='btn-primary btn' type='submit' value='Verzenden'>
        </form>
    </div>
</div>
");
Functions::htmlFooter();