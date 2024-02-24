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

        default:
            // ==== Declaring Variables ====
            # Strings
            break;
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    case 'menu':
        // ==== Declaring Variables ====
        # == Ints ==
        $breakCounter = 0;
        # == Strings ==
        # SQL
        $sql = "SELECT * FROM ".ConfigData::$dbTables['dishes'].";";

        # == Arrays ==
        $dishes = PizzariaSopranosDB::pdoSqlReturnArray($sql);

        # == HTML ==
        # Add item button
        $mainPage = "
        <button class='p-0 buttonNoOutline d-flex'>
            <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
            <h4 class='m-0 ms-1 align-self-center'>Item toevoegen</h4>
        </button>
        <hr/>
        ";

        // ==== Start of Case ====

        foreach ($dishes as $dish) {
            // ==== Declaring Variables ====
            # SQL (Dish media)
            $sql = "SELECT * FROM ".ConfigData::$dbTables['media']." WHERE ".ConfigData::$dbKeys['media']['dishID']." = ".$dish[ConfigData::$dbKeys['dishes']['id']].";";

            # Media objects
            $media = PizzariaSopranosDB::pdoSqlReturnArray($sql);

            # filePaths
            if (!empty($media)) {
                $completeFileName = $media[0]['fileName'].$media[0]['fileExtension'];
                $thumbPath = Functions::dynamicPathFromIndex()."files/images/dishes/".$dish[ConfigData::$dbKeys['dishes']['name']]."/$completeFileName";
            }

            // ==== Start of Loop ====
            if ($breakCounter % 3 == 0) {
                $mainPage .= "<div class='row mb-3'>";
            }

            $mainPage .= "
            <div class='col-lg-4 col-md-12 col-sm-12'>
                <div class='card mb-4'>
                    <img class='card-img-top' src='$thumbPath' alt='Dish Image'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$dish[ConfigData::$dbKeys['dishes']['name']]."</h5>
                        <p class='card-text'>€ ".$dish[ConfigData::$dbKeys['dishes']['price']]."</p>
                        <div class='d-flex flex-wrap'>
                            <button class='btn btn-primary me-2'>Aanpassen</button>
                            <button class='btn btn-danger'>Verwijderen</button>
                        </div>
                    </div>
                </div>
            </div>
            ";

            $breakCounter++;

            if ($breakCounter % 3 == 0) {
                $mainPage .= "</div>";
            }
        }

        if ($breakCounter % 3 != 0) {
            $mainPage .= "</div>";
        }
        break;
    case 'toppings':
        // ==== Declaring Variables ====
        # == Strings ==
        # SQL
        $sql = "SELECT * FROM ".ConfigData::$dbTables['toppings'].";";

        # == Arrays ==
        $toppings = PizzariaSopranosDB::pdoSqlReturnArray($sql);

        # == HTML ==
        # Add item button
        $mainPage = "
        <button class='p-0 button buttonNoOutline d-flex'>
            <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
            <h4 class='m-0 ms-1 align-self-center'>Item toevoegen</h4>
        </button>
        <hr/>
        ";

        // ==== Start of Case ====
        # Just one big list without any images and the change / delete buttons
        foreach ($toppings as $topping) {
            $mainPage .= "
            <div class='row mb-3'>
                <div class='col-4'>
                    ".$topping[ConfigData::$dbKeys['toppings']['name']]."
                </div>
                <div class='col-2'>
                € ".$topping[ConfigData::$dbKeys['toppings']['price']]."
                </div>
                <div class='col-6'>
                    <div class='d-flex justify-content-end'>
                        <button class='btn btn-primary me-2'>Aanpassen</button>
                        <button class='btn btn-danger'>Verwijderen</button>
                    </div>
                </div>
            </div>
            ";
        }
        break;
    case 'account':
        // ==== Declaring Variables ====
        # == Strings ==
        $idPasscodeInput = 'idPasscode';
        $namePasscodeInput = 'namePasscode';

        // ==== Start of Case ====
        # Making form for the passcode verification
        $mainPage = "
        <div class='container'>
            <div class='row justify-content-center'>
                <div class='col-3'>
                    <!-- Form -->
                    <form method='post' id='idFormPasscodeLogin'>
                        <input type='password' id='$idPasscodeInput' name='$namePasscodeInput' class='form-control mb-3' placeholder='Code' required/>
                    </form>
                </div>
                <div class='col-2'><!-- Submit button -->
                    <button class='btn btn-primary w-100' form='idFormPasscodeLogin'>Inloggen</button>
                </div>
            </div>
            <div class='row justify-content-center'>
                <div class='col-5'>
                    <!-- Numberpad -->
                    ".Functions::htmlNumberPad($idPasscodeInput)."
                </div>
            </div>
        </div>
        ";
        break;

    default:
        // ==== Declaring Variables ====
        $mainPage = 'hi';
        break;
}

# ==== Body ====
# Main page
echo("
<div class='container mb-3'>
    <div class='row justify-content-center mb-3'>
    <!-- Account Navbar -->
        <div class='col-8 mb-3'>
            ".Functions::htmlEmployeeNavbar()."
        </div>
    </div>
    <div class='row justify-content-center'>
        <div class='col-10 col-lg-8 col-md-8 col-sm-10'>
            $mainPage
        </div>
    </div>
</div>

");

# Scripts
echo("
    <script src='".Functions::dynamicPathFromIndex()."files/js/employeePanel.js'></script>
");