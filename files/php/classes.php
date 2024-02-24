<?php
// ============ Imports ============

// ============ Classes ============
class ConfigData {
    // ======== Declaring Variables ========
    # ==== Strings ====
    # Authentication
    public static string $userAPIAccessToken = 'SuperSecretWachtwoordDieNooitIemandZalRaden123';

    # ==== Arrays ====
    # Employee user roles
    public static array $employeeUserRoles = [
        'owner' => 'Owner',
        'employee' => 'Employee',
    ];
    # Database tablenames
    public static array $dbTables = [
        'addresses' => 'addresses',
        'allergies' => 'allergies',
        'allergyRelations' => 'allergyRelations',
        'billingAddresses' => 'billingAddresses',
        'defaultToppingRelations' => 'defaultToppingRelations',
        'dishes' => 'dishes',
        'employeeRoles' => 'employeeRoles',
        'employeeUsers' => 'employeeUsers',
        'media' => 'media',
        'orderDishes' => 'orderDishes',
        'orders' => 'orders',
        'toppings' => 'toppings',
        'users' => 'users',
    ];
    # Database keys
    public static array $dbKeys = [
        'addresses' => [
            'id' => 'addressID',
            'userID' => 'userID',
            'streetName' => 'streetName',
            'houseNumber' => 'houseNumber',
            'houseNumberAddition' => 'houseNumberAddition',
            'postalCode' => 'postalCode',
            'city' => 'city',
        ],
        'allergies' => [
            'id' => 'allergyID',
            'allergy' => 'name',
            'description' => 'description',
        ],
        'allergyRelations' => [
            'id' => 'allergyRelationID',
            'allergyID' => 'allergyID',
            'dishID' => 'dishID',
        ],
        'billingAddresses' => [
            'id' => 'billingAddressID',
            'userID' => 'userID',
            'streetName' => 'streetName',
            'houseNumber' => 'houseNumber',
            'houseNumberAddition' => 'houseNumberAddition',
            'postalCode' => 'postalCode',
            'city' => 'city',
        ],
        'defaultToppingRelations' => [
            'id' => 'toppingRelationID',
            'dishID' => 'dishID',
            'toppingID' => 'toppingID',
            'dateAdded' => 'dateAdded',
            'dateUpdated' => 'dateUpdated',
        ],
        'dishes' => [
            'id' => 'dishID',
            'name' => 'name',
            'price' => 'price',
            'discountPercentage' => 'discountPercentage',
            'ratingSpicy' => 'ratingSpicy',
            'dateAdded' => 'dateAdded',
            'dateUpdated' => 'dateUpdated',
        ],
        'employeeRoles' => [
            'id' => 'roleID',
            'role' => 'roleName',
        ],
        'employeeUsers' => [
            'id' => 'employeeID',
            'password' => 'password',
            'passcode' => 'passcode',
            'roleID' => 'roleID',
            'createdAt' => 'dateEmployeeCreated',
            'lastLogin' => 'dateEmployeeLastLogin',
            'name' => 'name',
            'phoneNumber' => 'phoneNumber',
            'email' => 'email',
            'birthDate' => 'birthDate'
        ],
        'media' => [
            'id' => 'mediaID',
            'dishID' => 'dishID',
            'mediaStatus' => 'mediaStatus',
            'mediaGroup' => 'mediaGroup',
            'fileExtension' => 'fileExtension',
            'fileName' => 'fileName',
            'mediaOrder' => 'mediaOrder',
        ],
        'orderDishes' => [
            'id' => 'orderDishID',
            'orderID' => 'orderID',
            'dishID' => 'dishID',
            'toppings' => 'toppings',
        ],
        'orders' => [
            'id' => 'orderID',
            'userID' => 'userID',
            'billingAddressID' => 'billingAddressID',
            'orderStatus' => 'orderStatus',
            'addressID' => 'addressID',
            'isGuest' => 'isGuest',
            'dateOrdered' => 'dateOrdered',
            'streetName' => 'streetName',
            'houseNumber' => 'houseNumber',
            'houseNumberAddition' => 'houseNumberAddition',
            'postalCode' => 'postalCode',
            'city' => 'city',
        ],
        'toppings' => [
            'id' => 'toppingID',
            'name' => 'name',
            'price' => 'price',
            'maxAmount' => 'maxAmount',
            'dateAdded' => 'dateAdded',
            'dateUpdated' => 'dateUpdated',
        ],
        'users' => [
            'id' => 'userID',
            'password' => 'password',
            'createdAt' => 'dateUserCreated',
            'lastLogin' => 'dateUserLastLogin',
            'name' => 'name',
            'phoneNumber' => 'phoneNumber',
            'email' => 'email',
            'birthDate' => 'birthDate',
        ],
    ];

