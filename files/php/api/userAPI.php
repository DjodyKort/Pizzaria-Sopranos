<?php
// ============ Imports ============
# Internally
require_once('../functions.php');
require_once('../classes.php');

// ============ Declaring Variables ============
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (!empty($uri)) {
    # Remove the query string
    $uri = explode('?', $uri)[0];

    # Remove everything before the last slash
    $uri = explode('/', $uri);
    $uri = '/'.$uri[count($uri)-1];
}
$method = $_SERVER['REQUEST_METHOD'] ?? '';
$headers = getallheaders();

// ============ Start of Program ============
if (!empty($uri) && !empty($method)) {
    try {
        switch ($uri) {
            case '/updateUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    # Access
                    $boolAccessGranted = Functions::isEqual($headers['Authorization'], ConfigData::$userAPIAccessToken);
                    if (!$boolAccessGranted) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Invalid access token'
                        ]);
                        exit();
                    }
                    # Checking if the POST is not empty
                    if (empty($_POST)) {
                        Functions::setHTTPResponseCode(400);
                        Functions::returnJson([
                            'error' => 'Invalid POST data'
                        ]);
                        exit();
                    }

                    # ==== Declaring Variables ====
                    # POST Variables
                    $userID = $_POST['userID'];
                    $strName = $_POST['users']['name'];
                    $strEmail = $_POST['users']['email'];
                    $strBirthDate = $_POST['users']['birthDate'];
                    $strPhoneNumber = $_POST['users']['phoneNumber'];

                    # SQL
                    $query = "UPDATE users SET name = ?, email = ?, birthDate = ?, phoneNumber = ? WHERE userID = ?";

                    // ==== Start of Program ====
                    try {
                        # Update the user
                        PizzariaSopranosDB::pdoSqlReturnTrue($query, [$strName, $strEmail, $strBirthDate, $strPhoneNumber, $userID]);

                        # Return API status
                        Functions::setHTTPResponseCode(200);
                        Functions::returnJson([
                            'status' => 'success',
                            'data' => [
                                'name' => $strName,
                            ]
                        ]);
                    }
                    catch (Exception $e) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Something went wrong'
                        ]);
                        exit();
                    }
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }
                break;
            case '/createUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    # Access
                    $boolAccessGranted = Functions::isEqual($headers['Authorization'], ConfigData::$userAPIAccessToken);
                    if (!$boolAccessGranted) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Invalid access token'
                        ]);
                        exit();
                    }

                    // ==== Declaring Variables ====
                    # == Datetime ==
                    try {
                        $dateTimeNow = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
                        $dateTimeNow = $dateTimeNow->format('Y-m-d H:i:s');
                    } catch (Exception $e) {
                        $dateTimeNow = '2023-01-01 00:00:00';
                        Functions::setHTTPResponseCode(419);
                        Functions::returnJson([
                            'error' => 'Invalid date'
                        ]);

                    }

                    # == Strings ==
                    # POST Variables
                    $strName = $_POST['nameNameInput'] ?? '';
                    $strEmail = strtolower($_POST['nameEmailInput']) ?? '';
                    $strPassword = $_POST['namePasswordInput'] ?? '';
                    if (empty($strName) || empty($strEmail) || empty($strPassword)) {
                        Functions::setHTTPResponseCode(400);
                        Functions::returnJson([
                            'error' => 'Invalid POST data'
                        ]);
                        exit();
                    }

                    // ==== Start of Program ====
                    # Hash the password
                    $strPassword = password_hash($strPassword, PASSWORD_DEFAULT);

                    # Check if the user already exists
                    $query = "SELECT * FROM users WHERE email = ?";
                    $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($query, [$strEmail]);

                    if (count($arrResult) > 0) {
                        Functions::setHTTPResponseCode(402);
                        Functions::returnJson([
                            'error' => 'User already exists'
                        ]);
                        exit();
                    }

                    # Insert the user
                    $query = "INSERT INTO users (name , email , password, dateUserCreated) VALUES (? , ? , ?, ?)";
                    try {
                        PizzariaSopranosDB::pdoSqlReturnTrue($query, [$strName, $strEmail, $strPassword, $dateTimeNow]);
                    } catch (Exception $e) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Something went wrong'
                        ]);
                        exit();
                    }
                    Functions::setHTTPResponseCode(200);
                    Functions::returnJson([
                        'status' => 'success',
                    ]);
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }
                break;
            case '/loginUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    # Access
                    $boolAccessGranted = Functions::isEqual($headers['Authorization'], ConfigData::$userAPIAccessToken);
                    if (!$boolAccessGranted) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Invalid access token'
                        ]);
                        exit();
                    }

                    // ==== Declaring Variables ====
                    # == Datetime ==
                    try {
                        $dateTimeNow = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
                        $dateTimeNow = $dateTimeNow->format('Y-m-d H:i:s');
                    }
                    catch (Exception $e) {
                        $dateTimeNow = '2023-01-01 00:00:00';
                        Functions::setHTTPResponseCode(419);
                        Functions::returnJson([
                            'error' => 'Invalid date'
                        ]);

                    }

                    # == Strings ==
                    # POST Variables
                    $strEmail = strtolower($_POST['nameEmailInput']);
                    $strPassword = $_POST['namePasswordInput'];

                    # SQL Variables
                    $queryCheckUser = "SELECT * FROM users WHERE email = ?";
                    $queryUpdateLastLogin = "UPDATE users SET dateUserLastLogin = ? WHERE email = ?";
                    $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($queryCheckUser, [$strEmail]);

                    // ==== Start of Program ====
                    # Check if the user exists
                    if (count($arrResult) == 0) {
                        Functions::setHTTPResponseCode(401);
                        Functions::returnJson([
                            'error' => 'User not found'
                        ]);
                        exit();
                    }

                    # Check if the password is correct
                    if (!password_verify($strPassword, $arrResult[0]['password'])) {
                        Functions::setHTTPResponseCode(420);
                        Functions::returnJson([
                            'error' => 'Invalid password'
                        ]);
                        exit();
                    }

                    # Update the last login date
                    PizzariaSopranosDB::pdoSqlReturnTrue($queryUpdateLastLogin, [$dateTimeNow, $strEmail]);

                    # Return the user data
                    Functions::setHTTPResponseCode(200);
                    Functions::returnJson([
                        'status' => 'success',
                        'data' => [
                            'userID' => $arrResult[0]['userID'],
                            'name' => $arrResult[0]['name']
                        ]
                    ]);
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }
                break;
            case '/getUserData':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    # Access
                    $boolAccessGranted = Functions::isEqual($headers['Authorization'], ConfigData::$userAPIAccessToken);
                    if (!$boolAccessGranted) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Invalid access token'
                        ]);
                        exit();
                    }
                    # Checking if the POST is not empty
                    if (empty($_POST)) {
                        Functions::setHTTPResponseCode(400);
                        Functions::returnJson([
                            'error' => 'Invalid POST data'
                        ]);
                        exit();
                    }

                    // ==== Declaring Variables ====
                    # == Strings ==
                    $test = '';
                    # POST Variables
                    $userID = $_POST['userID'];

                    # == Arrays ==
                    $arrResult = [];

                    // ==== Start of Program ====
                    # Deleting the userID from the POST array
                    unset($_POST['userID']);

                    # Getting the user data
                    foreach ($_POST as $strKey => $arrValues) {
                        // == Declaring Variables ==
                        # SQL
                        $query = "SELECT ".implode(', ', $arrValues)." FROM $strKey WHERE userID = ?";

                        // == Start of Program ==
                        # Getting the user data
                        $arrResult[$strKey] = PizzariaSopranosDB::pdoSqlReturnArray($query, [$userID]);

                        # Checking if the user data is not empty
                        if (empty($arrResult)) {
                            Functions::setHTTPResponseCode(403);
                            Functions::returnJson([
                                'error' => 'Something went wrong',
                            ]);
                            exit();
                        }
                    }

                    # Returning the requested user data
                    Functions::setHTTPResponseCode(200);
                    Functions::returnJson([
                        'status' => 'success',
                        'data' => $arrResult
                    ]);
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }
                break;
            case '/addAddress':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    # Access
                    $boolAccessGranted = Functions::isEqual($headers['Authorization'], ConfigData::$userAPIAccessToken);
                    if (!$boolAccessGranted) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Invalid access token'
                        ]);
                        exit();
                    }
                    # Checking if the POST is not empty
                    if (empty($_POST)) {
                        Functions::setHTTPResponseCode(400);
                        Functions::returnJson([
                            'error' => 'Invalid POST data'
                        ]);
                        exit();
                    }

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # POST Variables
                    $userID = $_POST['userID'];

                    unset($_POST['userID']);
                    $strTableName = array_key_first($_POST);

                    $strStreet = $_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['streetName']];
                    $strHouseNumber = $_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumber']];
                    $strHouseNumberAddition = $_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumberAddition']];
                    $strZipCode = $_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['postalCode']];
                    $strCity = $_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['city']];

                    # SQL
                    $checkQuery = "SELECT * FROM $strTableName WHERE userID = ?";
                    $query = "INSERT INTO $strTableName (userID, streetName, houseNumber, houseNumberAddition, postalCode, city) VALUES (?, ?, ?, ?, ?, ?)";

                    # == Arrays ==
                    $arrPreparedValues = [$userID, $strStreet, $strHouseNumber, $strHouseNumberAddition, $strZipCode, $strCity];

                    // ==== Start of Program ====
                    try {
                        # Check if the address already exists IF it's a billing address
                        if ($strTableName == 'billingAddresses') {
                            $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($checkQuery, [$userID]);
                            if (count($arrResult) > 0) {
                                Functions::setHTTPResponseCode(406);
                                Functions::returnJson([
                                    'error' => 'Address already exists'
                                ]);
                                exit();
                            }
                        }

                        # Insert the address
                        PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPreparedValues);
                        # Return API status
                        Functions::setHTTPResponseCode(200);
                        Functions::returnJson([
                            'status' => 'success',
                        ]);
                    }
                    catch (Exception $e) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Something went wrong'
                        ]);
                        exit();
                    }
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }
                break;
            default:
                Functions::setHTTPResponseCode(418);
                Functions::returnJson([
                    'error' => 'Invalid endpoint'
                ]);
        }
    }
    catch (Exception $e) {
        Functions::setHTTPResponseCode(403);
        Functions::returnJson([
            'error' => 'Something went wrong',
        ]);
        exit();
    }
}
else {
    Functions::setHTTPResponseCode(421);
    Functions::returnJson([
        'error' => 'Something went wrong'
    ]);
}