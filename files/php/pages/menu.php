<?php
// ============ Imports ============
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
# Strings
$currentPage = $_GET['page'] ?? '';

// ============ Start of Program ============
# Header
Functions::htmlHeader(320);

# Dynamic POST Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ======== Start of POST Request ========
    switch ($currentPage) {
        # Customize dish
        case ConfigData::$mainMenuPages['customizedish']:
            // ==== Declaring Variables ====
            # == GET ==
            $dishID = $_GET['dishID'] ?? '';
            $cartItemIndex = $_GET['cartItemIndex'] ?? '';

            # == POST ==
            $size = $_POST['size'] ?? '';
            $sauce = $_POST['sauce'] ?? '';
            $arrToppings = $_POST['nameToppingSlider'] ?? '';

            # == Strings ==
            # SQL
            $queryGetDishData = "SELECT * FROM ".ConfigData::$dbTables['dishes']." WHERE ".ConfigData::$dbKeys['dishes']['id']." = ?";
            $queryGetDishToppings = "SELECT * FROM ".ConfigData::$dbTables['toppings']." WHERE ".ConfigData::$dbKeys['toppings']['id']." IN (".implode(',', array_fill(0, count($arrToppings), '?')).")";
            $queryGetDishSizes = "SELECT * FROM ".ConfigData::$dbTables['dishSizes']." WHERE ".ConfigData::$dbKeys['dishSizes']['id']." = ?";
            $queryGetDishSauces = "SELECT * FROM ".ConfigData::$dbTables['dishSauces']." WHERE ".ConfigData::$dbKeys['dishSauces']['id']." = ?";
            $queryGetDefaultToppings = "SELECT ".ConfigData::$dbKeys['defaultToppingRelations']['toppingID']." FROM ".ConfigData::$dbTables['defaultToppingRelations']." WHERE ".ConfigData::$dbKeys['defaultToppingRelations']['dishID']." = ?;";

            # == Arrays ==
            $arrayKeys = array_keys($arrToppings);
            $arrDishData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishData, [$dishID]);
            $arrToppingsData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishToppings, $arrayKeys);
            $arrSizeData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSizes, [$size]);
            $arrSauceData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSauces, [$sauce]);
            $arrDefaultToppingsIds = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDefaultToppings, [$dishID]); $arrDefaultToppingsIds = array_column($arrDefaultToppingsIds, ConfigData::$dbKeys['defaultToppingRelations']['toppingID']);

            // ==== Start of Program ====
            # Calculating the total price
            $intTotalPrice = $arrDishData[0][ConfigData::$dbKeys['dishes']['price']];

            # Adding the price of the size to the total price
            $intTotalPrice += $arrSizeData[0][ConfigData::$dbKeys['dishSizes']['price']];

            # Adding the price of the sauce
            $intTotalPrice += $arrSauceData[0][ConfigData::$dbKeys['dishSauces']['price']];

            # Adding the price of the toppings
            foreach ($arrToppings as $toppingID => $toppingAmount) {
                // ==== Declaring Variables ====
                # == Ints ==
                $toppingPrice = $arrToppingsData[array_search($toppingID, $arrayKeys)][ConfigData::$dbKeys['toppings']['price']];

                // ==== Start of Program ====
                # Check if the topping is a default topping or not
                if (in_array($toppingID, $arrDefaultToppingsIds)) {
                    # If the topping amount is more than 1, add the price of extra toppings to the total
                    if ($toppingAmount > 1) {
                        $intTotalPrice += ($toppingAmount - 1) * $toppingPrice;
                    }
                } else {
                    $intTotalPrice += $toppingAmount * $toppingPrice;
                }
            }

            # Making the order and adding it to the cart
            $arrOrder = [
                'dishID' => $dishID,
                'name' => $arrDishData[0][ConfigData::$dbKeys['dishes']['name']],
                'size' => $size,
                'sauce' => $sauce,
                'toppings' => array_filter($arrToppings),
                'dishTotal' => $intTotalPrice
            ];

            # Adding the order to the cart
            if ($cartItemIndex == "") {
                $_SESSION['cart'][] = $arrOrder;
            }
            else {
                $_SESSION['cart'][$cartItemIndex] = $arrOrder;
            }

            # Redirecting back to the menu
            header("Location: ./menu.php");
            break;

        # Cart
        case ConfigData::$mainMenuPages['cart']:
            // ==== Declaring Variables ====
            # == Strings ==
            # SQL

            // ==== Start of Program ===
            header("Location: ./order.php");
            break;

        default:
            // ==== Declaring Variables ====
            # Strings
            $removeCartItemIndex = $_POST['removeCartItem'] ?? '';

            // ==== Start of Program ===
            # Removing the item from the cart
            if (isset($removeCartItemIndex)) {
                unset($_SESSION['cart'][$removeCartItemIndex]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            break;
    }
}

