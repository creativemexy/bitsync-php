<?php
/**
 * Notification API
 * Handles notification operations via AJAX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Notification.php';

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $db = Database::getInstance();
    $notification = new Notification($db);
    
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'list':
                    $limit = (int)($_GET['limit'] ?? 20);
                    $offset = (int)($_GET['offset'] ?? 0);
                    $notifications = $notification->getForUser($userId, $limit, $offset);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $notifications
                    ]);
                    break;
                    
                case 'count':
                    $unreadCount = $notification->getUnreadCount($userId);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => ['unread_count' => $unreadCount]
                    ]);
                    break;
                    
                case 'stats':
                    $stats = $notification->getStats($userId);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $stats
                    ]);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
                    break;
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'mark-read':
                    $notificationId = $_POST['notification_id'] ?? '';
                    
                    if (empty($notificationId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Notification ID required']);
                        break;
                    }
                    
                    $result = $notification->markAsRead($notificationId, $userId);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'Notification marked as read' : 'Failed to mark as read'
                    ]);
                    break;
                    
                case 'mark-all-read':
                    $result = $notification->markAllAsRead($userId);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'All notifications marked as read' : 'Failed to mark all as read'
                    ]);
                    break;
                    
                case 'delete':
                    $notificationId = $_POST['notification_id'] ?? '';
                    
                    if (empty($notificationId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Notification ID required']);
                        break;
                    }
                    
                    $result = $notification->delete($notificationId, $userId);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'Notification deleted' : 'Failed to delete notification'
                    ]);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
                    break;
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
} 