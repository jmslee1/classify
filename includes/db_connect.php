<?php
/* Database Connection - Auto-Detect */

$dbname = 'classify_db';

$credentials_to_try = [
    ['localhost','3306', 'root', ''],         // 1. Windows XAMPP/Linux
    ['localhost','8889', 'root', 'root'],     // 2. Mac MAMP
    ['localhost','3306', 'root', 'root'],     // 3. Linux/Mac
    ['127.0.0.1','3306', 'root', '']        // 4. Linux 
    // add your servers
];

$pdo = null;
$errors = [];

foreach ($credentials_to_try as $cred) {
    try {
        $host = $cred[0];
        $port = $cred[1];
        $user = $cred[2];
        $pass = $cred[3];

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