# Dynamic HTML
$mainPage = '';
switch ($currentPage) {
    # Customize dish
    case ConfigData::$mainMenuPages['customizedish']:
        // ==== Declaring Variables ====
        # == GET ==
        $dishID = $_GET['dishID'] ?? '';
        $cartItemIndex = $_GET['cartItemIndex'] ?? '';

        # == Strings ==
        # ConfigData
        $strDefaultMediaPath = Functions::dynamicPathFromIndex().ConfigData::$defaultMediaPath;

        # SQL
        $queryGetDishData = "SELECT * FROM ".ConfigData::$dbTables['dishes']." WHERE ".ConfigData::$dbKeys['dishes']['id']." = ?";
        $queryGetDishMedia = "SELECT * FROM ".ConfigData::$dbTables['media']." WHERE ".ConfigData::$dbKeys['media']['dishID']." = ?";
        $queryGetDefaultToppings = "SELECT ".ConfigData::$dbKeys['defaultToppingRelations']['toppingID']." FROM ".ConfigData::$dbTables['defaultToppingRelations']." WHERE ".ConfigData::$dbKeys['defaultToppingRelations']['dishID']." = ?;";
        $queryGetToppings = "SELECT * FROM ".ConfigData::$dbTables['toppings'];
        $queryGetDishSizes = "SELECT * FROM ".ConfigData::$dbTables['dishSizes'];
        $queryGetDishSauces = "SELECT * FROM ".ConfigData::$dbTables['dishSauces'];

        # == Arrays ==
        $arrDishData = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishData, [$dishID]);
        $arrDishMedia = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishMedia, [$dishID]);
        $arrDefaultToppingsIds = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDefaultToppings, [$dishID]); $arrDefaultToppingsIds = array_column($arrDefaultToppingsIds, ConfigData::$dbKeys['defaultToppingRelations']['toppingID']);
        $arrToppings = PizzariaSopranosDB::pdoSqlReturnArray($queryGetToppings);
        $arrDishSizes = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSizes);
        $arrDishSauces = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishSauces);

        # == HTML ==
        $htmlSizeSelector = "";
        foreach ($arrDishSizes as $index => $size) {
            // ==== Declaring Variables ====
            # == Dynamic Strings ==
            if ($cartItemIndex == "") {
                $checked = array_key_first($arrDishSizes) == $index ? "checked" : "";
            }
            else {
                // ==== Declaring Variables ====
                # == Strings ==
                # SQL
                $queryGetCartItemSize = "SELECT ".ConfigData::$dbKeys['dishSizes']['id']." FROM ".ConfigData::$dbTables['dishSizes']." WHERE ".ConfigData::$dbKeys['dishSizes']['id']." = ?";

                # == Arrays ==
                $arrCartItemSize = PizzariaSopranosDB::pdoSqlReturnArray($queryGetCartItemSize, [$_SESSION['cart'][$cartItemIndex]['size']])[0];

                // ==== Start of IF ====
                $checked = $size[ConfigData::$dbKeys['dishSizes']['id']] == $arrCartItemSize[ConfigData::$dbKeys['dishSizes']['id']] ? "checked" : "";
            }

            # == Strings ==

            $strSizeID = $size[ConfigData::$dbKeys['dishSizes']['id']];
            $strSizeName = $size[ConfigData::$dbKeys['dishSizes']['name']];
            $strSizePrice = $size[ConfigData::$dbKeys['dishSizes']['price']];

            // ==== Start of Program ====
            $htmlSizeSelector .= "
            <div class='form-check form-check-inline'>
                <input class='form-check form-check-input' type='radio' name='size' id='size$strSizeName' value='$strSizeID' $checked>
                <label class='form-check form-check-label' for='size{$strSizeName}'>$strSizeName - €{$strSizePrice}</label>
            </div>
            ";
        }
        $htmlSauceSelector = "";
        foreach ($arrDishSauces as $index => $sauce) {
            // ==== Declaring Variables ====
            # == Dynamic Strings ==
            if ($cartItemIndex == "") {
                $checked = array_key_first($arrDishSizes) == $index ? "checked" : "";
            }
            else {
                // ==== Declaring Variables ====
                # == Strings ==
                # SQL
                $queryGetCartItemSauce = "SELECT ".ConfigData::$dbKeys['dishSauces']['id']." FROM ".ConfigData::$dbTables['dishSauces']." WHERE ".ConfigData::$dbKeys['dishSauces']['id']." = ?";

                # == Arrays ==
                $arrCartItemSauce = PizzariaSopranosDB::pdoSqlReturnArray($queryGetCartItemSauce, [$_SESSION['cart'][$cartItemIndex]['sauce']])[0];

                // ==== Start of IF ====
                $checked = $sauce[ConfigData::$dbKeys['dishSauces']['id']] == $arrCartItemSauce[ConfigData::$dbKeys['dishSauces']['id']] ? "checked" : "";
            }

            # == Strings ==
            $strSauceID = $sauce[ConfigData::$dbKeys['dishSauces']['id']];
            $strSauceName = $sauce[ConfigData::$dbKeys['dishSauces']['name']];
            $strSaucePrice = $sauce[ConfigData::$dbKeys['dishSauces']['price']];

            // ==== Start of Program ====
            $htmlSauceSelector .= "
            <div class='form-check form-check-inline'>
                <input class='form-check form-check-input' type='radio' name='sauce' id='sauce$strSauceName' value='$strSauceID' $checked>
                <label class='form-check form-check-label' for='sauce$strSauceName'>$strSauceName</label>
            </div>
            ";
        }
        $htmlToppingSelector = "";
        foreach ($arrToppings as $topping) {
            // ==== Declaring Variables ====
            # == Strings ==
            $strToppingName = $topping[ConfigData::$dbKeys['toppings']['name']];
            $strToppingPrice = $topping[ConfigData::$dbKeys['toppings']['price']];

            # == Ints ==
            $inToppingID = $topping[ConfigData::$dbKeys['toppings']['id']];
            $intToppingMaxAmount = $topping[ConfigData::$dbKeys['toppings']['maxAmount']];

            # Based on if the topping is a default topping or not, the amount of toppings will be different
            if ($cartItemIndex == "") {
                $intCurrentToppingAmount = in_array($topping[ConfigData::$dbKeys['toppings']['id']], $arrDefaultToppingsIds) ? 1 : 0;
            } else {
                $intCurrentToppingAmount = $_SESSION['cart'][$cartItemIndex]['toppings'][$inToppingID] ?? 0;
            }

            // ==== Start of Program ====
            $htmlToppingSelector .= "
            <div class='topping'>
                <div class='container-fluid'>
                    <div class='row mb-2'>
                        <div class='col-6'>
                            <label for='idToppingSlider{$inToppingID}'>$strToppingName <br/> €$strToppingPrice</label>
                        </div>
                        <div class='col-1'>
                            <p id='idToppingAmount{$inToppingID}'>$intCurrentToppingAmount</p>
                        </div>
                        <div class='col-5'>
                            <input type='range' min='0' max='$intToppingMaxAmount' value='$intCurrentToppingAmount' class='slider' name='nameToppingSlider[$inToppingID]' id='idToppingSlider{$inToppingID}'><br/>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                rangeSliderValueListener('idToppingSlider{$inToppingID}', 'idToppingAmount{$inToppingID}');
            </script>
            ";
        }

        $htmlCartIndex = $cartItemIndex == "" ? "<input type='hidden' name='cartItemIndex' value='$cartItemIndex'>" : "";

        // ==== Start of Program ====
        # Script loading
        echo("<script src='".Functions::dynamicPathFromIndex()."files/js/menuPage2.js'></script>");

        # Making the customizer
        $mainPage .= "
        <div class='container'>
            <div class='row justify-content-center'>
                <div class='col-10 border border-dark rounded'>
                    <div class='container-fluid mt-3 mb-3'>
                        <div class='row'>
                            <div class='col-md-6'>
                                <img class='img-fluid border border-black' src='".Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath.$arrDishMedia[0][ConfigData::$dbKeys['media']['fileFolderName']].'/'.$arrDishMedia[0][ConfigData::$dbKeys['media']['fileName']]."' alt='".$arrDishData[0][ConfigData::$dbKeys['dishes']['name']]."''>
                            </div>
                            <div class='col-md-6'>
                                <form method='POST'>
                                    <b><p>Pizza Bodem</p></b>
                                    $htmlSizeSelector
                                    <hr/>
                                    <b><p>Saus</p></b>
                                    $htmlSauceSelector
                                    <hr/>
                                    <b><p>Standaard Toppings</p></b>
                                    $htmlToppingSelector
                                    
                                    $htmlCartIndex
                                    <input class='btn btn-primary w-100' type='submit' value='Toevoegen aan winkelwagen'>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";

        break;

    # Cart
    case ConfigData::$mainMenuPages['cart']:
        // ==== Declaring Variables ====
        # == Strings ==

        // ==== Start of Case ===
        # Making the total price & order button
        $mainPage .= "
        <div class='row'>
            <div class='col-12 mb-4'>
                <div class='container-fluid p-0'>
                    <div class='row mb-3'>
                        <div class='col-12'>
                            <p class='card-text text-right'><strong>Totaal:</strong> €".array_sum(array_column($_SESSION['cart'], 'dishTotal'))."</p>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-6'>
                        <form method='POST'>
                            <input class='btn btn-primary w-100' type='submit' value='Betalen'>
                        </form>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        ";
        # Making the cart items
        $mainPage .= Functions::makeCartItems();
        break;
    default:
        // ==== Declaring Variables ====
        # == Strings ==
        # ConfigData
        $strDefaultMediaPath = Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath;

        # SQL
        $queryGetAllDishes = "SELECT * FROM ".ConfigData::$dbTables['dishes'];
        $queryGetDishMedia = "SELECT * FROM ".ConfigData::$dbTables['media']." WHERE ".ConfigData::$dbKeys['media']['dishID']." = ?";

        # == Bools ==
        $openedRow = false;

        # == Arrays ==
        $arrAllDishes = PizzariaSopranosDB::pdoSqlReturnArray($queryGetAllDishes);

        // ==== Start of Program ===
        # Making cards for the dishes (4 per row, 3 per row on mobile)
        foreach ($arrAllDishes as $index => $dish) {
            // ==== Declaring Variables ====
            # == Arrays ==
            $arrDishMedia = PizzariaSopranosDB::pdoSqlReturnArray($queryGetDishMedia, [$dish[ConfigData::$dbKeys['dishes']['id']]]);

            # == Strings ==
            # Media Path
            $strMediaPath = $strDefaultMediaPath.$arrDishMedia[0][ConfigData::$dbKeys['media']['fileFolderName']].'/'.$arrDishMedia[0][ConfigData::$dbKeys['media']['fileName']];

            # == HTML ==
            # Spicy rating
            $htmlSpiceRating = "";
            for ($i = 1; $i < 6; $i++) {
                if ($i <= $dish[ConfigData::$dbKeys['dishes']['ratingSpicy']]) {
                    $htmlSpiceRating .= "<img src='".Functions::dynamicPathFromIndex()."files/images/pepper-hot-solid.svg' alt='Spicy' style='width: 20px; height: 20px;'>";
                }
                else {
                    $htmlSpiceRating .= "<img src='".Functions::dynamicPathFromIndex()."files/images/pepper-hot-regular.svg' alt='Spicy' style='width: 20px; height: 20px;'>";
                }
            }

            // ==== Start of Loop ===
            # Checking counter to check if new row is needed
            if ($index % 4 == 0) {
                $mainPage .= "<div class='row'>";
                $openedRow = true;
            }

            # Making the card
            $mainPage .= "
            <div class='col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12'>
                <div class='card mb-3'>
                    <img src='$strMediaPath' class='card-img-top' alt='".$dish[ConfigData::$dbKeys['dishes']['name']]."' style='height: 350px; object-fit: cover;'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$dish[ConfigData::$dbKeys['dishes']['name']]."</h5>
                        <div class='container-fluid p-0'>
                            <div class='row mb-3'>
                                <!-- Spicy Rating -->
                                <div class='col-8'>
                                    $htmlSpiceRating
                                </div>
                                
                                <!-- Price -->
                                <div class='col-4'>
                                    <p class='card-text text-right'>€".$dish[ConfigData::$dbKeys['dishes']['price']]."</p>
                                </div>
                            </div>
                            <div class='row mb-2'>
                                <!-- Order button -->
                                <div class='col-12'>
                                    <a href='./menu.php?page=".ConfigData::$mainMenuPages['customizedish']."&dishID=".$dish[ConfigData::$dbKeys['dishes']['id']]."' class='btn btn-primary w-100'>
                                        Bestellen
                                    </a>
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            ";
            # Checking counter to check if new row is needed
            if ($index % 4 == 3) {
                $mainPage .= "</div>";
                $openedRow = false;
            }
        }

