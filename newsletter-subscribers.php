<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simple admin check (you can enhance this with proper authentication)
$admin_key = $_GET['admin_key'] ?? $_POST['admin_key'] ?? '';
if ($admin_key !== 'bitsync2024') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

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

switch ($action) {
    case 'list':
        // List all subscribers
        echo json_encode([
            'success' => true,
            'subscribers' => $subscribers,
            'count' => count($subscribers)
        ]);
        break;
        
    case 'delete':
        // Delete a subscriber
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        
        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }
        
        // Find and remove the subscriber
        $found = false;
        foreach ($subscribers as $key => $subscriber) {
            if ($subscriber['email'] === $email) {
                unset($subscribers[$key]);
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Subscriber not found']);
            exit;
        }
        
        // Re-index array
        $subscribers = array_values($subscribers);
        
        // Save updated list
        if (file_put_contents($subscribersFile, json_encode($subscribers, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true, 'message' => 'Subscriber deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete subscriber']);
        }
        break;
        
    case 'export':
        // Export subscribers as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['Email', 'Subscribed At', 'IP Address', 'User Agent']);
        
        // Add subscriber data
        foreach ($subscribers as $subscriber) {
            fputcsv($output, [
                $subscriber['email'],
                $subscriber['subscribed_at'],
                $subscriber['ip_address'],
                $subscriber['user_agent']
            ]);
        }
        
        fclose($output);
        exit;
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?> 