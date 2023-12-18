<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="files/css/style.css">
    <title>Menu</title>
</head>

<body class="MenuBody">
    <section class="MenuContainer">
        <div class="MenuItem1">
            <input type="checkbox" class="invisible" name="MenuItem1" id="MenuItem1" value="Pizza Salami">
            <label for="MenuItem1" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Salami">
                <h1 class="MenuText">Pizza Salami</h1>
            </label>
        </div>
        <div class="MenuItem2">
            <input type="checkbox" class="invisible" name="MenuItem2" id="MenuItem2" value="Pizza Margherita">
            <label for="MenuItem2" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Margherita">
                <h1 class="MenuText">Pizza Margherita</h1>
            </label>
        </div>
        <div class="MenuItem3">
            <input type="checkbox" class="invisible" name="MenuItem3" id="MenuItem3" value="Pizza Funghi">
            <label for="MenuItem3" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Funghi">
                <h1 class="MenuText">Pizza Funghi</h1>
            </label>
        </div>
        <div class="MenuItem4">
            <input type="checkbox" class="invisible" name="MenuItem4" id="MenuItem4" value="Pizza Calzone">
            <label for="MenuItem4" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Calzone">
                <h1 class="MenuText">Pizza Calzone</h1>
            </label>
        </div>
        <div class="MenuItem5">
            <input type="checkbox" class="invisible" name="MenuItem5" id="MenuItem5" value="Pizza Prosciutto">
            <label for="MenuItem5" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Prosciutto">
                <h1 class="MenuText">Pizza Prosciutto</h1>
            </label>
        </div>
        <div class="MenuItem6">
            <input type="checkbox" class="invisible" name="MenuItem6" id="MenuItem6" value="Pizza Quattro Formaggi">
            <label for="MenuItem6" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Quattro Formaggi">
                <h1 class="MenuText">Pizza Quattro Formaggi</h1>
            </label>
        </div>
        <div class="MenuItem7">
            <input type="checkbox" class="invisible" name="MenuItem7" id="MenuItem7" value="Pizza Verdure">
            <label for="MenuItem7" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Verdure">
                <h1 class="MenuText">Pizza Verdure</h1>
            </label>
        </div>
        <div class="MenuItem8">
            <input type="checkbox" class="invisible" name="MenuItem8" id="MenuItem8" value="Pizza Marinara">
            <label for="MenuItem8" class="MenuLabel">
                <img class="PizzaImg" src="./files/img/pizza.png" alt="Pizza Marinara">
                <h1 class="MenuText">Pizza Marinara</h1>
            </label>
        </div>
    </section>

    <section class="CartContainer">
        <div class="CartView">
            <div class="CartOrdercontainer">
                <h1 class="MenuText CartText">Uw winkelwagen</h1>
                <div class="OrderContainer">
                    <h1 class="MenuText CartOrder" id="CartTextField"></h1>
                </div>
            </div>
            <button type="submit" class="CartConfirm">Bestellen</button>
        </div>
    </section>
    <script src="./files/js/menuPage.js"></script>
</body>

</html>