# Closing the row if it is still open
if ($openedRow) {
    $mainPage .= "</div>";
}
        break;
}

# Shopping Cart section
$shoppingCart = "";
if (isset($_SESSION['cart'])) {
    // ==== Declaring Variables ====
    # == Strings ==
    $strDefaultMediaPath = Functions::dynamicPathFromIndex().ConfigData::$dishMediaPath;

    // ==== Start of Program ===
    # Making the shopping cart div
    $shoppingCart .= "<div class='container-fluid' style='height: 500px; overflow-y: auto'>";

    # Making the total price & order button
    $shoppingCart .= "
    <div class='row'>
        <div class='col-12 mb-4'>
            <h5 class='card-title text-center'>Totaal Prijs</h5>
            <div class='container-fluid p-0'>
                <div class='row mb-3'>
                    <div class='col-12'>
                        <p class='card-text text-right'><strong>Totaal:</strong> €".array_sum(array_column($_SESSION['cart'], 'dishTotal'))."</p>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-12'>
                    <a href='./menu.php?page=".ConfigData::$mainMenuPages['cart']."' class='btn btn-primary w-100'>
                        Naar winkelwagen
                    </a>
                </div>
            </div>
            <hr>
        </div>
    </div>
    ";

    # Making the cart items
    $shoppingCart .= Functions::makeCartItems();

    # Closing the shopping cart div
    $shoppingCart .= "</div>";
}

