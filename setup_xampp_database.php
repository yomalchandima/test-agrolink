<?php
// Setup database for XAMPP
echo "<h1>XAMPP Database Setup for AgroLink</h1>";

// XAMPP database configuration
$host = 'localhost';
$dbname = 'agrolink_db';
$username = 'root';
$password = ''; // XAMPP MySQL has no password by default

try {
    echo "<h2>Step 1: Connect to MySQL</h2>";
    
    // First, connect without database to create it if it doesn't exist
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connected to MySQL successfully</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p>✅ Database '$dbname' created/verified</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connected to database '$dbname'</p>";
    
    echo "<h2>Step 2: Create Tables</h2>";
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('farmer', 'buyer', 'transporter', 'admin') NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            location VARCHAR(255),
            business_name VARCHAR(255),
            business_type VARCHAR(100),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            email_verified BOOLEAN DEFAULT FALSE
        )
    ");
    echo "<p>✅ Users table created/verified</p>";
    
    // Create user_preferences table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email_notifications BOOLEAN DEFAULT TRUE,
            sms_notifications BOOLEAN DEFAULT FALSE,
            order_updates BOOLEAN DEFAULT TRUE,
            price_alerts BOOLEAN DEFAULT TRUE,
            new_products BOOLEAN DEFAULT FALSE,
            preferred_delivery_time VARCHAR(100),
            delivery_radius INT DEFAULT 25,
            contactless_delivery BOOLEAN DEFAULT TRUE,
            special_instructions TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_preferences (user_id)
        )
    ");
    echo "<p>✅ User preferences table created/verified</p>";
    
    // Create notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'info',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "<p>✅ Notifications table created/verified</p>";
    
    echo "<h2>Step 3: Create Demo Accounts</h2>";
    
    // Generate correct password hash for 'demo123'
    $correct_password_hash = password_hash('demo123', PASSWORD_DEFAULT);
    echo "<p>✅ Generated password hash for 'demo123'</p>";
    
    // Delete existing demo accounts to recreate them
    $demo_emails = ['farmer@demo.com', 'buyer@demo.com', 'transporter@demo.com', 'admin@agrolink.com'];
    foreach ($demo_emails as $email) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$email]);
    }
    echo "<p>✅ Cleared existing demo accounts</p>";
    
    // Insert demo accounts with correct passwords
    $demo_accounts = [
        ['admin@agrolink.com', $correct_password_hash, 'admin', 'AgroLink Admin', '+94 11 234 5678', 'Colombo', 'AgroLink System', TRUE, TRUE],
        ['farmer@demo.com', $correct_password_hash, 'farmer', 'Ranjith Fernando', '+94 71 123 4567', 'Matale', 'Fernando Farm', TRUE, TRUE],
        ['buyer@demo.com', $correct_password_hash, 'buyer', 'Duleeka Rathnayake', '+94 71 234 5678', 'Colombo', 'Green Leaf Restaurant', TRUE, TRUE],
        ['transporter@demo.com', $correct_password_hash, 'transporter', 'Kumara Silva', '+94 71 345 6789', 'Colombo', 'Silva Transport', TRUE, TRUE]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, full_name, phone, location, business_name, is_active, email_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($demo_accounts as $account) {
        $stmt->execute($account);
        echo "<p>✅ Created demo account: {$account[0]} ({$account[2]})</p>";
    }
    
    echo "<h2>Step 4: Test Demo Accounts</h2>";
    
    foreach ($demo_accounts as $account) {
        $email = $account[0];
        $role = $account[2];
        
        // Test login
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
        
        if ($user && password_verify('demo123', $user['password'])) {
            echo "<p>✅ $email ($role) - Login test passed</p>";
        } else {
            echo "<p>❌ $email ($role) - Login test failed</p>";
        }
    }
    
    echo "<h2>Step 5: Test Buyer Registration</h2>";
    
    // Test buyer registration
    $test_email = 'testbuyer@example.com';
    
    // Delete test account if exists
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    
    // Simulate registration
    $test_password_hash = password_hash('testpass123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, full_name, is_active, email_verified) 
        VALUES (?, ?, 'buyer', 'Test Buyer', TRUE, FALSE)
    ");
    $stmt->execute([$test_email, $test_password_hash]);
    
    // Test login with new account
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'buyer'");
    $stmt->execute([$test_email]);
    $test_user = $stmt->fetch();
    
    if ($test_user && password_verify('testpass123', $test_user['password'])) {
        echo "<p>✅ Buyer registration test passed</p>";
    } else {
        echo "<p>❌ Buyer registration test failed</p>";
    }
    
    echo "<h2>✅ XAMPP Database Setup Completed!</h2>";
    echo "<p><strong>Demo Accounts:</strong></p>";
    echo "<ul>";
    echo "<li>Farmer: farmer@demo.com / demo123</li>";
    echo "<li>Buyer: buyer@demo.com / demo123</li>";
    echo "<li>Transporter: transporter@demo.com / demo123</li>";
    echo "<li>Admin: admin@agrolink.com / demo123</li>";
    echo "</ul>";
    
    echo "<p><a href='login.html'>Go to Login Page</a></p>";
    echo "<p><a href='register_buyer.html'>Go to Buyer Registration</a></p>";
    echo "<p><a href='check_database.php'>Check Database</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure XAMPP MySQL is running and the password is correct.</p>";
}
?>
