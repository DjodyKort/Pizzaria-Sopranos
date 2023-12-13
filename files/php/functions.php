<?php
// ============ Imports ============

// ============ Functions ============
class Functions {
    // ======== Functions ========
    # ==== PHP ====
    public static function pathUntilIndex(): string{
        // ======== Declaring Variables ========
        $currentPath = $_SERVER['PHP_SELF'];

        // ======== Start of Function ========
        # Check if index.php is in the path
        if (str_contains($currentPath, 'index.php')) {
            $strPath = './';
        } else {
            # Get the amount of slashes in the path
            $intSlashCount = substr_count($currentPath, '/');
            # Create the path
            $strPath = str_repeat('../', $intSlashCount-2);
        }
        return($strPath);
    }

    # ==== HTML ====
    public static function htmlHeader(): void {
        // ======== Declaring Variables ========

        // ======== Start of Program ========
        echo("
        <!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Pizzaria Sopranos</title>
                    
                    <!-- CSS Imports -->
                    <link rel='stylesheet' href='".self::pathUntilIndex()."files/css/bootstrap.min.css'>
                    <link rel='stylesheet' href='".self::pathUntilIndex()."files/css/style.css'>
                    
                    <!-- JS Imports -->
                    <script src='".self::pathUntilIndex()."files/js/jquery-3.7.1.min.js'></script>
                    <script src='".self::pathUntilIndex()."files/js/bootstrap.bundle.min.js'></script> 
                </head>
                <body>
        ");
    }

    public static function htmlFooter(): void {
        echo("
                </body>
            </html>
        ");
    }
}