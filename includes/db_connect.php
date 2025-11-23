<?php
/* Database Connection - Auto-Detect */

$host = 'localhost';
$dbname = 'classify_db';

$credentials_to_try = [
    ['3306', 'root', ''],         // 1. Windows XAMPP/Linux
    ['8889', 'root', 'root'],     // 2. Mac MAMP
    ['3306', 'root', 'root'],     // 3. Linux/Mac
    ['3306', 'root', 'password']  // 4. Linux
];

$pdo = null;
$errors = [];

foreach ($credentials_to_try as $cred) {
    try {
        $port = $cred[0];
        $user = $cred[1];
        $pass = $cred[2];

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        
        // Connection attempt
        $pdo = new PDO($dsn, $user, $pass);
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        break; 

    } catch (PDOException $e) {

        $errors[] = "Failed on Port $port with User '$user': " . $e->getMessage();
    }
}

if (!$pdo) {
    echo "<h3>Database Connection Failed</h3>";
    echo "<p>We tried to auto-detect your OS settings, but failed.</p>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul>";
    die();
}
?>