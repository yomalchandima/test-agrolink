<?php
// Simple test to check XAMPP connection
echo "<h1>XAMPP Connection Test</h1>";

// Test 1: Check if PHP is working
echo "<h2>Test 1: PHP is working</h2>";
echo "<p>✅ PHP is working - this page loaded successfully</p>";

// Test 2: Check if we can connect to MySQL
echo "<h2>Test 2: MySQL Connection</h2>";
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ MySQL connection successful</p>";
    
    // Test 3: Check if we can create/access database
    echo "<h2>Test 3: Database Access</h2>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS agrolink_test");
    echo "<p>✅ Database creation successful</p>";
    
    $pdo = new PDO("mysql:host=$host;dbname=agrolink_test;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Database access successful</p>";
    
    // Test 4: Check if we can create tables
    echo "<h2>Test 4: Table Creation</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS test_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL
        )
    ");
    echo "<p>✅ Table creation successful</p>";
    
    // Test 5: Check if we can insert data
    echo "<h2>Test 5: Data Insertion</h2>";
    $test_password = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO test_users (email, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['test@example.com', $test_password, 'buyer']);
    echo "<p>✅ Data insertion successful</p>";
    
    // Test 6: Check if we can retrieve data
    echo "<h2>Test 6: Data Retrieval</h2>";
    $stmt = $pdo->prepare("SELECT * FROM test_users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    $user = $stmt->fetch();
    
    if ($user && password_verify('test123', $user['password'])) {
        echo "<p>✅ Data retrieval and password verification successful</p>";
    } else {
        echo "<p>❌ Data retrieval or password verification failed</p>";
    }
    
    // Clean up
    $pdo->exec("DROP DATABASE agrolink_test");
    echo "<p>✅ Test database cleaned up</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>✅ All tests completed!</h2>";
echo "<p><a href='setup_xampp_database.php'>Run Database Setup</a></p>";
echo "<p><a href='login.html'>Go to Login Page</a></p>";
?>
