<?php
/**
 * Newsletter Subscription Handler
 * Saves subscribers to CockroachDB database
 */

header('Content-Type: application/json');

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        return false;
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, '"\'');
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

loadEnv(__DIR__ . '/.env');

require_once 'includes/Database.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $email = trim($input['email'] ?? '');
    $firstName = trim($input['first_name'] ?? '');
    $lastName = trim($input['last_name'] ?? '');
    $source = trim($input['source'] ?? 'website');
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    try {
        $db = Database::getInstance();
        
        // Check if subscriber already exists
        $existing = $db->fetchOne(
            "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?",
            [$email]
        );
        
        if ($existing) {
            if ($existing['is_active']) {
                echo json_encode(['success' => false, 'message' => 'You are already subscribed to our newsletter']);
            } else {
                // Reactivate subscription
                $db->query(
                    "UPDATE newsletter_subscribers SET is_active = true, unsubscribed_at = NULL, updated_at = CURRENT_TIMESTAMP WHERE email = ?",
                    [$email]
                );
                echo json_encode(['success' => true, 'message' => 'Welcome back! Your subscription has been reactivated']);
            }
        } else {
            // Add new subscriber
            $db->insert('newsletter_subscribers', [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'source' => $source,
                'is_active' => true,
                'subscribed_at' => date('Y-m-d H:i:s')
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);
        }
        
    } catch (Exception $e) {
        error_log("Newsletter subscription error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Subscription failed. Please try again later.']);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} 