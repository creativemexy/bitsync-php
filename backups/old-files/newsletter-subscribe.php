<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';

// Validate email
if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Create subscribers directory if it doesn't exist
$subscribersDir = 'subscribers';
if (!file_exists($subscribersDir)) {
    mkdir($subscribersDir, 0755, true);
}

$subscribersFile = $subscribersDir . '/newsletter-subscribers.json';

// Load existing subscribers
$subscribers = [];
if (file_exists($subscribersFile)) {
    $subscribers = json_decode(file_get_contents($subscribersFile), true) ?? [];
}

// Check if email already exists
foreach ($subscribers as $subscriber) {
    if ($subscriber['email'] === $email) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already subscribed']);
        exit;
    }
}

// Add new subscriber
$newSubscriber = [
    'email' => $email,
    'subscribed_at' => date('Y-m-d H:i:s'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
];

$subscribers[] = $newSubscriber;

// Save to file
if (file_put_contents($subscribersFile, json_encode($subscribers, JSON_PRETTY_PRINT))) {
    // Create backup
    $backupFile = $subscribersDir . '/newsletter-subscribers_' . date('Y-m-d_H-i-s') . '.json';
    copy($subscribersFile, $backupFile);
    
    // Optional: Send welcome email (you can implement this later)
    // sendWelcomeEmail($email);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Successfully subscribed to newsletter!',
        'subscriber_count' => count($subscribers)
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save subscription']);
}

// Function to send welcome email (optional)
function sendWelcomeEmail($email) {
    $to = $email;
    $subject = "Welcome to BitSync Group Newsletter!";
    $message = "
    <html>
    <head>
        <title>Welcome to BitSync Group Newsletter</title>
    </head>
    <body>
        <h2>Welcome to BitSync Group!</h2>
        <p>Thank you for subscribing to our newsletter. You'll now receive updates about:</p>
        <ul>
            <li>Latest technology trends and insights</li>
            <li>Company news and announcements</li>
            <li>Exclusive offers and promotions</li>
            <li>Industry best practices</li>
        </ul>
        <p>Best regards,<br>The BitSync Group Team</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: BitSync Group <hello@bitsyncgroup.com>" . "\r\n";
    
    mail($to, $subject, $message, $headers);
}
?> 