    # User setting links
    public static array $userSettingLinks = [
        'account' => 'Account',
        'addresses' => 'Adressen',
        'orders' => 'Orders',
        'logout' => 'Uitloggen',
    ];
    public static array $employeeSettingLinks = [
        'orders' => 'Orders',
        'menu' => 'Menu-items',
        'toppings' => 'Topping-lijst',
        'account' => 'Accountbeheer',
        'logout' => 'Uitloggen',
    ];

    # Statuscodes
    public static array $statusCodes = [
        # UserAPI
        401 => ['De login gegevens zijn niet correct!', 'danger'], // User not found
        402 => ['Het account bestaat al!', 'warning'], // User already exists
        403 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Database error

        400 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Invalid POST data
        405 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Invalid method
        406 => ['Er is al een factuuradres opgegeven, probeer deze te wijzigen!', 'warning'], // Billing address already exists
        407 => ['De login gegevens zijn niet correct!', 'danger'], // API Access key invalid
        418 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Invalid endpoint

        419 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Invalid variable (most likely invalid date)

        420 => ['De login gegevens zijn niet correct!', 'danger'], // USER LOGIN - Not authorized (invalid password or email)

        421 => ['Er is iets fout gegaan, probeer het later opnieuw!', 'warning'], // Any error really
    ];
}

class PizzariaSopranosDB {
    // ======== Declaring Variables ========
    # ==== Database ====
    # Strings
    private static string $dbHost = 's244.webhostingserver.nl';
    private static string $dbUser = 'deb142504_sopranos';
    private static string $dbPass = 'DePJH7L3';
    private static string $dbName = 'deb142504_sopranos';

    // ======== Functions ========
    private static function connectToDB(): PDO {
        // ==== Declaring Variables ====
        $Hostname   = self::$dbHost;                   // Database servername
        $DBname     = self::$dbName;                   // Database name
        $port       = "3306";                          // Database port
        $Username   = self::$dbUser;                   // Database username
        $Password   = self::$dbPass;                   // Database user password

        // ==== Start of Function ====
        $conn = new PDO("mysql:host=[$Hostname]; port=$port; dbname=$DBname", $Username, $Password); // Create the actual connection
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return($conn);
    }

    // Function to execute a query and return the fetched data as array
    public static function pdoSqlReturnArray($sPdoQuery, $p_arrValues=NULL): false|array
    {
        $DBconnect = self::connectToDB();                               // Connect to the MySQL database
        $statement = $DBconnect->prepare($sPdoQuery);                   // Make the query with the parameter(s)
        if ($p_arrValues == NULL) {$statement->execute();}              // Execute the query
        else {$statement->execute($p_arrValues);}                       // Execute the query
        $aResult = $statement->fetchAll(PDO::FETCH_ASSOC);              // Put the results in the $aResult
        $DBconnect=NULL;                                                // Close the connection
        return($aResult);                                               // Return to the calling method
    }

    // Function to execute a query and return the last row ID/Latest inserted ID
    public static function pdoSqlReturnLastID($sPdoQuery, $p_arrValues=NULL): false|string
    {
        $DBconnect = self::connectToDB();                               // Connect to the MySQL database
        $statement = $DBconnect->prepare($sPdoQuery);                   // Make the query with the parameter(s)
        if ($p_arrValues == NULL) {$statement->execute();}              // Execute the query
        else {$statement->execute($p_arrValues);}                       // Execute the query
        $lastRowID = $DBconnect->lastInsertId();                          // Put the results in the $aResult
        $DBconnect=NULL;                                                // Close the connection
        return($lastRowID);                                               // Return to the calling method
    }

    // Function to execute SQL query and return True
    public static function pdoSqlReturnTrue($sPdoQuery, $p_arrValues=NULL): true
    {
        $DBconnect = self::connectToDB();                         // Connect to the MySQL database
        $statement = $DBconnect->prepare($sPdoQuery);       // Make the query with the parameter
        if ($p_arrValues == NULL) {$aResult = $statement->execute();} // Execute the query and put the results in the $aResult
        else {$aResult = $statement->execute($p_arrValues);}   // Execute the query and put the results in the $aResult
        $DBconnect=NULL;                                    // Close the connection
        return(TRUE);                                       // Return to the calling method
    }
}