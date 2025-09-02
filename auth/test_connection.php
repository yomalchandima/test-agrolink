<?php
// Test database connection
require_once '../config/database.php';

echo "<h2>AgroLink Database Connection Test</h2>";

try {
    // Test basic connection
    echo "<p>✅ Database connection successful!</p>";
    
    // Test if tables exist
    $tables = ['users', 'products', 'orders', 'cart', 'wishlist', 'reviews', 'notifications', 'user_preferences'];
    
    echo "<h3>Checking Tables:</h3>";
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }
    
    // Test demo users
    echo "<h3>Checking Demo Users:</h3>";
    $stmt = $pdo->prepare("SELECT email, role, full_name FROM users WHERE email LIKE '%@demo.com' OR email = 'admin@agrolink.com'");
    $stmt->execute();
    $demo_users = $stmt->fetchAll();
    
    if (count($demo_users) > 0) {
        echo "<p>✅ Demo users found:</p>";
        echo "<ul>";
        foreach ($demo_users as $user) {
            echo "<li>{$user['full_name']} ({$user['email']}) - {$user['role']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No demo users found. Please import the database schema.</p>";
    }
    
    // Test sample products
    echo "<h3>Checking Sample Products:</h3>";
    $stmt = $pdo->prepare("SELECT name, price, category FROM products LIMIT 5");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    if (count($products) > 0) {
        echo "<p>✅ Sample products found:</p>";
        echo "<ul>";
        foreach ($products as $product) {
            echo "<li>{$product['name']} - Rs. {$product['price']} ({$product['category']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No products found. Please import the database schema.</p>";
    }
    
    echo "<h3>✅ Database setup is complete and working!</h3>";
    echo "<p><a href='../login.html'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>
