<?php
// ============ Imports ============
# Internally
require_once($_SERVER["DOCUMENT_ROOT"].'/files/php/functions.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
Functions::htmlHeader();
echo ("
<div>
    <label for='nameEmailInput'>Email: </label>
    <input type='text' id='idEmailInput' name='nameEmailInput'>
</div>
");

Functions::htmlFooter();
