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
            // ==== Authentication ====
            # Users
            case '/loginUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

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
                    # Table name
                    $strTableName = ConfigData::$dbTables['users'];

                    # POST Variables
                    $strEmail = strtolower(filter_var($_POST['nameEmailInput'], FILTER_SANITIZE_EMAIL));
                    $strPassword = htmlspecialchars($_POST['namePasswordInput'], ENT_QUOTES, 'UTF-8');

                    # SQL Variables
                    $queryCheckUser = "SELECT * FROM $strTableName WHERE ".ConfigData::$dbKeys[$strTableName]['email']." = ?";
                    $queryUpdateLastLogin = "UPDATE $strTableName SET ".ConfigData::$dbKeys[$strTableName]['lastLogin']." = ? WHERE ".ConfigData::$dbKeys[$strTableName]['email']." = ?";

                    # == Arrays ==
                    # User check
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
            case '/employeeLogin':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

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
                    # Table name
                    $strTableName = ConfigData::$dbTables['employeeUsers'];
                    $strRoleTableName = ConfigData::$dbTables['employeeRoles'];

                    # POST Variables
                    $strEmail = strtolower(filter_var($_POST['nameEmailInput'], FILTER_SANITIZE_EMAIL));
                    $strPassword = htmlspecialchars($_POST['namePasswordInput'], ENT_QUOTES, 'UTF-8');

                    # SQL Variables
                    $queryCheckUser = "SELECT * FROM $strTableName WHERE ".ConfigData::$dbKeys[$strTableName]['email']." = ?";
                    $queryUpdateLastLogin = "UPDATE $strTableName SET ".ConfigData::$dbKeys[$strTableName]['lastLogin']." = ? WHERE ".ConfigData::$dbKeys[$strTableName]['id']." = ?";
                    $queryGetRoleInfo = "SELECT * FROM $strRoleTableName WHERE ".ConfigData::$dbKeys[$strRoleTableName]['id']." = ?";

                    # == Arrays ==
                    # User check
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
                    PizzariaSopranosDB::pdoSqlReturnTrue($queryUpdateLastLogin, [$dateTimeNow, $arrResult[0]['employeeID']]);

                    # Getting the role information from the roles table via the roleID
                    $arrRoleInfo = PizzariaSopranosDB::pdoSqlReturnArray($queryGetRoleInfo, [$arrResult[0]['roleID']]);

                    # Return the user data
                    Functions::setHTTPResponseCode(200);
                    Functions::returnJson([
                        'status' => 'success',
                        'data' => [
                            'employeeID' => $arrResult[0]['employeeID'],
                            'name' => $arrResult[0]['name'],
                            'role' => $arrResult[0]['roleID']
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
            case '/employeePasscodeLogin':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # Table name
                    $strTableName = ConfigData::$dbTables['employeeUsers'];

                    # POST Variables
                    $employeeID = filter_var($_POST['employeeID'], FILTER_SANITIZE_NUMBER_INT);
                    $strPasscode = filter_var($_POST['namePasscode'], FILTER_SANITIZE_NUMBER_INT);

                    # SQL Variables
                    $queryCheckAuth = "SELECT * FROM $strTableName WHERE ".ConfigData::$dbKeys[$strTableName]['passcode']." = ? AND ".ConfigData::$dbKeys[$strTableName]['id']." = ?";

                    // ==== Start of Program ====
                    # Check if the passcode exists
                    $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($queryCheckAuth, [$strPasscode, $employeeID]);

                    # Check if the passcode is correct
                    if (count($arrResult) == 0) {
                        Functions::setHTTPResponseCode(408);
                        Functions::returnJson([
                            'error' => 'Invalid passcode'
                        ]);
                        exit();
                    }
                    else {
                        # Return the user data
                        Functions::setHTTPResponseCode(200);
                        Functions::returnJson([
                            'status' => 'success',
                        ]);
                    }
                }
                else {
                    Functions::setHTTPResponseCode(418);
                    Functions::returnJson([
                        'error' => 'Invalid method'
                    ]);
                }

            // ==== Getting data ====
            # Users / Employees
            case '/getUserData':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    $test = '';
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);

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
            case '/getEmployeeData':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    $test = '';
                    # POST Variables
                    $employeeID = filter_var($_POST['employeeID'], FILTER_SANITIZE_NUMBER_INT);

                    # == Arrays ==
                    $arrResult = [];

                    // ==== Start of Program ====
                    # Deleting the userID from the POST array
                    unset($_POST['employeeID']);

                    # Getting the user data
                    foreach ($_POST as $strKey => $arrValues) {
                        // == Declaring Variables ==
                        # SQL
                        $query = "SELECT ".implode(', ', $arrValues)." FROM $strKey WHERE employeeID = ?";

                        // == Start of Program ==
                        # Getting the user data
                        $arrResult[$strKey] = PizzariaSopranosDB::pdoSqlReturnArray($query, [$employeeID]);

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

            # Addresses
            case '/getAddress':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);
                    unset($_POST['userID']);
                    $strTableName = array_key_first($_POST);
                    $addressIDName = ConfigData::$dbKeys[$strTableName]['id'];
                    $addressID = filter_var($_POST[$strTableName], FILTER_SANITIZE_NUMBER_INT);

                    # SQL
                    $query = "SELECT * FROM $strTableName WHERE userID = ? AND $addressIDName = ?";

                    // ==== Start of Program ====
                    # Getting the user data
                    $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($query, [$userID, $addressID]);

                    # Checking if the user data is not empty
                    if (empty($arrResult)) {
                        Functions::setHTTPResponseCode(403);
                        Functions::returnJson([
                            'error' => 'Something went wrong',
                        ]);
                        exit();
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

            // ==== Adding data ====
            # Users
            case '/createUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

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
                    $strName = htmlspecialchars($_POST['nameNameInput'], ENT_QUOTES, 'UTF-8') ?? '';
                    $strEmail = strtolower(filter_var($_POST['nameEmailInput'], FILTER_SANITIZE_EMAIL)) ?? '';
                    $strPassword = htmlspecialchars($_POST['namePasswordInput'], ENT_QUOTES, 'UTF-8') ?? '';
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
            # Addresses
            case '/addAddress':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);

                    unset($_POST['userID']);
                    $strTableName = array_key_first($_POST);

                    $strStreet = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['streetName']], ENT_QUOTES, 'UTF-8');
                    $strHouseNumber = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumber']], ENT_QUOTES, 'UTF-8');
                    $strHouseNumberAddition = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumberAddition']], ENT_QUOTES, 'UTF-8');
                    $strZipCode = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['postalCode']], ENT_QUOTES, 'UTF-8');
                    $strCity = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['city']], ENT_QUOTES, 'UTF-8');

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

            // ==== Updating data ====
            # Users / Employees
            case '/updateEmployeeUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    # ==== Declaring Variables ====
                    # POST Variables
                    $employeeID = filter_var($_POST['employeeID'], FILTER_SANITIZE_NUMBER_INT);
                    $strName = htmlspecialchars($_POST[ConfigData::$dbTables['employeeUsers']]['name'], ENT_QUOTES, 'UTF-8');
                    $strEmail = filter_var($_POST[ConfigData::$dbTables['employeeUsers']]['email'], FILTER_SANITIZE_EMAIL);
                    $strBirthDate = htmlspecialchars($_POST[ConfigData::$dbTables['employeeUsers']]['birthDate'], ENT_QUOTES, 'UTF-8');
                    $strPhoneNumber = htmlspecialchars($_POST[ConfigData::$dbTables['employeeUsers']]['phoneNumber'], ENT_QUOTES, 'UTF-8');
                    $strPasscode = filter_var($_POST[ConfigData::$dbTables['employeeUsers']]['passcode'], FILTER_SANITIZE_NUMBER_INT);

                    # SQL
                    $query = "UPDATE employeeUsers SET name = ?, email = ?, birthDate = ?, phoneNumber = ?, passcode = ? WHERE employeeID = ?";

                    // ==== Start of Program ====
                    try {
                        # Update the user
                        PizzariaSopranosDB::pdoSqlReturnTrue($query, [$strName, $strEmail, $strBirthDate, $strPhoneNumber, $strPasscode, $employeeID]);

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
            case '/updateUser':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    # ==== Declaring Variables ====
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);
                    $strName = htmlspecialchars($_POST['users']['name'], ENT_QUOTES, 'UTF-8');
                    $strEmail = filter_var($_POST['users']['email'], FILTER_SANITIZE_EMAIL);
                    $strBirthDate = htmlspecialchars($_POST['users']['birthDate'], ENT_QUOTES, 'UTF-8');
                    $strPhoneNumber = htmlspecialchars($_POST['users']['phoneNumber'], ENT_QUOTES, 'UTF-8');

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

            # Addresses
            case '/updateAddress':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);
                    unset($_POST['userID']);
                    $strTableName = array_key_first($_POST);
                    $addressIDName = ConfigData::$dbKeys[$strTableName]['id'];
                    $addressID = filter_var($_POST[$strTableName][$addressIDName], FILTER_SANITIZE_NUMBER_INT);

                    $strStreet = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['streetName']], ENT_QUOTES, 'UTF-8');
                    $strHouseNumber = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumber']], ENT_QUOTES, 'UTF-8');
                    $strHouseNumberAddition = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['houseNumberAddition']], ENT_QUOTES, 'UTF-8');
                    $strZipCode = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['postalCode']], ENT_QUOTES, 'UTF-8');
                    $strCity = htmlspecialchars($_POST[$strTableName][ConfigData::$dbKeys['billingAddresses']['city']], ENT_QUOTES, 'UTF-8');

                    # SQL
                    $query = "UPDATE $strTableName SET streetName = ?, houseNumber = ?, houseNumberAddition = ?, postalCode = ?, city = ? WHERE userID = ? AND $addressIDName = ?";
                    $arrPreparedValues = [$strStreet, $strHouseNumber, $strHouseNumberAddition, $strZipCode, $strCity, $userID, $addressID];

                    $completedQuery = "UPDATE $strTableName SET streetName = '$strStreet', houseNumber = '$strHouseNumber', houseNumberAddition = '$strHouseNumberAddition', postalCode = '$strZipCode', city = '$strCity' WHERE userID = $userID AND $addressIDName = $addressID";
                    // ==== Start of Program ====
                    try {
                        # Update the address
                        PizzariaSopranosDB::pdoSqlReturnTrue($query, $arrPreparedValues);

                        # Return API status
                        Functions::setHTTPResponseCode(200);
                        Functions::returnJson([
                            'status' => 'success',
                            $completedQuery
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
                break;

            // ==== Deleting data ====
            case '/deleteAddress':
                // ======== POST ========
                if ($method == 'POST') {
                    // ==== Checking access ====
                    Functions::checkAccessToken($headers['Authorization']);
                    Functions::checkPostData($_POST);

                    // ==== Declaring Variables ====
                    # == Strings ==
                    # POST Variables
                    $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);
                    unset($_POST['userID']);
                    $strTableName = array_key_first($_POST);
                    $addressIDName = array_key_first($_POST[$strTableName]);
                    $addressID = filter_var($_POST[$strTableName][$addressIDName], FILTER_SANITIZE_NUMBER_INT);

                    # SQL
                    $query = "DELETE FROM $strTableName WHERE userID = ? AND $addressIDName = ?";
                    $arrPreparedValues = [$userID, $addressID];


                    // ==== Start of Program ====
                    try {
                        # Delete the address
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
