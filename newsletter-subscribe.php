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
require_once 'includes/Monitoring.php';

// Initialize monitoring
$monitoring = new Monitoring();
$monitoring->startRequest();

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
        // Track failed validation
        $monitoring->trackFormSubmission('newsletter', false, [
            'error' => 'Invalid email',
            'email' => $email
        ]);
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        $monitoring->endRequest();
        exit;
    }
    
    if (empty($email)) {
        // Track failed validation
        $monitoring->trackFormSubmission('newsletter', false, [
            'error' => 'Email required',
            'email' => $email
        ]);
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        $monitoring->endRequest();
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
                // Track duplicate subscription attempt
                $monitoring->trackFormSubmission('newsletter', false, [
                    'error' => 'Already subscribed',
                    'email' => $email
                ]);
                
                echo json_encode(['success' => false, 'message' => 'You are already subscribed to our newsletter']);
            } else {
                // Reactivate subscription
                $db->query(
                    "UPDATE newsletter_subscribers SET is_active = true, unsubscribed_at = NULL, updated_at = CURRENT_TIMESTAMP WHERE email = ?",
                    [$email]
                );
                
                // Track reactivation
                $monitoring->trackFormSubmission('newsletter', true, [
                    'action' => 'reactivation',
                    'email' => $email
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Welcome back! Your subscription has been reactivated']);
            }
        } else {
            // Add new subscriber
            $subscriberId = $db->insert('newsletter_subscribers', [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'source' => $source,
                'is_active' => true,
                'subscribed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Track successful subscription
            $monitoring->trackFormSubmission('newsletter', true, [
                'action' => 'new_subscription',
                'subscriber_id' => $subscriberId,
                'email' => $email
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);
        }
        
        // Track performance
        $monitoring->trackPerformance('newsletter_processing', microtime(true) * 1000);
        
    } catch (Exception $e) {
        // Track error
        $monitoring->logError($e, [
            'form_type' => 'newsletter',
            'email' => $email
        ]);
        
        error_log("Newsletter subscription error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Subscription failed. Please try again later.']);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$monitoring->endRequest(); 