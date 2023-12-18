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
                    Functions::setHTTPResponseCode(401);
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
                    Functions::setHTTPResponseCode(409);
                    Functions::returnJson([
                        'error' => 'User already exists'
                    ]);
                    exit();
                }

                # Insert the user
                $query = "INSERT INTO users (name , email , password, dateUserCreated) VALUES (? , ? , ?, ?)";
                try {
                    PizzariaSopranosDB::pdoSqlReturnTrue($query, [$strName, $strEmail, $strPassword, $dateTimeNow]);
                }
                catch (Exception $e) {
                    Functions::setHTTPResponseCode(500);
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
                Functions::setHTTPResponseCode(405);
                Functions::returnJson([
                    'error' => 'Invalid method'
                ]);
            } break;
        default:
            Functions::setHTTPResponseCode(404);
            Functions::returnJson([
                'error' => 'Invalid endpoint'
            ]);
    }
}
else {
    Functions::setHTTPResponseCode(404);
    Functions::returnJson([
        'error' => 'Invalid endpoint'
    ]);
}