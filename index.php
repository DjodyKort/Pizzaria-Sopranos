<?php
// ============ Imports ============
# Internally
require_once('./files/php/functions.php');
require_once('./files/php/classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============


Functions::htmlHeader();

echo("
<div class='box'>
    <div class='buttons'>
        <button class='children' >Takeout</button>
        <button class='children' >Delivery</button>
        <button class='children' >Locations</button>
    </div>
    <form method='post'>
        <div class='inputPostcode'>
            <input class='children' type='text' placeholder='vul postcode in'>
        </div>
        <div class='submit'>
            <input  name='submit' type='submit' placeholder='vul postcode in'>
        </div>
    </form>
</div>
");

if(isset($_POST['submit'])){
    header('Location: ./files/php/pages/menu.php');
    exit;
}

Functions::htmlFooter();