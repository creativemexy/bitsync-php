<?php
/**
 * Chat Management - Admin Panel
 * Monitor and manage live chat conversations
 */

session_start();

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

loadEnv(__DIR__ . '/../.env');

require_once __DIR__ . '/../includes/Database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

// Get chat statistics
$stats = [
    'active_sessions' => $db->fetchOne("SELECT COUNT(*) as count FROM chat_sessions WHERE status = 'active'")['count'] ?? 0,
    'total_messages' => $db->fetchOne("SELECT COUNT(*) as count FROM chat_messages")['count'] ?? 0,
    'unread_messages' => $db->fetchOne("SELECT COUNT(*) as count FROM chat_messages WHERE is_read = false")['count'] ?? 0,
    'today_sessions' => $db->fetchOne("SELECT COUNT(*) as count FROM chat_sessions WHERE DATE(created_at) = CURRENT_DATE")['count'] ?? 0
];

// Get recent chat sessions
$recentSessions = $db->fetchAll("
    SELECT cs.*, 
           COUNT(cm.id) as message_count,
           MAX(cm.timestamp) as last_message_time
    FROM chat_sessions cs
    LEFT JOIN chat_messages cm ON cs.session_id = cm.session_id
    GROUP BY cs.id
    ORDER BY cs.last_activity DESC
    LIMIT 10
");

// Get recent messages
$recentMessages = $db->fetchAll("
    SELECT cm.*, cs.user_agent, cs.ip_address
    FROM chat_messages cm
    JOIN chat_sessions cs ON cm.session_id = cs.session_id
    ORDER BY cm.timestamp DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Management - BitSync Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">BitSync Admin</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="pages.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Content</a>
                        <a href="subscribers.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Subscribers</a>
                        <a href="contacts.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Contacts</a>
                        <a href="monitoring.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Monitoring</a>
                        <a href="chat-management.php" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Chat Management</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 text-sm mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Chat Management</h2>
            <p class="text-gray-600">Monitor and manage live chat conversations</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-comments text-3xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Sessions</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['active_sessions']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-3xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['total_messages']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell text-3xl text-red-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Unread Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['unread_messages']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-3xl text-purple-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Sessions</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['today_sessions']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Sessions and Messages -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Chat Sessions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Chat Sessions</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentSessions)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentSessions as $session): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                Session: <?php echo substr($session['session_id'], 0, 8); ?>...
                                            </span>
                                            <?php if ($session['status'] === 'active'): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Closed
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            Messages: <?php echo $session['message_count']; ?> • 
                                            Last activity: <?php echo date('M j, Y H:i', strtotime($session['last_activity'])); ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            IP: <?php echo htmlspecialchars($session['ip_address'] ?? 'Unknown'); ?>
                                        </p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium" onclick="viewSession('<?php echo $session['session_id']; ?>')">
                                        View
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No recent chat sessions</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Messages</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentMessages)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentMessages as $message): ?>
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>
                                                <?php if (strlen($message['message']) > 100): ?>...<?php endif; ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Session: <?php echo substr($message['session_id'], 0, 8); ?>... • 
                                                <?php echo date('M j, Y H:i', strtotime($message['timestamp'])); ?>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Type: <?php echo ucfirst($message['message_type']); ?>
                                                <?php if (!$message['is_read']): ?>
                                                    • <span class="text-red-500 font-medium">Unread</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No recent messages</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="exportChatData()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Chat Data
                    </button>
                    <button onclick="clearOldSessions()" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Clear Old Sessions
                    </button>
                    <button onclick="refreshData()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-sync mr-2"></i>
                        Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewSession(sessionId) {
            // In a real implementation, this would open a detailed view of the chat session
            alert('Viewing session: ' + sessionId);
        }

        function exportChatData() {
            // In a real implementation, this would export chat data to CSV/JSON
            alert('Exporting chat data...');
        }

        function clearOldSessions() {
            if (confirm('Are you sure you want to clear old chat sessions? This action cannot be undone.')) {
                // In a real implementation, this would clear old sessions
                alert('Clearing old sessions...');
            }
        }

        function refreshData() {
            location.reload();
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            // Only refresh if the page is visible
            if (!document.hidden) {
                refreshData();
            }
        }, 30000);
    </script>
</body>
</html> 