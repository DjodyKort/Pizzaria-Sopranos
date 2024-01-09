<?php
// ============ Imports ============
# Internally
require_once('./files/php/functions.php');
require_once('./files/php/classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
# Header
Functions::htmlHeader();

# POST Request
if(isset($_POST['submit'])){
    header('Location: ./files/php/pages/menu.php');
    exit;
}

# Body
echo("
<div class='box'>
    <h1>Welcome to Pizzaria Sopranos</h1>
    <h2>Choose Takeout or Delivery</h2>
    <form method='POST'>
        <label for='order_type'>Order Type:</label>
        <br>
        <div class='labelDiv'>
            <input type='radio' name='order_type' value='takeout' id='takeout' checked>
            <label for='takeout'>Takeout</label>
            <input type='radio' name='order_type' value='delivery' id='delivery'>
            <label for='delivery'>Delivery</label>
        </div>
        <br>
        <br>
        <label for='address_zipcode'>Address and Zip Code:</label>
        <br>
        <input type='text' class='enterZipText' id='address_zipcode' name='address_zipcode'>
        <br>
        <br>
        <input type='submit' class='enterZipText' name='submit' value='Place Order'>
    </form>
</div>

<style>
    /* Responsive Styles */
    @media only screen and (max-width: 600px) {
        .box {
            width: 90%;
            margin: 0 auto;
        }
        .enterZipText {
            width: 100%;
        }
    }
</style>
");

# Footer
Functions::htmlFooter();