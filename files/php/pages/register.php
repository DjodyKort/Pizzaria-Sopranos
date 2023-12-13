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

    <label for='name'>enter your name</label><br>
    <input type='text' name='name' placeholder='bernt'><br>

    <label for='email'>enter your email</label><br>
    <input type='email' name='email' placeholder='pizza@pizza.com'><br>

    <label for='password'>enter your password</label><br>
    <input type='password' name='password'><br>

    <label for='confirmPassword'>confirm password</label><br>
    <input type='password' name='confirmPassword'><br>

    <input type='submit' name='submit'>
</form>
</div>
");

if(!empty($_POST)){
    if(isset($_POST['submit'])){
        if($_POST['password'] != $_POST['confirmPassword']){
            exit;
        }else{
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "INSERT INTO user (name , email , password) VALUES (? , ? , ?)";
            PizzariaSopranosDB::pdoSqlReturnLastID($query, [$_POST['name'], $_POST['email'], $password ]);
            header('Location: ./login.php');
        }
    }
}

Functions::htmlFooter();
