<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['email', 'password', 'confirm_password', 'full_name', 'role'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }
    
    $password = $data['password'];
    $confirm_password = $data['confirm_password'];
    $full_name = trim($data['full_name']);
    $role = $data['role'];
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $location = isset($data['location']) ? trim($data['location']) : '';
    $business_name = isset($data['business_name']) ? trim($data['business_name']) : '';
    $business_type = isset($data['business_type']) ? trim($data['business_type']) : '';
    $address = isset($data['address']) ? trim($data['address']) : '';
    
    // Validate password
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }
    
    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }
    
    // Validate role
    $valid_roles = ['farmer', 'buyer', 'transporter'];
    if (!in_array($role, $valid_roles)) {
        throw new Exception('Invalid role selected');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email already registered');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role, full_name, phone, location, business_name, business_type, address, email_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $email,
        $hashed_password,
        $role,
        $full_name,
        $phone,
        $location,
        $business_name,
        $business_type,
        $address,
        false // email_verified starts as false
    ]);
    
    $user_id = $pdo->lastInsertId();
    
    // Create default user preferences
    $stmt = $pdo->prepare("
        INSERT INTO user_preferences (user_id, email_notifications, sms_notifications, order_updates, price_alerts, new_products, preferred_delivery_time, delivery_radius, contactless_delivery) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $user_id,
        true,  // email_notifications
        false, // sms_notifications
        true,  // order_updates
        true,  // price_alerts
        false, // new_products
        'afternoon', // preferred_delivery_time
        25,    // delivery_radius
        true   // contactless_delivery
    ]);
    
    // Create welcome notification
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type) 
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $user_id,
        'Welcome to AgroLink!',
        'Thank you for registering with AgroLink. Your account has been created successfully.',
        'success'
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Please login to continue.',
        'user_id' => $user_id
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
