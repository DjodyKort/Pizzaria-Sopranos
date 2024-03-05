<?php
// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
$tableAddresses = 'addresses';
$tableOrders = 'orders';
$tableOrderDishes = 'orderDishes';

// ============ Start of Program ============
# Header
Functions::htmlHeader(320);

# POST Request

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //check if chosenAddress is clicked
    $dateTime = date('Y-m-d, H:i');
    if(isset($_POST['chosenAddress']) && $_SESSION['loggedIn']){
        # SQL
        
        $query = "INSERT INTO $tableOrders (userID, orderStatus, addressID, billingAddressID, isGuest, dateOrdered) VALUES (?, ?, ?, ?, ?, ?)";
        $arrPrepare = [$_SESSION['userID'], 0, $_POST['addressId'], $_POST['billingAddressId'], 0 , $dateTime ];
        
        # Praepare
        $result = PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPrepare);
    }else{
        # SQL
        $query = "INSERT INTO $tableOrders (orderStatus, isGuest, streetName, houseNumber, houseNumberAddition, postalCode, city, dateOrdered) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $arrPrepare = [0, 1, $_POST['nameStreetName'], $_POST['nameHouseNumber'], $_POST['nameHouseNumberAddition'], $_POST['namePostalCode'], $_POST['nameCity'] , date('Y-m-d H:i')];
        # Praepare
        $result = PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPrepare);
    
    }
    # SQL
    $query = "SELECT orderID FROM $tableOrders ORDER BY orderID DESC LIMIT 1";
    $result = PizzariaSopranosDB::pdoSqlReturnArray($query);

    foreach ($_SESSION['cart'] as $pizza) {
        $query = "INSERT INTO $tableOrderDishes (orderID, dishID, toppings) VALUES (?, ?, ?)";
        $toppingString = '';

        foreach ($pizza['Toppings'] as $topping) {
            $toppingString .= $topping['name'] . ', ';
        }

        $arrPrepare = [$result[0]['orderID'], $pizza['Id'], rtrim($toppingString, ', ')];

        // Prepare and execute the SQL query inside the loop
        PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPrepare);
    }

}
#begin bootstrap for page
echo ("<div class='container'>
<div class='row justify-content-center'>
    <div class='col-lg-5 col-md-8 col-sm-10 col-10 border border-dark rounded'>
        <form class='container-fluid mt-4 pl-5' method='post'>
            <div class='row'>
                <div class='col-lg-8 col-md-12 col-sm-12'>
                ");
if (isset($_SESSION['loggedIn'])) { 
    $result = PizzariaSopranosDB::pdoSqlReturnArray("SELECT addresses.*, billing.billingAddressID
    FROM $tableAddresses addresses
    LEFT JOIN billingAddresses billing ON addresses.userID = billing.userID
    WHERE addresses.userID = {$_SESSION['userID']}"
    );
    foreach ($result as $row) {
        echo("
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
                <button type='submit' class='btn btn-outline-success w-100' name='chosenAddress'>Kies Address</button>
            </div>
        </div>
        
        <br/>
        ");
    }
} else {
    echo("
        <label>E-mailadres </label>
        <input class='form-control' type='email' id='idEmailInput' placeholder='Sopranos@Sopranos.nl' name='nameEmailInput' required><br/>

        <label>Telefoon Nummer </label>
        <input class='form-control' type='tel' placeholder='06 12345678' id='idPhonenumberInput' name='namePhonenumberInput' required><br/>

        <label>Straatnaam: </label>
        <input class='form-control' type='text' id='idStreetName' name='nameStreetName' placeholder='Straatnaam' required><br/>

        <label'>Huisnummer: </label>
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
    ");
}
//end bootstrap for page
echo("                       
</div>
</div>
</form>
</div>
</div>
</div>");


Functions::htmlFooter();