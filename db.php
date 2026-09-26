<?php
// db.php - DataLens MySQL Database Connection (PDO)
// Connects to XAMPP MySQL database 'datalens'

$dbHost = '127.0.0.1';
$dbName = 'datalens';
$dbUser = 'root';
$dbPass = '';

/**
 * Get PDO Database Connection
 *
 * @return PDO
 * @throws Exception
 */
function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    global $dbHost, $dbName, $dbUser, $dbPass;

    // Supports both standard port 3306 and XAMPP secondary port 3307
    $ports = [3307, 3306];
    $lastException = null;

    foreach ($ports as $port) {
        try {
            $dsn = "mysql:host={$dbHost};port={$port};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $lastException = $e;
        }
    }

    throw new Exception("Database connection failed: " . ($lastException ? $lastException->getMessage() : "Unable to connect to MySQL on ports 3307 or 3306."));
}
