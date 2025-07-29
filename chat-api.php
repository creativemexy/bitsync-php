<?php
/**
 * Enhanced Chat API
 * Handles chat operations including messages, file uploads, and analytics
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

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
require_once 'includes/enhanced-chat.php';

// Initialize chat system
$enhancedChat = new EnhancedChat();

// Handle different API endpoints
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'send_message':
            handleSendMessage();
            break;
            
        case 'get_history':
            handleGetHistory();
            break;
            
        case 'upload_file':
            handleFileUpload();
            break;
            
        case 'mark_read':
            handleMarkRead();
            break;
            
        case 'get_quick_responses':
            handleGetQuickResponses();
            break;
            
        case 'track_event':
            handleTrackEvent();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleSendMessage() {
    global $enhancedChat;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $message = trim($input['message'] ?? '');
    $sessionId = $input['session_id'] ?? session_id();
    $userId = $input['user_id'] ?? null;
    $messageType = $input['message_type'] ?? 'text';
    
    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Message is required']);
        return;
    }
    
    // Save message to database
    $messageId = $enhancedChat->saveMessage($userId, $message, $messageType, $sessionId);
    
    // Track chat event
    trackChatEvent($sessionId, 'message_sent', [
        'message_length' => strlen($message),
        'message_type' => $messageType
    ]);
    
    echo json_encode([
        'success' => true,
        'message_id' => $messageId,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function handleGetHistory() {
    global $enhancedChat;
    
    $sessionId = $_GET['session_id'] ?? session_id();
    $limit = min(100, max(1, intval($_GET['limit'] ?? 50)));
    
    $history = $enhancedChat->getChatHistory($sessionId, $limit);
    
    echo json_encode([
        'success' => true,
        'history' => $history,
        'unread_count' => $enhancedChat->getUnreadCount($sessionId)
    ]);
}

function handleFileUpload() {
    global $enhancedChat;
    
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        return;
    }
    
    $file = $_FILES['file'];
    $sessionId = $_POST['session_id'] ?? session_id();
    $userId = $_POST['user_id'] ?? null;
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File type not allowed']);
        return;
    }
    
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum 5MB allowed']);
        return;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = 'uploads/chat/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Save file message to database
        $message = "File uploaded: " . $file['name'];
        $messageId = $enhancedChat->saveMessage($userId, $message, 'file', $sessionId);
        
        // Track file upload event
        trackChatEvent($sessionId, 'file_uploaded', [
            'file_name' => $file['name'],
            'file_size' => $file['size'],
            'file_type' => $file['type']
        ]);
        
        echo json_encode([
            'success' => true,
            'message_id' => $messageId,
            'file_url' => $filepath,
            'file_name' => $file['name'],
            'file_size' => $file['size'],
            'file_type' => $file['type']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
}

function handleMarkRead() {
    global $enhancedChat;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $sessionId = $input['session_id'] ?? session_id();
    
    $enhancedChat->markAsRead($sessionId);
    
    echo json_encode([
        'success' => true,
        'unread_count' => 0
    ]);
}

function handleGetQuickResponses() {
    global $db;
    
    $responses = $db->fetchAll(
        "SELECT category, response_text FROM chat_quick_responses WHERE is_active = true ORDER BY sort_order ASC"
    );
    
    echo json_encode([
        'success' => true,
        'responses' => $responses
    ]);
}

function handleTrackEvent() {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $sessionId = $input['session_id'] ?? session_id();
    $eventType = $input['event_type'] ?? '';
    $eventData = $input['event_data'] ?? [];
    
    if (empty($eventType)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Event type is required']);
        return;
    }
    
    trackChatEvent($sessionId, $eventType, $eventData);
    
    echo json_encode(['success' => true]);
}

function trackChatEvent($sessionId, $eventType, $eventData = []) {
    global $db;
    
    $db->insert('chat_analytics', [
        'session_id' => $sessionId,
        'event_type' => $eventType,
        'event_data' => json_encode($eventData),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?> 