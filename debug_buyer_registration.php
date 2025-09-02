<?php
// Debug buyer registration
header('Content-Type: application/json');

// Log the incoming request
error_log("Buyer registration debug - Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Buyer registration debug - Content type: " . $_SERVER['CONTENT_TYPE']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw input
    $raw_input = file_get_contents('php://input');
    error_log("Buyer registration debug - Raw input: " . $raw_input);
    
    // Try to decode JSON
    $data = json_decode($raw_input, true);
    if ($data === null) {
        error_log("Buyer registration debug - JSON decode failed: " . json_last_error_msg());
        $data = $_POST;
    }
    
    error_log("Buyer registration debug - Decoded data: " . print_r($data, true));
    
    // Check required fields
    $required_fields = ['email', 'password', 'confirm_password', 'full_name', 'role'];
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
    
    // Test database connection
    try {
        require_once 'config/database.php';
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'debug_data' => $data,
            'field_check' => [
                'email' => $data['email'],
                'role' => $data['role'],
                'full_name' => $data['full_name'],
                'password_length' => strlen($data['password']),
                'confirm_password_match' => ($data['password'] === $data['confirm_password'])
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage(),
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
