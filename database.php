<?php
// database.php
// This file contains the database connection settings

define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'seekosoft_adbanao');
define('DB_USER', 'root'); // Update with your database username
define('DB_PASS', ''); // Update with your database password

function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>