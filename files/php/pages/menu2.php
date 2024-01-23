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

//initalize $_SESSION var
// Switchers between menu1 and menu2
if (isset($_POST['menu1'])) {
    $pizzaID = $_POST['pizzaID'];
    if ($_SESSION['boolPreventHeader']) {
        header("Location: ./menu2.php?pizzaID=$pizzaID");
        $_SESSION['boolPreventHeader'] = false;
    }
}

if (!isset($_GET['pizzaID'])) {
    //here needs to come a foreach loop for when all pizza's are added to the database
    foreach($result as $row){
        echo (" 
        <form method='post'>
            <input type='submit' name='menu1' value='Bestellen'>
            <input type='hidden' name='pizzaID' value='".$row['dishID']."'>
        </form>
        ");
    }

    if (empty($_SESSION['cart'])) {
        
    }else{
        for($i = 0;  $i <= count($_SESSION['cart']) -1; $i++){
            echo("<br/>".$_SESSION['cart'][$i]['Pizza'] .  " " . $_SESSION['cart'][$i]['Size'] . "<br/>");
            foreach($_SESSION['cart'][$i]['Toppings'] as $toppingData){
                print_r($toppingData['name'] . " ");
                print_r($toppingData['price']);
                echo("<br/>");
            }
        }
        echo("Total Prijs: " . $_SESSION['total']);
    }
    
    if(!empty($_SESSION['orderItems'])){
        print_r($_SESSION['orderedItems']);
    }

    $_SESSION['boolPreventHeader'] = true;

} else {
    //Get value
    $pizzaID = $_GET['pizzaID'];
    $arrayToppings = PizzariaSopranosDB::pdoSqlReturnArray("SELECT t.name , t.price
        FROM $tableDefaultToppings dt
        JOIN $tableToppings t ON dt.toppingID = t.toppingID
        WHERE dt.dishID = $pizzaID
    ");
    echo ("
    <form method='post'>
    <p>Pizza Bottom</p>
        <select id='sizeSelector' onchange='updatePrice()' name='size' >
            <option value='0'>Normaal</option>
            <option value='2'>Groot + €2</option>
            <option value='4'>XXL + €4</option>
        </select>
        <select>
            <option value='0'>tomato</option>
            <option value='1'>bbq</option>
        </select><br/>
        <p>Ingredients</p>
        <table>
        <tbody id='tbody'>");
    for ($i = 0; $i < count($arrayToppings); $i++) {
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

    // Add a row for adding a new topping
    
    $arrMoney = PizzariaSopranosDB::pdoSqlReturnArray("SELECT `price` FROM $tableDishes WHERE `dishID` = $pizzaID");
    $money = $arrMoney[0]['price'];
    
    $arrToppings = PizzariaSopranosDB::pdoSqlReturnArray("SELECT * FROM $tableToppings ORDER BY `name` ASC");
    
    echo ("</tbody></table>
    <table> ");
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
        echo("
    <tr id='toggleRow'>
        <td colspan='5'>
            <button onclick='toggleAdditionalToppings()' name='buttonAdditonalToppings' type='button'>Show More Toppings</button>
        </td>
    </tr>
    </table>
    
    <input type='submit' name='submitPizza' id='submitButton' value='Add to Cart Amount €".$money."' onclick='updateHidden();'>
    <input type='hidden' id='money' name='money' value='".floatval($money)."' />
    </form>
    ");

    if(isset($_POST['submitPizza'])){
        $selectedToppings = $_POST['selectedToppings'];
        $toppingQuantities = $_POST['toppingQuantities'];
        $pizzaName = PizzariaSopranosDB::pdoSqlReturnArray("SELECT `name` FROM $tableDishes WHERE `dishID` = " . $_GET['pizzaID']);
        // Loop through the selected toppings and their quantities
        for ($i = 0; $i < count($selectedToppings); $i++) {
            $toppingName = $selectedToppings[$i];
            $quantity = $toppingQuantities[$i];
            $priceTopping = 0;
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
                    $_SESSION['total'] += $priceTopping;
                    echo($_SESSION['total'] . " > 1<br/>");

                }//do standard calculation for toppings
                else{
                    $priceTopping = $quantity * $arrCheck[0]['price'];
                    $_SESSION['total'] += $priceTopping;
                    echo($_SESSION['total'] . " no <br/>");
                }
                
                
                $toppingData[] = array(
                    'name' => $toppingName,
                    'quantity' => $quantity,
                    'price' => $priceTopping
                );
            }
        }
        //set size
        $size = '';
        $_SESSION['total'] += $_POST['size'];
        if($_POST['size'] == 0){
            $size = 'Normaal';
        }
        else if($_POST['size'] == 2){
            $size = 'Groot';
        }else{
            $size = 'XXL';
        }
        $_SESSION['total'] += $money;
        //put data into current SESSION var
        echo($_SESSION['total'] . " <br/>");
        $_SESSION['cart'][] = array(
            "Pizza" => $pizzaName[0]['name'],
            "Size" => $size,
            "Toppings" => $toppingData
        );
        header("Location: menu2.php");
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
    function increment(topping , toppingPrice) {
        var currentCounter = parseInt(document.getElementById('counter' + topping).innerText);
           
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
    function reduction(topping , toppingPrice) {
        var currentCounterElement = document.getElementById('counter' + topping);
        var currentCounter = parseInt(currentCounterElement.innerText);
        var inputHidden = document.getElementById(topping).value; 
        console.log(typeof inputHidden);
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