<!DOCTYPE html>
<html>

<head>
    <title>Confirmation Page</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #ff0000;
            text-align: center;
        }

        h2 {
            color: #333333;
            text-align: center;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
            text-align: center; /* Added this line */
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333333;
        }

        input[type="radio"],
        input[type="text"],
        input[type="submit"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border: 1px solid #cccccc;
            border-radius: 3px;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            margin-right: 10px;
        }

        input[type="radio"]:checked + label {
            background-color: #ff0000;
        }

        input[type="text"],
        input[type="submit"] {
            display: block;
        }
    </style>
</head>

<body>
    <h1>Welcome to Pizzaria Sopranos</h1>
    <h2>Choose Takeout or Delivery</h2>
    <form action="process_order.php" method="POST">
        <label for="order_type">Order Type:</label>
        <br>
        <input type="radio" name="order_type" value="takeout" id="takeout" checked>
        <label for="takeout">Takeout</label>
        <input type="radio" name="order_type" value="delivery" id="delivery">
        <label for="delivery">Delivery</label>
        <br>
        <br>
        <label for="address_zipcode">Address and Zip Code:</label>
        <br>
        <input type="text" id="address_zipcode" name="address_zipcode">
        <br>
        <br>
        <input type="submit" value="Place Order">
    </form>
</body>

</html>