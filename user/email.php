<?php
/**
 * BitSync Email Client
 * Basic email interface for user dashboard
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/EmailClient.php';

$db = Database::getInstance();
$userManager = new User($db);
$emailClient = new EmailClient($db);

// Load user data
$userManager->loadById($_SESSION['user_id']);
$userData = $userManager->getUserData();

// Get email stats
$emailStats = $emailClient->getEmailStats($_SESSION['user_id']);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'send_email':
                $toEmail = trim($_POST['to_email'] ?? '');
                $subject = trim($_POST['subject'] ?? '');
                $message = trim($_POST['message'] ?? '');
                
                if (empty($toEmail) || empty($subject) || empty($message)) {
                    throw new Exception('To email, subject, and message are required');
                }
                
                $result = $emailClient->sendEmail($_SESSION['user_id'], $toEmail, $subject, $message);
                
                if ($result['success']) {
                    $message = 'Email sent successfully';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get emails for display
$inboxEmails = $emailClient->getReceivedEmails($userData['email'], 10);
$sentEmails = $emailClient->getSentEmails($_SESSION['user_id'], 10);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email - BitSync Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-blue-600">BitSync Email</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Compose Email -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Compose Email</h2>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="send_email">
                        
                        <div>
                            <label for="to_email" class="block text-sm font-medium text-gray-700">To:</label>
                            <input type="email" id="to_email" name="to_email" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="recipient@example.com">
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject:</label>
                            <input type="text" id="subject" name="subject" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Email subject">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message:</label>
                            <textarea id="message" name="message" rows="6" required 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Type your message here..."></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>Send Email
                        </button>
                    </form>
                    
                    <!-- Email Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Email Stats</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Received:</span>
                                <span class="font-medium"><?php echo $emailStats['received']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sent:</span>
                                <span class="font-medium"><?php echo $emailStats['sent']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Unread:</span>
                                <span class="font-medium text-red-600"><?php echo $emailStats['unread']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inbox -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Inbox</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php if (empty($inboxEmails)): ?>
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-inbox text-lg mb-2"></i>
                                <p>No emails in inbox</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($inboxEmails as $email): ?>
                                <div class="p-4 hover:bg-gray-50 <?php echo !$email['is_read'] ? 'bg-blue-50' : ''; ?>">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo htmlspecialchars($email['subject']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                From: <?php echo htmlspecialchars($email['from_user_id'] ? 'User' : 'System'); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo date('M j, Y g:i A', strtotime($email['created_at'])); ?>
                                            </p>
                                        </div>
                                        <?php if (!$email['is_read']): ?>
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sent -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Sent</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php if (empty($sentEmails)): ?>
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-paper-plane text-lg mb-2"></i>
                                <p>No sent emails</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($sentEmails as $email): ?>
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?php echo htmlspecialchars($email['subject']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                To: <?php echo htmlspecialchars($email['to_email']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo date('M j, Y g:i A', strtotime($email['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 