<?php
session_start();

function checkSession($required_role = null) {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        return [
            'authenticated' => false,
            'message' => 'Please login to access this page'
        ];
    }
    
    // Check if session is not expired (24 hours)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 86400) {
        session_destroy();
        return [
            'authenticated' => false,
            'message' => 'Session expired. Please login again.'
        ];
    }
    
    // Check role if required
    if ($required_role && $_SESSION['role'] !== $required_role) {
        return [
            'authenticated' => false,
            'message' => 'Access denied. You do not have permission to access this page.'
        ];
    }
    
    return [
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name'],
            'user_data' => $_SESSION['user_data'] ?? [],
            'preferences' => $_SESSION['user_preferences'] ?? []
        ]
    ];
}

// Function to get current user data
function getCurrentUser() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name'],
            'user_data' => $_SESSION['user_data'] ?? [],
            'preferences' => $_SESSION['user_preferences'] ?? []
        ];
    }
    return null;
}

// Function to require authentication
function requireAuth($required_role = null) {
    $result = checkSession($required_role);
    
    if (!$result['authenticated']) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $result['message'],
            'redirect' => 'login.html'
        ]);
        exit;
    }
    
    return $result['user'];
}
?>
