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
    if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
        throw new Exception('Email, password, and role are required');
    }
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }
    
    $password = $data['password'];
    $role = $data['role'];
    
    // Validate role
    $valid_roles = ['farmer', 'buyer', 'transporter', 'admin'];
    if (!in_array($role, $valid_roles)) {
        throw new Exception('Invalid role selected');
    }
    
    // Get user from database
    $stmt = $pdo->prepare("
        SELECT id, email, password, role, full_name, phone, location, business_name, business_type, address, is_active, email_verified 
        FROM users 
        WHERE email = ? AND role = ?
    ");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Invalid email or role');
    }
    
    if (!$user['is_active']) {
        throw new Exception('Account is deactivated. Please contact support.');
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid password');
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    // Store additional user data in session
    $_SESSION['user_data'] = [
        'phone' => $user['phone'],
        'location' => $user['location'],
        'business_name' => $user['business_name'],
        'business_type' => $user['business_type'],
        'address' => $user['address'],
        'email_verified' => $user['email_verified']
    ];
    
    // Get user preferences
    $stmt = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $preferences = $stmt->fetch();
    
    if ($preferences) {
        $_SESSION['user_preferences'] = $preferences;
    }
    
    // Create login notification
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type) 
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $user['id'],
        'Login Successful',
        'You have successfully logged into your AgroLink account.',
        'info'
    ]);
    
    // Determine redirect URL based on role
    $redirect_url = '';
    switch ($role) {
        case 'farmer':
            $redirect_url = 'dashboard_farmer.html';
            break;
        case 'buyer':
            $redirect_url = 'dashboard_buyer.html';
            break;
        case 'transporter':
            $redirect_url = 'dashboard_transporter.html';
            break;
        case 'admin':
            $redirect_url = 'dashboard_admin.html';
            break;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'business_name' => $user['business_name']
        ],
        'redirect_url' => $redirect_url
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
