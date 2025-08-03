<?php
/**
 * Email API
 * Handles email operations via AJAX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/EmailClient.php';

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
    $emailClient = new EmailClient($db);
    
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'inbox':
                    $limit = (int)($_GET['limit'] ?? 20);
                    $offset = (int)($_GET['offset'] ?? 0);
                    $userEmail = $_SESSION['user_data']['email'] ?? '';
                    $emails = $emailClient->getReceivedEmails($userEmail, $limit, $offset);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $emails
                    ]);
                    break;
                    
                case 'sent':
                    $limit = (int)($_GET['limit'] ?? 20);
                    $offset = (int)($_GET['offset'] ?? 0);
                    $emails = $emailClient->getSentEmails($userId, $limit, $offset);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $emails
                    ]);
                    break;
                    
                case 'email':
                    $emailId = $_GET['email_id'] ?? '';
                    
                    if (empty($emailId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email ID required']);
                        break;
                    }
                    
                    $email = $emailClient->getEmail($emailId, $userId);
                    
                    if ($email) {
                        // Mark as read if it's a received email
                        if ($email['to_email'] === ($_SESSION['user_data']['email'] ?? '')) {
                            $emailClient->markAsRead($emailId, $userId);
                        }
                        
                        echo json_encode([
                            'success' => true,
                            'data' => $email
                        ]);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Email not found']);
                    }
                    break;
                    
                case 'stats':
                    $stats = $emailClient->getEmailStats($userId);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $stats
                    ]);
                    break;
                    
                case 'search':
                    $query = $_GET['q'] ?? '';
                    
                    if (empty($query)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Search query required']);
                        break;
                    }
                    
                    $emails = $emailClient->searchEmails($userId, $query);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $emails
                    ]);
                    break;
                    
                case 'templates':
                    $templates = $emailClient->getEmailTemplates();
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $templates
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
                case 'send':
                    $toEmail = $_POST['to_email'] ?? '';
                    $subject = $_POST['subject'] ?? '';
                    $message = $_POST['message'] ?? '';
                    $attachments = $_POST['attachments'] ?? [];
                    
                    if (empty($toEmail) || empty($subject) || empty($message)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'To email, subject, and message are required']);
                        break;
                    }
                    
                    $result = $emailClient->sendEmail($userId, $toEmail, $subject, $message, $attachments);
                    
                    echo json_encode($result);
                    break;
                    
                case 'mark-read':
                    $emailId = $_POST['email_id'] ?? '';
                    
                    if (empty($emailId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email ID required']);
                        break;
                    }
                    
                    $result = $emailClient->markAsRead($emailId, $userId);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'Email marked as read' : 'Failed to mark as read'
                    ]);
                    break;
                    
                case 'star':
                    $emailId = $_POST['email_id'] ?? '';
                    $isStarred = $_POST['is_starred'] ?? false;
                    
                    if (empty($emailId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email ID required']);
                        break;
                    }
                    
                    // Update starred status
                    $result = $db->update('emails', 
                        ['is_starred' => $isStarred ? 1 : 0], 
                        'id = :email_id AND (from_user_id = :user_id OR to_email = (SELECT email FROM users WHERE id = :user_id))', 
                        ['email_id' => $emailId, 'user_id' => $userId]
                    );
                    
                    echo json_encode([
                        'success' => $result > 0,
                        'message' => $result > 0 ? 'Email starred status updated' : 'Failed to update starred status'
                    ]);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
                    break;
            }
            break;
            
        case 'DELETE':
            switch ($action) {
                case 'delete':
                    $emailId = $_GET['email_id'] ?? '';
                    
                    if (empty($emailId)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email ID required']);
                        break;
                    }
                    
                    $result = $emailClient->deleteEmail($emailId, $userId);
                    
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'Email deleted' : 'Failed to delete email'
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
    error_log("Email API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
} 