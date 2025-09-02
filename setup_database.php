<?php
// Database setup script for AgroLink
echo "<h1>AgroLink Database Setup</h1>";

// Database configuration
$host = 'localhost';
$dbname = 'agrolink_db';
$username = 'root';
$password = '#795PD678rt2';

try {
    // First, connect without database to create it if it doesn't exist
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p>✅ Database '$dbname' created/verified</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema
    $schema = file_get_contents('database/schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(--|#|\/\*)/', $statement)) {
            try {
                $pdo->exec($statement);
                echo "<p>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (PDOException $e) {
                // Ignore errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<p>⚠️ Warning: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    echo "<h2>✅ Database setup completed!</h2>";
    echo "<p><a href='auth/test_connection.php'>Test Database Connection</a></p>";
    echo "<p><a href='test_buyer_registration.html'>Test Buyer Registration</a></p>";
    echo "<p><a href='register_buyer.html'>Go to Buyer Registration</a></p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>
