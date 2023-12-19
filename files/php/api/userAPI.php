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
    switch ($uri) {
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
            } else {
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
                    Functions::setHTTPResponseCode(501);
                    Functions::returnJson([
                        'error' => 'Invalid date'
                    ]);

                }

                # == Strings ==
                # POST Variables
                $strEmail = strtolower($_POST['nameEmailInput']) ?? '';
                $strPassword = $_POST['namePasswordInput'] ?? '';

                if (empty($strEmail) || empty($strPassword)) {
                    Functions::setHTTPResponseCode(400);
                    Functions::returnJson([
                        'error' => 'Invalid POST data'
                    ]);
                    exit();
                }

                # SQL Variables
                $queryCheckUser = "SELECT * FROM users WHERE email = ?";
                $queryUpdateLastLogin = "UPDATE users SET dateUserLastLogin = ? WHERE email = ?";
                $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($queryCheckUser, [$strEmail]);

                // ==== Start of Program ====
                # Check if the user exists
                if (count($arrResult) == 0) {
                    Functions::setHTTPResponseCode(208);
                    Functions::returnJson([
                        'error' => 'User not found'
                    ]);
                    exit();
                }

                # Check if the password is correct
                if (!password_verify($strPassword, $arrResult[0]['password'])) {
                    Functions::setHTTPResponseCode(601);
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
                # == SQL ==
                $query = "SELECT ".implode(', ', $_POST)." FROM users WHERE userID = ?";

                // ==== Start of Program ====
                # Getting the user data
                $arrResult = PizzariaSopranosDB::pdoSqlReturnArray($query, [$_SESSION['userID']]);

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
                Functions::returnJson($arrResult);
            }
            break;
        default:
            Functions::setHTTPResponseCode(404);
            Functions::returnJson([
                'error' => 'Invalid endpoint'
            ]);
    }
}
else {
    Functions::setHTTPResponseCode(418);
    Functions::returnJson([
        'error' => 'Invalid endpoint'
    ]);
}