# Body
if (!isset($_SESSION['cart']) or empty($_SESSION['cart'])) {
    echo("
    <div class='container-fluid'>
        <div class='row justify-content-center'>
            <!-- Menu Section -->
            <div class='col-md-10'>
                <h2 class='text-center'>Menu</h2> <hr/>
                $mainPage
            </div>
        </div>
    </div>
    ");
}
else {
    if ($currentPage == ConfigData::$mainMenuPages['cart']) {
        echo("
        <div class='container-fluid'>
            <div class='row justify-content-center'>
                <!-- Menu Section -->
                <div class='col-md-3'>
                    <h4 class='text-center'>Winkelwagen</h4>
                    $mainPage
                </div>
            </div>
        </div>
        ");
    }
    elseif ($currentPage == ConfigData::$mainMenuPages['customizedish']) {
        echo("
        <div class='container-fluid'>
            <div class='row justify-content-center'>
                <!-- Menu Section -->
                <div class='col-md-10'>
                    <h2 class='text-center'>Menu</h2> <hr/>
                    $mainPage
                </div>
        </div>
        ");
    }
    else {
        echo("
        <div class='container-fluid'>
            <div class='row justify-content-center'>
                <!-- Menu Section -->
                <div class='col-lg-9 col-md-8 col-sm-12 pe-4'>
                    <h2 class='text-center'>Menu</h2> <hr/>
                    $mainPage
                </div>
        
                <!-- Shopping Cart Section -->
                <div class='col-lg-3 col-md-4 col-sm-12'> <!-- Change here -->
                    <h2 class='text-center'>Winkelwagen</h2> <hr/>
                    $shoppingCart
                </div>
            </div>
        </div>
        ");
    }
}

# Footer
Functions::htmlFooter();