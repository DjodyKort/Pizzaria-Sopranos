<?php
// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

# TableNames
$tableAddresses = ConfigData::$dbTables['addresses'];
$tableOrders = ConfigData::$dbTables['orders'];
$tableOrderDishes = ConfigData::$dbTables['orderDishes'];

// ============ Start of Program ============
# Header
Functions::htmlHeader(320);

# POST Request
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($currentPage) {
        case 'choosePaymentMethod':
            // ==== Declare Variables ====
            # == POST & Session ==
            $orderID = $_SESSION['lastRowIDOrder'] ?? '';
            $chosenPaymentMethod = $_POST['paymentMethod'] ?? '';

            # == Strings ==
            # SQL
            $query = "UPDATE $tableOrders SET ".ConfigData::$dbKeys['orders']['paymentMethod']." = ? WHERE ".ConfigData::$dbKeys['orders']['id']." = ?";
            
            // ==== Start of Loop ====
            # Updating the order with the chosen payment method
            $boolResult = PizzariaSopranosDB::pdoSqlReturnTrue($query, [$chosenPaymentMethod, $orderID]);

            if ($boolResult) {
                # Header message
                $_SESSION['headerMessage'] = "<div class='alert alert-success' role='alert'>De order is verstuurd naar het filiaal!</div>";

                # Clearing the cart and the lastRowIDOrder
                unset($_SESSION['cart']); unset($_SESSION['lastRowIDOrder']);

                # Redirect to payment page
                header("Location: ".Functions::dynamicPathFromIndex()."index.php");
            }
            else {
                # Header message
                $_SESSION['headerMessage'] = "<div class='alert alert-danger' role='alert'>Er is iets fout gegaan, probeer het opnieuw!</div>";

                # Redirect to payment page
                header("Location: ".Functions::dynamicPathFromIndex()."index.php");
            }
            break;
        default:
            echo($_POST['chosenAddress']);
            //check if chosenAddress is clicked
            $dateTime = date('Y-m-d, H:i');

            // Calculating totalPrice
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $pizza) {
                $totalPrice += $pizza['dishTotal'];
            }
            if (isset($_POST['chosenAddress']) && $_SESSION['loggedIn']) {
                # SQL
                $query = "INSERT INTO $tableOrders (userID, orderStatus, addressID, billingAddressID, isGuest, dateOrdered, totalPrice) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $arrPrepare = [$_SESSION['userID'], 0, $_POST['addressId'], $_POST['billingAddressId'], 0 , $dateTime, $totalPrice];

                # Praepare
                $lastRowID = PizzariaSopranosDB::pdoSqlReturnLastID($query, $arrPrepare);

                # Put in session
                $_SESSION['lastRowIDOrder'] = $lastRowID;
            }
            else {
                # SQL
                $query = "INSERT INTO $tableOrders (orderStatus, isGuest, streetName, houseNumber, houseNumberAddition, postalCode, city, dateOrdered, totalPrice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $arrPrepare = [0, 1, $_POST['nameStreetName'], $_POST['nameHouseNumber'], $_POST['nameHouseNumberAddition'], $_POST['namePostalCode'], $_POST['nameCity'], date('Y-m-d H:i'), $totalPrice];

                # Praepare
                $lastRowID = PizzariaSopranosDB::pdoSqlReturnLastID($query, $arrPrepare);

                # Put in session
                $_SESSION['lastRowIDOrder'] = $lastRowID;
            }
            # SQL
            $query = "SELECT orderID FROM $tableOrders ORDER BY orderID DESC LIMIT 1";
            $result = PizzariaSopranosDB::pdoSqlReturnArray($query);
            foreach ($_SESSION['cart'] as $pizza) {
                $query = "INSERT INTO $tableOrderDishes (orderID, dishID, toppings) VALUES (?, ?, ?)";
                $toppingString = '';

                foreach ($pizza['toppings'] as $toppingID => $toppingAmount) {
                    // ==== Declare Variables ====
                    # == Strings ==
                    # SQL
                    $queryGetToppingInfo = "SELECT * FROM toppings WHERE toppingID = ?";

                    # == Arrays ==
                    $arrToppingInfo = PizzariaSopranosDB::pdoSqlReturnArray($queryGetToppingInfo, [$toppingID])[0];

                    // ==== Start of Loop ====
                    $toppingString .= "{$toppingAmount}x $arrToppingInfo[name], ";
                }

                $arrPrepare = [$result[0]['orderID'], $pizza['dishID'], rtrim($toppingString, ', ')];

                // Prepare and execute the SQL query inside the loop
                PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPrepare);
            }

            # Redirect to choose payment method
            header("Location: ./order.php?page=choosePaymentMethod");
            break;   
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    case 'choosePaymentMethod':
        // ==== Start of Loop ====
        $mainPage .= "
        <div class='container'>
            <h4 class='mt-2'>Kies een betaalmethode</h4>
            <div class='row'>
                <div class='col-12'>
                    <form>
                        <div class='form-check'>
                            <input class='form-check-input' type='radio' name='paymentMethod' id='ideal' value='iDeal' checked>
                            <label class='form-check-label d-flex' for='ideal'>
                                <img class='me-2' height='20px' src='".Functions::dynamicPathFromIndex()."files/images/ideal-logo.png' alt='Error: PayPal logo not found'>
                                <p>iDeal</p>
                            </label>
                        </div>
                        <div class='form-group mb-3' id='bank-selection'>
                            <label for='bank'>Kies je bank</label>
                            <select class='form-control' id='bank'>
                                <option>ABN AMRO</option>
                                <option>Rabobank</option>
                                <option>ING</option>
                                <!-- Add more banks as needed -->
                            </select>
                        </div>
                        <div class='form-check'>
                            <input class='form-check-input' type='radio' name='paymentMethod' id='paypal' value='PayPal'>
                            <label class='form-check-label d-flex' for='paypal'>
                                <img class='me-2' height='20px' src='".Functions::dynamicPathFromIndex()."files/images/paypal-logo.png' alt='Error: PayPal logo not found'>
                                <p>PayPal</p>
                            </label>
                        </div>
                        <!-- Add more payment methods as needed -->
                        <button type='submit' class='btn btn-primary mt-3'>Betalen</button>
                    </form>
                </div>
            </div>
        </div>
        ";

        break;
    default:
        // ==== Declare Variables ====
        # HTML
        if (isset($_SESSION['loggedIn'])) {
            $result = PizzariaSopranosDB::pdoSqlReturnArray("SELECT addresses.*, billing.billingAddressID
    FROM $tableAddresses addresses
    LEFT JOIN billingAddresses billing ON addresses.userID = billing.userID
    WHERE addresses.userID = {$_SESSION['userID']}"
            );
            foreach ($result as $row) {
                $mainPage .= "
                <div class='row'>
                    <div class='col-7 d-flex align-items-center'>
                        <p class='addressText mw-100 m-0 ms-1'>
                        {$row['streetName']} {$row['houseNumber']} {$row['houseNumberAddition']}<br/>
                        {$row['postalCode']} {$row['city']}
                        </p>
                        
                    </div>
                    <div class='col-5'>
                        <input type='hidden' value='{$row['addressID']}' name='addressId'>       
                        <input type='hidden' value='{$row['billingAddressID']}' name='billingAddressId'>         
                        <button type='submit' class='btn btn-outline-success w-100' name='chosenAddress'>Kies Adres</button>
                    </div>
                </div>
                <br/>   
                ";
            }
            $mainPage .= "
            <div class='row'>
                <div class='col-12 mb-3'>
                    <a class='d-flex align-items-center text-decoration-none text-black' href='./userSettings.php?page=createBAddress' >
                        <!-- Round button with plus inside -->
                        <button class='p-0 buttonNoOutline'>
                            <img height='35px' class='plus-button' src='".Functions::dynamicPathFromIndex()."files/images/plus-circle.svg' alt='Error: Plus button not found'>
                        </button>
                    
                        <!-- Text -->
                        <p class='m-0 ms-2'>
                            Adres toevoegen
                        </p>
                    </a>
                </div>
            </div>
            ";
        }
        else {
            $mainPage .= "
            <label>E-mailadres </label>
            <input class='form-control' type='email' id='idEmailInput' placeholder='Sopranos@Sopranos.nl' name='nameEmailInput' required><br/>
    
            <label>Telefoon Nummer </label>
            <input class='form-control' type='tel' placeholder='06 12345678' id='idPhonenumberInput' name='namePhonenumberInput' required><br/>
    
            <label>Straatnaam: </label>
            <input class='form-control' type='text' id='idStreetName' name='nameStreetName' placeholder='Straatnaam' required><br/>
    
            <label>Huisnummer: </label>
            <input class='form-control' type='text' id='idHouseNumber' name='nameHouseNumber' placeholder='Huisnummer' required><br/>
    
            <label>Huisnummer toevoeging: </label>
            <input class='form-control' type='text' id='idHouseNumberAddition' name='nameHouseNumberAddition' placeholder='Huisnummer toevoeging'><br/>
    
            <label>Postcode: </label>
            <input class='form-control' type='text' id='idPostalCode' name='namePostalCode' placeholder='Postcode'><br/>
    
            <label>Plaats: </label>
            <input class='form-control' type='text' id='idCity' name='nameCity' placeholder='Plaats' required><br/>
                </div>
            </div> 
            <div class='row mt-4 mb-4'>
                <div class='col-12 col-lg-12 justify-content-center'>
                    <button type='submit' class='buttonIndexSubmit d-flex justify-content-center align-items-center btn w-100'>
                        <p style='margin: auto;'>Bestel</p>
                    </button>
                </div>
            </div>
            ";
        }

        // ==== Start of Loop ====
        break;
}

# Body
echo ("
<div class='container'>
    <div class='row justify-content-center'>
        <div class='col-lg-5 col-md-8 col-sm-10 col-10 border border-dark rounded'>
            <form class='container-fluid mt-4 pl-5' method='POST'>
                <div class='row'>
                    <div class='col-lg-8 col-md-12 col-sm-12'>
                        $mainPage
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
");

# Footer
Functions::htmlFooter();