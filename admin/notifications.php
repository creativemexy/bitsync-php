<?php
/**
 * Admin Notifications Management
 * Allows admins to send and manage notifications
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Notification.php';
require_once __DIR__ . '/../includes/User.php';

$db = Database::getInstance();
$notification = new Notification($db);
$userManager = new User($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'send_notification':
                $type = $_POST['type'] ?? 'info';
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $targetUsers = $_POST['target_users'] ?? 'all';
                
                if (empty($title) || empty($message)) {
                    throw new Exception('Title and message are required');
                }
                
                if ($targetUsers === 'all') {
                    // Send to all active users
                    $count = $notification->createSystemNotification($title, $message, $type);
                    $message = "Notification sent to $count users";
                } else {
                    // Send to specific user
                    $userId = $_POST['user_id'] ?? '';
                    if (empty($userId)) {
                        throw new Exception('Please select a user');
                    }
                    
                    $notification->create($userId, $type, $title, $message);
                    $message = 'Notification sent successfully';
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users for targeting
$users = $userManager->getAllUsers();

// Get notification types
$notificationTypes = $notification->getTypes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Management - BitSync Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Notification Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Send Notification Form -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Send Notification</h2>
                    </div>
                    
                    <form method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="action" value="send_notification">
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Notification Type</label>
                            <select id="type" name="type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <?php foreach ($notificationTypes as $type => $info): ?>
                                    <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="title" name="title" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter notification title">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="4" required 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Enter notification message"></textarea>
                        </div>
                        
                        <div>
                            <label for="target_users" class="block text-sm font-medium text-gray-700">Target Users</label>
                            <select id="target_users" name="target_users" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="all">All Active Users</option>
                                <option value="specific">Specific User</option>
                            </select>
                        </div>
                        
                        <div id="specific_user_section" class="hidden">
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Select User</label>
                            <select id="user_id" name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Select a user...</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Send Notification
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notification Types Info -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Notification Types</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php foreach ($notificationTypes as $type => $info): ?>
                                <div class="flex items-center space-x-3">
                                    <i class="<?php echo $info['icon']; ?> <?php echo $info['color']; ?> text-lg"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo ucfirst($type); ?></h3>
                                        <p class="text-xs text-gray-500">
                                            <?php
                                            switch ($type) {
                                                case 'info': echo 'General information and updates'; break;
                                                case 'success': echo 'Successful operations and confirmations'; break;
                                                case 'warning': echo 'Important warnings and alerts'; break;
                                                case 'error': echo 'Error messages and critical issues'; break;
                                                case 'system': echo 'System maintenance and updates'; break;
                                                case 'update': echo 'Feature updates and improvements'; break;
                                                default: echo 'General notification';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle specific user selection
        document.getElementById('target_users').addEventListener('change', function() {
            const specificSection = document.getElementById('specific_user_section');
            if (this.value === 'specific') {
                specificSection.classList.remove('hidden');
            } else {
                specificSection.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 