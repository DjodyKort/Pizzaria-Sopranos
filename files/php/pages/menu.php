<?php

// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
$tableDishes = 'dishes';
$tableDefaultToppings = 'defaultToppingRelations';
$tableToppings = 'toppings';

// ============ Start of Program ============
# Header
ob_start();
Functions::htmlHeader(320);
//sql statement for menu1 
$result = PizzariaSopranosDB::pdoSqlReturnArray("SELECT * FROM $tableDishes");

if (!isset($_GET['pizzaID'])) {
    
    //Bootstrap boiler
    echo("
        <div class='container-fluid'>
        <div class='row'>
        <div class='col-8'>   
        <form method='post'>
        <div class='row'>
    ");

    //Generate the data from the database to show all pizza's
    foreach ($result as $row) {
        echo ("
            <div class='col-4 col '>
                    <img src='" . Functions::dynamicPathFromIndex() . "/files/images/pizza.png' class='img-thumbnail' alt='Pizza Image' style='max-width: 100%; height: auto;'>
                    <br/><label>" . $row['name'] . "</label>
                    <input type='submit' name='menu" . $row['dishID'] . "' value='Bestellen'>
                    <input type='hidden' name='pizzaID" . $row['dishID'] . "' value='" . $row['dishID'] . "'>
                
            </div>
        ");
    }
    //exit divs till first row
    echo("
        </div>
        </form>
        </div>
    ");

    //Porcess pizza submit button
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        foreach ($result as $row) {
            if (isset($_POST['menu' . $row['dishID']])) {
                $pizzaID = $_POST['pizzaID' . $row['dishID']];
                if ($_SESSION['boolPreventHeader']) {
                    $_SESSION['boolPreventHeader'] = false;
                    header("Location: ./menu.php?pizzaID=$pizzaID");
                }
            }
        }
    }

    //check if the cart is empty to show all pizza's in current session
    if (empty($_SESSION['cart'])) {
        
    }else{
        //start cart bootstrap
        echo("<div class='col-4'>");
        for ($i = 0; $i <= count($_SESSION['cart']) - 1; $i++) {

            //set pizza name in cart
            echo("
                <div class='list-group-item'>
                <p class='mb-1'>" . $_SESSION['cart'][$i]['Pizza'] . " - " . $_SESSION['cart'][$i]['Size'] . "</p>
            ");
        
            //set Dishtotal per pizza
            echo("
                <p class='mb-1'>Pizza Price: €" . $_SESSION['cart'][$i]['DishTotal'] . "</p>
                <form method='post' class='form-inline'>
                    <button type='submit' class='btn  mr-1' name='update'>Wijzigen</button>
                    <button type='submit' class='btn ' name='delete'>Verwijderen</button>
                    <input type='hidden' name='arrayKey' value='" . $i . "'>
                </form>
            ");
        }
        //print full price
        echo("
            <p>Total Price: €" . $_SESSION['total'] . "</p>
            </div>
        ");
        
       //Exit bootstrap
        echo("</div></div>");

        //delete key from array and update total correspondingly 
        if(isset($_POST['delete'])){
            if(isset($_POST['arrayKey'])){
                $_SESSION['total'] -= $_SESSION['cart'][$_POST['arrayKey']]['DishTotal'];
                //delete the array key
                unset($_SESSION['cart'][$_POST['arrayKey']]);
                //order the array keys so they start at 0
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                header("Refresh:0");
            }
        }
        //Go to the update page
        if(isset($_POST['update'])){
            if(isset($_POST['arrayKey'])){
                header("Location: ./menu.php?pizzaID=".$_SESSION['cart'][$_POST['arrayKey']]['Id']."&arrayKey=".$_POST['arrayKey']."");
            }
        }
    }

    $_SESSION['boolPreventHeader'] = true;

} else {
    //Get value
    $pizzaID = $_GET['pizzaID'];

    //get the topping data from the database
    $arrayToppings = PizzariaSopranosDB::pdoSqlReturnArray("SELECT t.name , t.price
        FROM $tableDefaultToppings dt
        JOIN $tableToppings t ON dt.toppingID = t.toppingID
        WHERE dt.dishID = $pizzaID
    ");

    //intialize var's
    $arrToppings = array();
    $arrMoney = array();
    $money = 0;
    
    //bootstrap boiler + part of form
    echo("
        <div class='container'>
        <div class='row justify-content-center'>
        <div class='col-10   border border-dark rounded'>
        <div class='container-fluid mt-4'>
        <div class='row'>
        <div class='col-md-6'>
            <img src='" . Functions::dynamicPathFromIndex() . "/files/images/QuattroFormaggi.jpeg' class='img-fluid' alt='Image'>
        </div>
        <div class='col-md-6'>
        <form method='post'>
        <p>Pizza Bottom</p>
    ");

    //Update page for updating the pizza
    if (isset($_GET['arrayKey'])) {
        // Display form for updating with existing data
        echo ("
                <div class='form-group'>
                    <select id='sizeSelector' onchange='updatePrice()' name='size' class='form-control' >
                        <option value='0'" . ($_SESSION['cart'][$_GET['arrayKey']]['Size'] == 'Normaal' ? " selected" : "") . ">Normaal</option>
                        <option value='2'" . ($_SESSION['cart'][$_GET['arrayKey']]['Size']== 'Groot' ? " selected" : "") . ">Groot + €2</option>
                        <option value='4'" . ($_SESSION['cart'][$_GET['arrayKey']]['Size']== 'XXL' ? " selected" : "") . ">XXL + €4</option>
                    </select>
                </div>
                <div class='form-group'>
                <select name='Sauce' class='form-control'>
                    <option value='Tomato'" . ($_SESSION['cart'][$_GET['arrayKey']]['Sauce'] == 'Tomato' ? " selected" : "") . ">tomato</option>
                    <option value='BBQ'" . ($_SESSION['cart'][$_GET['arrayKey']]['Sauce'] == 'BBQ' ? " selected" : "") . ">bbq</option>
                </select>
                </div>
                <p>Ingredients</p>
                <table>
                <tbody id='tbody'>");
    
        for ($i = 0; $i < count($_SESSION['cart'][$_GET['arrayKey']]['Toppings']); $i++) {
            echo ("
                <tr>
                    <td>
                        <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction(`" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . '`,' . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['price'] . " )'>
                    </td>
                    <td>
                        <p style='margin: 5px 0 5px 15px;' >" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "</p> 
                    </td>
                    <td>
                        <p id='counter" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' style='margin: 5px 15px;'>" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['quantity'] . "</p>
                        <input type='hidden' name='toppingQuantities[]' id='amount" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' value='" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['quantity'] . "'>
                        <input type='hidden' name='selectedToppings[]' value='" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "'>
                        <input type='hidden' id='" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' name='" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' value='" . ($_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['orignal'] == 'true' ? "true" : "false") . "'>
                        <input type='hidden' id='amount" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' name='amount" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . "' value='" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['quantity'] . "'>
                    </td>
                    <td>
                        <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment(`" . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['name'] . '`,' . $_SESSION['cart'][$_GET['arrayKey']]['Toppings'][$i]['price'] . " )'>
                    <td>
                </tr>");
        }
    }
    //page for adding pizza to the cart
    else{
            echo ("
        
            <select id='sizeSelector' onchange='updatePrice()' name='size' >
                <option value='0'>Normaal</option>
                <option value='2'>Groot + €2</option>
                <option value='4'>XXL + €4</option>
            </select>
            <select name = 'Sauce'>
                <option value='Tomato'>tomato</option>
                <option value='BBQ'>bbq</option>
            </select><br/>
            <p>Ingredients</p>
            <table>
            <tbody id='tbody'>");
        for ($i = 0; $i <= count($arrayToppings) - 1; $i++) {
            echo ("
                <tr>
                    <td>
                        <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction(`".$arrayToppings[$i]['name'] .'`,'.$arrayToppings[$i]['price']." )'>
                    </td>
                    <td>
                        <p style='margin: 5px 0 5px 15px;' >" . $arrayToppings[$i]['name'] . "</p> 
                    </td>
                    <td>
                        <p id='counter".$arrayToppings[$i]['name']."' style='margin: 5px 15px;'>1</p>
                        <input type='hidden' name='toppingQuantities[]' id='amount".$arrayToppings[$i]['name']."' value='1'>
                        <input type='hidden' name='selectedToppings[]' value='".$arrayToppings[$i]['name']."'>
                        <input type='hidden' id='".$arrayToppings[$i]['name']."' name='".$arrayToppings[$i]['name']."' value='true'>
                        <input type='hidden' id='amount".$arrayToppings[$i]['name']."' name='amount".$arrayToppings[$i]['name']."' value='1'>
                    </td>
                    <td>
                        <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment(`".$arrayToppings[$i]['name'] .'`,'.$arrayToppings[$i]['price'].")'>
                    <td>
                </tr>");
        }
        
        
    }
    //exit table
    echo ("
        </tbody>
        </table>
        <div class='scrollable-table' style='max-height: 200px; overflow-y: auto;'>
        <table> 
    ");
    //get price of the pizza from database
    $arrMoney = PizzariaSopranosDB::pdoSqlReturnArray("SELECT `price` FROM $tableDishes WHERE `dishID` = $pizzaID");
    $money += $arrMoney[0]['price'];
    //get all toppings from the database
    $arrToppings = PizzariaSopranosDB::pdoSqlReturnArray("SELECT * FROM $tableToppings ORDER BY `name` ASC");
    
    //go trough all the toppings and make them addable
    foreach ($arrToppings as $index => $topping) {
        if ($index < 3) {
            // Display the first 3 toppings directly
            echo("
                <tr>
                    <td>
                    ".$topping['name']."
                    </td>
                    <td colspan='4'>
                        <img src='../../images/plus.png' height='25px' alt='Add Topping' style='cursor: pointer;' onclick='addNewTopping(`".$topping['name']."` , ".$topping['price'].")'>
                    </td>
                </tr>
            ");
        } else {
            // Display a dropdown for additional toppings
            echo("
                <tr class='additional-toppings' style='display: none;'>
                    <td>
                        ".$topping['name']."
                    </td>
                    <td colspan='4'>
                        <img src='../../images/plus.png' height='25px' alt='Add Topping' style='cursor: pointer;' onclick='addNewTopping(`".$topping['name']."` , ".$topping['price'].")'>
                    </td>
                </tr>
            ");
        }
    }

    //button for showing all the toppings Caps at 3
    echo("
        <tr id='toggleRow'>
        <td colspan='5' >
            <button onclick='toggleAdditionalToppings()' name='buttonAdditonalToppings' type='button' class='btn btn-secondary'>Show More Toppings</button>
        </td>
        </tr>
        </table>
        </div>
    ");

    //Check if the page is for update or create and handle the request.
    if(!isset($_GET['arrayKey'])){
        echo("
            <input type='submit' name='submitPizza' id='submitButton' value='Add to Cart Amount €".$money."' onclick='updateHidden();' class='btn btn-primary'>
            <input type='hidden' id='money' name='money' value='".floatval($money)."' />
        ");
    }else{
        echo("    
            <input type='submit' name='submitPizza' id='submitButton' value='Add to Cart Amount €".$_SESSION['cart'][$_GET['arrayKey']]['DishTotal']."' onclick='updateHidden();' class='btn btn-primary'>
            <input type='hidden' id='money' name='money' value='".floatval($_SESSION['cart'][$_GET['arrayKey']]['DishTotal'])."' />
        ");
    }

    //exit bootstrap div's
    echo("
        <div class='mt-2'>
        </div>
        </form>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        ");

    if(isset($_POST['submitPizza'])){
        $selectedToppings = $_POST['selectedToppings'];
        $toppingQuantities = $_POST['toppingQuantities'];
        $pizzaName = PizzariaSopranosDB::pdoSqlReturnArray("SELECT `name` FROM $tableDishes WHERE `dishID` = " . $_GET['pizzaID']);
        // Loop through the selected toppings and their quantities
        $priceTopping = 0;
        $dishTotal = 0;
        for ($i = 0; $i < count($selectedToppings); $i++) {
            $toppingName = $selectedToppings[$i];
            $quantity = $toppingQuantities[$i];
            
            //check if html page wasnt changed by user
            $arrCheck = PizzariaSopranosDB::pdoSqlReturnArray("SELECT `name` , `price` FROM $tableToppings WHERE `name` = '$toppingName'");
            if(empty($arrCheck)){
                $toppingName = '';
            }
            //check if quantity is in between the bounds
            if($quantity > 3){
                $quantity = 1;
            }
            
            
            // Add the topping data to the array
            if(!empty($toppingName)){
                
                //check if its a standard topping and if its quantity > then 1
                $found = false;
                foreach ($arrayToppings as $topping) {
                    if ($topping['name'] === $toppingName) {
                        $found = true;
                        break;
                    }
                    
                }
                //check if the standard toppings are 1 or less 
                if($found && $quantity <= 1){
                    $priceTopping = $arrCheck[0]['price'];
                }//if its more then 1 do quantity - 1 so the total
                else if($found && $quantity >= 1){
                    $priceTopping = ($quantity - 1) * $arrCheck[0]['price'];
                    $dishTotal += $priceTopping;
                }//do standard calculation for toppings
                else{
                    $priceTopping = $quantity * $arrCheck[0]['price'];
                    $dishTotal += $priceTopping;
                }
                
                $toppingData[] = array(
                    'name' => $toppingName,
                    'quantity' => $quantity,
                    'price' => $priceTopping,
                    'orignal' => $_POST[$toppingName]
                );
            }
        }
        //set size
        $size = '';
        $dishTotal += $_POST['size'];
        if($_POST['size'] == 0){
            $size = 'Normaal';
        }
        else if($_POST['size'] == 2){
            $size = 'Groot';
        }else{
            $size = 'XXL';
        }
        $dishTotal += $money;
        if(!isset($_GET['arrayKey'])){
            $_SESSION['total'] += $dishTotal;
            //put data into current SESSION var
            $_SESSION['cart'][] = array(
                "Pizza" => $pizzaName[0]['name'],
                "Id" => $_GET['pizzaID'],
                "Size" => $size,
                "Sauce" => $_POST['Sauce'],
                "DishTotal" => $dishTotal,
                "Toppings" => $toppingData
            );
        }else{
            //echo($_SESSION['cart'][$_GET['arrayKey']]['DishTotal'] > "<br/>");
            $_SESSION['total'] -= $_SESSION['cart'][$_GET['arrayKey']]['DishTotal'];
            //echo($_SESSION['total']);
            $_SESSION['total'] += $dishTotal;
            //echo($_SESSION['total']);
            $_SESSION['cart'][$_GET['arrayKey']] = array(
                "Pizza" => $pizzaName[0]['name'],
                "Id" => $_GET['pizzaID'],
                "Size" => $size,
                "Sauce" => $_POST['Sauce'],
                "DishTotal" => $dishTotal,
                "Toppings" => $toppingData
            );
        }
        
        header("Location: menu.php");
    }
}

# Footer
Functions::htmlFooter();
ob_end_flush();
?>

<script>
    //change from int to long
    var total = parseFloat(document.getElementById("money").value);
    // Function to increment the counter
    function increment(topping, toppingPrice) {
        var currentCounter = parseInt(document.getElementById('counter' + topping).innerText);
        var inputHidden = document.getElementById(topping).value;

        if (inputHidden === "true" && (currentCounter === 0)) {
            // Do not update the total price, but still increment the counter
            var newCounter = currentCounter + 1;
            document.getElementById('counter' + topping).innerText = newCounter;
            document.getElementById('amount' + topping).value = newCounter;
            return;
        }

        if (currentCounter < 3) {
            var newCounter = currentCounter + 1;
            document.getElementById('counter' + topping).innerText = newCounter;
            document.getElementById('amount' + topping).value = newCounter;
            total += toppingPrice;

            var roundedNumber = parseFloat(total).toFixed(2);
            document.getElementById("submitButton").value = 'Add to Cart Amount €' + roundedNumber;
        }
    }



    // Function to add a new topping row
    function addNewTopping(topping, toppingPrice) {
        var table = document.querySelector('table');
        var counterElement = document.getElementById('counter' + topping);
        if (!counterElement) {
            var newRow = document.createElement('tr');
            newRow.innerHTML = `
            <tr>
                <td>
                    <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction("${topping}" , ${toppingPrice})'>
                </td>
                <td>
                    <p style='margin: 5px 0 5px 15px;'>${topping}</p> 
                </td>
                <td>
                    <p id='counter${topping}' style='margin: 5px 15px;'>0</p>
                    <input type='hidden' name='toppingQuantities[]' id='amount${topping}' value='0'>
                    <input type='hidden' name='selectedToppings[]' value='${topping}'>
                    <input type='hidden' id='${topping}' value='false'>
                    <input type='hidden' id='amount${topping}' name='amount${topping}' value='1'>
                </td>
                <td>
                    <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment("${topping}" , ${toppingPrice})'>
                </td>
            </tr>`;
            var tbody = table.querySelector('tbody');
            tbody.appendChild(newRow)
            increment(topping, toppingPrice)
        } else if (counterElement) {    
            var currentCounterElement = document.getElementById('counter' + topping);
            var currentCounter = parseInt(currentCounterElement.innerText);
            increment(topping, toppingPrice);
        }
    }


    //function for reducing the amount of an counter
    function reduction(topping, toppingPrice) {
        var currentCounterElement = document.getElementById('counter' + topping);
        var currentCounter = parseInt(currentCounterElement.innerText);
        var inputHidden = document.getElementById(topping).value;
        if (inputHidden === "true" && currentCounter === 0) {
        // Do not update the total price or decrement the counter below 0
            return;
        }
        if (inputHidden === "true" && (currentCounter === 0 || currentCounter === 1)) {
            // Do not update the total price, but still decrement the counter
            var newCounter = currentCounter - 1;
            currentCounterElement.innerText = newCounter;
            document.getElementById('amount' + topping).value = newCounter;
            return;
        }

        if (currentCounter > 0) {
            var newCounter = currentCounter - 1;
            currentCounterElement.innerText = newCounter;
            document.getElementById('amount' + topping).value = newCounter;
            total -= toppingPrice;

            var roundedNumber = parseFloat(total).toFixed(2);
            document.getElementById("submitButton").value = 'Add to Cart Amount €' + roundedNumber;

            if (newCounter === 0 && inputHidden == 'false') {
                var rowToRemove = currentCounterElement.parentNode.parentNode;
                rowToRemove.parentNode.removeChild(rowToRemove);
            }
        }
    }


    var sizeSelector = document.getElementById('sizeSelector');
    var previousSizeValue = sizeSelector.options[sizeSelector.selectedIndex].value;

     function updatePrice() {
        var selectedValue = sizeSelector.options[sizeSelector.selectedIndex].value;
        console.log(total);
        // Subtract the cost of the previous size
        total -= parseFloat(previousSizeValue);

        // Add the cost of the new size
        total += parseFloat(selectedValue);

        // Update the displayed total
        var roundedNumber = parseFloat(total).toFixed(2);
        
        document.getElementById("submitButton").value = 'Add to Cart Amount €' + roundedNumber;

        // Update the previous size value for the next change
        previousSizeValue = selectedValue;
    }

    function toggleAdditionalToppings() {
        var additionalToppings = document.getElementsByClassName('additional-toppings');

        for (var i = 0; i < additionalToppings.length; i++) {
            additionalToppings[i].style.display = (additionalToppings[i].style.display === 'none') ? 'table-row' : 'none';
        }
    }

    function updateHidden(){
        document.getElementById("money").value = parseFloat(total).toFixed(2); 
    }
</script>