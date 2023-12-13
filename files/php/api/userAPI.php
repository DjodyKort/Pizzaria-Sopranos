<?php
// ============ Imports ============
# Internally
require_once($_SERVER["DOCUMENT_ROOT"].'/files/php/functions.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/files/php/classes.php');

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
                // ==== Declaring Variables ====
                # Access
                $boolAccessGranted = Functions::checkAuthentication($headers['Authorization'], ConfigData::$userAPIAccessToken);
                if (!$boolAccessGranted) {
                    Functions::setHTTPResponseCode(401);
                    Functions::returnJson([
                        'error' => 'Invalid access token'
                    ]);
                    exit();
                }

                # POST Variables
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

                Functions::setHTTPResponseCode(200);
                Functions::returnJson([
                    'test' => 'test'
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