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
        echo($_SESSION['total']);
        echo (" 
        <form method='post'>
            <input type='submit' name='menu1' value='Bestellen'>
            <input type='hidden' name='pizzaID' value='".$row['dishID']."'>
        </form>
        ");
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
    <p>Pizza Bottom</p>
        <select id='sizeSelector' onchange='updatePrice()'>
            <option value='0'>Normaal</option>
            <option value='2'>Groot + €2</option>
            <option value='4'>XXL + €4</option>
        </select>
        <select>
            <option value='0'>tomato</option>
            <option value='1'>bbq</option>
        </select><br/>
        <p>Ingredients</p>
        <table>");
    for ($i = 0; $i < count($arrayToppings); $i++) {
        echo ("
            <tr>
                <td>
                    <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction(`".$arrayToppings[$i]['name'] .'`,'.$arrayToppings[$i]['price']." )'>
                </td>
                <td>
                    <p style='margin: 5px 0 5px 15px;'>" . $arrayToppings[$i]['name'] . "</p> 
                </td>
                <td>
                    <p id='counter".$arrayToppings[$i]['name']."' style='margin: 5px 15px;'>1</p>
                    <input type='hidden' id='".$arrayToppings[$i]['name']."' value='true'>
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

    echo ("</table>
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
            <button onclick='toggleAdditionalToppings()'>Show More Toppings</button>
        </td>
    </tr>
    </table>
    <form method='post'>
        <input type='submit' name='submitPizza' id='submitButton' value='Add to Cart Amount €".$money."' onclick='updateHidden();'>
        <input type='hidden' id='money' name='money' value='".floatval($money)."' />
    </form>
    ");

    if(isset($_POST['submitPizza'])){
        $_SESSION['total'] = $_POST['money'];
        header("Location: menu2.php");
    }
}

# Footer
Functions::htmlFooter();
ob_end_flush();
?>

<script>
    //chnage from int to long
    var total = parseFloat(document.getElementById("money").value);
    // Function to increment the counter
    function increment(topping , toppingPrice) {
        var currentCounter = parseInt(document.getElementById('counter' + topping).innerText);
           
        if (currentCounter < 3) {
            var newCounter = currentCounter + 1;
            document.getElementById('counter' + topping).innerText = newCounter;
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
            <td>
                <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction("${topping}" , ${toppingPrice})'>
            </td>
            <td>
                <p style='margin: 5px 0 5px 15px;'>${topping}</p> 
            </td>
            <td>
                <p id='counter${topping}' style='margin: 5px 15px;'>0</p>
                <input type='hidden' id='${topping}' value='false'>
            </td>
            <td>
                <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment("${topping}" , ${toppingPrice})'>
            </td>`;
            table.appendChild(newRow);
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