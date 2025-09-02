<?php
// Debug login process
header('Content-Type: application/json');

// Log the incoming request
error_log("Login debug - Request method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw input
    $raw_input = file_get_contents('php://input');
    error_log("Login debug - Raw input: " . $raw_input);
    
    // Try to decode JSON
    $data = json_decode($raw_input, true);
    if ($data === null) {
        error_log("Login debug - JSON decode failed: " . json_last_error_msg());
        $data = $_POST;
    }
    
    error_log("Login debug - Decoded data: " . print_r($data, true));
    
    // Check required fields
    $required_fields = ['email', 'password', 'role'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missing_fields),
            'debug_data' => $data
        ]);
        exit;
    }
    
    // Test database connection and user lookup
    try {
        require_once 'config/database.php';
        
        $email = $data['email'];
        $role = $data['role'];
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, email, role, full_name, password, is_active FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Test password
            $password_correct = password_verify($data['password'], $user['password']);
            
            echo json_encode([
                'success' => true,
                'message' => 'User found in database',
                'debug_data' => $data,
                'user_info' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['full_name'],
                    'is_active' => $user['is_active'],
                    'password_correct' => $password_correct
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found with email: ' . $email . ' and role: ' . $role,
                'debug_data' => $data
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'debug_data' => $data
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD']
    ]);
}
?>
