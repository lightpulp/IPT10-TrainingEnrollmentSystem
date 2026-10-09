```php
<?php
// config/db.php
// Include the Database class
require_once __DIR__ . '/../classes/Database.php';

$dsn = "mysql:host=localhost;dbname=training_db;charset=utf8mb4";
$user = "root";
$pass = "";

// Obtain the single shared PDO connection
$db = Database::getInstance($dsn, $user, $pass);
?>