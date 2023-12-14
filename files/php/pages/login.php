<?php 

// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
Functions::htmlHeader();

echo("
<div class='box'>
<form method='post'>

    <label for='email'>enter your email</label><br>
    <input type='email' name='email' placeholder='pizza@pizza.com'><br>

    <label for='password'>enter your password</label><br>
    <input type='password' name='password'><br>

    <input type='submit' name='submit'>
</form>
</div>
");

if(!empty($_POST)){
    if(isset($_POST['submit'])){
        $email = $_POST['email'];

        $query = "SELECT * FROM user WHERE `email` = ?";

        $array = PizzariaSopranosDB::pdoSqlReturnArray($query, [$email]);
        print_r($array[0]['password'] . "<br/>");
        if (password_verify('1234', $array[0]['password'])) {
            echo "success";
        } else {
            echo "Invalid email or password";
        }
    }
}

Functions::htmlFooter();