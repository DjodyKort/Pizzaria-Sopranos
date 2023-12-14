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
    <div class='inputPostcode'>
        <input class='children' type='text' placeholder='vul postcode in'>
    </div>
    <div class='submit'>
        <input  type='submit' placeholder='vul postcode in'>
    </div>
</div>

");

Functions::htmlFooter();