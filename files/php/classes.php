<?php
// ============ Imports ============

// ============ Classes ============
class ConfigData {
    // ======== Declaring Variables ========
    # ==== Strings ====
    # Authentication
    public static string $userAPIAccessToken = 'SuperSecretWachtwoordDieNooitIemandZalRaden123';
}

class PizzariaSopranosDB {
    // ======== Declaring Variables ========
    # ==== Database ====
    # Strings
    private static string $dbHost = 's244.webhostingserver.nl';
    private static string $dbUser = 'deb142504_sopranos';
    private static string $dbPass = 'DePJH7L3';
    private static string $dbName = 'deb142504_sopranos';

    # PDO
    private static ?PDO $pdo = null;

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

    // Function to excecute SQL query and return True
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