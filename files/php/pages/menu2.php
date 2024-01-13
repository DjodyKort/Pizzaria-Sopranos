<?php

// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
$arrayToppings = array('tomat', 'chez', 'beef', 'chez but more');

// ============ Start of Program ============
# Header
Functions::htmlHeader(320);

// Switchers between menu1 and menu2
if (isset($_POST['menu1'])) {
    $pizza = $_POST['pizzaType'];
    if ($_SESSION['boolPreventHeader']) {
        header("Location: ./menu2.php?pizza=$pizza");
        $_SESSION['boolPreventHeader'] = false;
    }
}

if (!isset($_GET['pizza'])) {
    //here needs to come a foreach loop for when all pizza's are added to the database
    echo (" 
    <img src='../../images/pizza.png' height='320px' alt='Responsive image' value='Quatre Formagi'><br/>
    quatre formagi
    <form method='post'>
        <input type='submit' name='menu1' value='submit'>
        <input type='hidden' name='pizzaType' value='quatre formagi'>
    </form>
");

    $_SESSION['boolPreventHeader'] = true;

} else {
    echo ("
    <form method='post'>
    <p>Pizza Bottom</p>
        <select>
            <option value='0'>Small</option>
            <option value='1'>Medium</option>
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
                    <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction($i)'>
                </td>
                <td>
                    <p style='margin: 5px 0 5px 15px;'>" . $arrayToppings[$i] . "</p> 
                </td>
                <td>
                    <p id='counter$i' style='margin: 5px 15px;'>1</p>
                </td>
                <td>
                    <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment($i)'>
                <td>
            </tr>");
    }

    // Add a row for adding a new topping
    echo ("</table>
    <table>
        <tr>
            <td>
                pepperoni
            </td>
            <td colspan='4'>
                <img src='../../images/plus.png' height='25px' alt='Add Topping' style='cursor: pointer;' onclick='addNewTopping(`pepperoni`)'>
            </td>
        </tr>
        <tr>
            <td>
                bacon
            </td>
            <td colspan='4'>
                <img src='../../images/plus.png' height='25px' alt='Add Topping' style='cursor: pointer;' onclick='addNewTopping(`bacon`)'>
            </td>
        </tr>
    </table>
    <input type='submit'>
    </form>
    ");
}

# Footer
Functions::htmlFooter();
?>

<script>
    // Function to increment the counter
    function increment(i, topping) {
        if (!topping) {
            var parameter = i;
        } else {
            var parameter = topping;
        }
        var currentCounter = parseInt(document.getElementById('counter' + parameter).innerText);
        if (currentCounter < 3) {
            var newCounter = currentCounter + 1;
            document.getElementById('counter' + parameter).innerText = newCounter;

        }
    }

    // Function to add a new topping row
    function addNewTopping(topping) {
        var table = document.querySelector('table');

        // Check if the counter element exists
        var counterElement = document.getElementById('counter' + topping);

        // If it doesn't exist, create it with an initial value of 1
        if (!counterElement) {
            var newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td>
                <img src='../../images/minus.png' height='25px' alt='reduction' style='cursor: pointer;' onclick='reduction("${topping}")'>
            </td>
            <td>
                <p style='margin: 5px 0 5px 15px;'>${topping}</p> 
            </td>
            <td>
                <p id='counter${topping}' style='margin: 5px 15px;'>1</p>
            </td>
            <td>
                <img src='../../images/plus.png' height='25px' alt='Increment' style='cursor: pointer;' onclick='increment("${topping}")'>
            </td>`;
            table.appendChild(newRow);
        } else if (counterElement) {


            var currentCounterElement = document.getElementById('counter' + topping);
            console.log(currentCounterElement);
            var currentCounter = parseInt(currentCounterElement.innerText);
            increment(currentCounter, topping);
        }
    }



    function reduction(i) {
        // Get the current value of the counter
        var currentCounterElement = document.getElementById('counter' + i);
        var currentCounter = parseInt(currentCounterElement.innerText);

        if (currentCounter > 0) {
            // Update the counter
            var newCounter = currentCounter - 1;
            currentCounterElement.innerText = newCounter;

            // Check if the counter is now 0, and if so, remove the row
            console.log(i);
            if (newCounter === 0 && typeof i === 'string') {
                var rowToRemove = currentCounterElement.parentNode.parentNode;
                rowToRemove.parentNode.removeChild(rowToRemove);
            }
        }
    }

</script>