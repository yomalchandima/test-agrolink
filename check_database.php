<?php
// Check database structure
require_once 'config/database.php';

echo "<h1>Database Structure Check</h1>";

try {
    // Check if users table exists and has correct structure
    echo "<h2>Users Table:</h2>";
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check user count by role
    echo "<h2>User Count by Role:</h2>";
    $stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stmt->execute();
    $role_counts = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Role</th><th>Count</th></tr>";
    foreach ($role_counts as $count) {
        echo "<tr>";
        echo "<td>{$count['role']}</td>";
        echo "<td>{$count['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check specific buyer accounts
    echo "<h2>Buyer Accounts:</h2>";
    $stmt = $pdo->prepare("SELECT id, email, full_name, is_active, email_verified FROM users WHERE role = 'buyer'");
    $stmt->execute();
    $buyers = $stmt->fetchAll();
    
    if ($buyers) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Active</th><th>Verified</th></tr>";
        foreach ($buyers as $buyer) {
            echo "<tr>";
            echo "<td>{$buyer['id']}</td>";
            echo "<td>{$buyer['email']}</td>";
            echo "<td>{$buyer['full_name']}</td>";
            echo "<td>" . ($buyer['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($buyer['email_verified'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No buyer accounts found</p>";
    }
    
    // Check if user_preferences table exists
    echo "<h2>User Preferences Table:</h2>";
    $stmt = $pdo->prepare("DESCRIBE user_preferences");
    $stmt->execute();
    $pref_columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($pref_columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if notifications table exists
    echo "<h2>Notifications Table:</h2>";
    $stmt = $pdo->prepare("DESCRIBE notifications");
    $stmt->execute();
    $notif_columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($notif_columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>✅ Database structure check completed</h2>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
