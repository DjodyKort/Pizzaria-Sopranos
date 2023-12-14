<?php 

// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
Functions::htmlHeader();


echo("
<div class='box'>
<form method='post'>

    <label for='name'>enter your name</label><br>
    <input type='text' name='name' placeholder='bernt'><br>");

if($_SESSION['error'] != ""){
    echo $_SESSION['error'];
    unset($_SESSION['error']);
}

echo("
    <label for='email'>enter your email</label><br>
    <input type='email' name='email' placeholder='pizza@pizza.com'><br>

    <label for='password'>enter your password</label><br>
    <input type='password' name='password'><br>");

if($_SESSION['error'] != ""){
    echo $_SESSION['error'];
}

echo("
    <label for='confirmPassword'>confirm password</label><br>
    <input type='password' name='confirmPassword'><br>

    <input type='submit' name='submit'>
</form>
</div>
");

if(!empty($_POST)){
    
    if(isset($_POST['submit'])){
        $email = $_POST['email'];
        $query = "SELECT * FROM users WHERE `email` = ?";
        $array = PizzariaSopranosDB::pdoSqlReturnArray($query, [$email]);
        if( !array_key_exists("email", $array)){
            if($_POST['password'] != $_POST['confirmPassword']){
                //echo "passwords are not the same";
            }else{
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $query = "INSERT INTO users (name , email , password) VALUES (? , ? , ?)";
                PizzariaSopranosDB::pdoSqlReturnLastID($query, [$_POST['name'], $_POST['email'], $password ]);
                header('Location: ./login.php');
            }
        }else{
            //echo  "email already registered";
        }
    }
}

Functions::htmlFooter();
?>
