<?php
/**
 * BitSync Group Admin - Contact Submissions
 * Manage contact form submissions
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
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'mark_read') {
        $contactId = $_POST['contact_id'] ?? '';
        if ($contactId) {
            try {
                $db->query(
                    "UPDATE contact_submissions SET is_read = true WHERE id = ?",
                    [$contactId]
                );
                $message = 'Message marked as read';
            } catch (Exception $e) {
                $error = 'Failed to update message: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $contactId = $_POST['contact_id'] ?? '';
        if ($contactId) {
            try {
                $db->delete('contact_submissions', 'id = ?', [$contactId]);
                $message = 'Message deleted successfully';
            } catch (Exception $e) {
                $error = 'Failed to delete message: ' . $e->getMessage();
            }
        }
    }
}

// Get filter parameters
$filter = $_GET['filter'] ?? 'all';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query based on filter
$whereClause = '';
$params = [];

if ($filter === 'unread') {
    $whereClause = 'WHERE is_read = false';
} elseif ($filter === 'read') {
    $whereClause = 'WHERE is_read = true';
}

$contacts = $db->fetchAll(
    "SELECT * FROM contact_submissions $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$limit, $offset])
);

$totalContacts = $db->fetchOne("SELECT COUNT(*) as count FROM contact_submissions $whereClause", $params)['count'] ?? 0;
$totalPages = ceil($totalContacts / $limit);

$unreadCount = $db->fetchOne("SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = false")['count'] ?? 0;
$readCount = $db->fetchOne("SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = true")['count'] ?? 0;

// Get specific contact for viewing
$viewContact = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $contactId = $_GET['view'];
    $viewContact = $db->fetchOne("SELECT * FROM contact_submissions WHERE id = ?", [$contactId]);
    
    // Mark as read when viewing
    if ($viewContact && !$viewContact['is_read']) {
        $db->query("UPDATE contact_submissions SET is_read = true WHERE id = ?", [$contactId]);
        $viewContact['is_read'] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitSync Admin - Contact Submissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="index.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="pages.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Pages</a>
                            <a href="services.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                            <a href="subscribers.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Subscribers</a>
                            <a href="contacts.php" class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium">Contacts</a>
                            <a href="settings.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Settings</a>
                        </div>
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
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Contact Submissions</h1>
                    <p class="mt-2 text-gray-600">Manage contact form submissions from your website</p>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-3xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $totalContacts; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope-open text-3xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Read Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $readCount; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-3xl text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Unread Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $unreadCount; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($viewContact): ?>
            <!-- View Contact Message -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Message Details</h3>
                        <a href="contacts.php" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-arrow-left mr-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">From</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($viewContact['name']); ?></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:<?php echo htmlspecialchars($viewContact['email']); ?>" class="text-blue-600 hover:text-blue-900">
                                    <?php echo htmlspecialchars($viewContact['email']); ?>
                                </a>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php if ($viewContact['phone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($viewContact['phone']); ?>" class="text-blue-600 hover:text-blue-900">
                                        <?php echo htmlspecialchars($viewContact['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Company</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($viewContact['company'] ?: 'N/A'); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Subject</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($viewContact['subject'] ?: 'No subject'); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Message</h4>
                            <div class="mt-1 p-4 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo htmlspecialchars($viewContact['message']); ?></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Received</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($viewContact['created_at'])); ?></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="mt-1">
                                <?php if ($viewContact['is_read']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Read</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Unread</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <a href="mailto:<?php echo htmlspecialchars($viewContact['email']); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            <i class="fas fa-reply mr-2"></i>Reply
                        </a>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="contact_id" value="<?php echo $viewContact['id']; ?>">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700" onclick="return confirm('Delete this message?')">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Filter Tabs -->
            <div class="mb-6">
                <nav class="flex space-x-8">
                    <a href="?filter=all" class="<?php echo $filter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        All Messages
                    </a>
                    <a href="?filter=unread" class="<?php echo $filter === 'unread' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Unread (<?php echo $unreadCount; ?>)
                    </a>
                    <a href="?filter=read" class="<?php echo $filter === 'read' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Read
                    </a>
                </nav>
            </div>

            <!-- Contacts List -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contact Messages</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($contacts)): ?>
                                <?php foreach ($contacts as $contact): ?>
                                    <tr class="<?php echo !$contact['is_read'] ? 'bg-yellow-50' : ''; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($contact['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($contact['email']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($contact['subject'] ?: 'No subject'); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . (strlen($contact['message']) > 50 ? '...' : ''); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($contact['is_read']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Read</span>
                                            <?php else: ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Unread</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y g:i A', strtotime($contact['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="?view=<?php echo $contact['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            <?php if (!$contact['is_read']): ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                                        <i class="fas fa-check mr-1"></i>Mark Read
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this message?')">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No messages found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalContacts); ?> of <?php echo $totalContacts; ?> results
                            </div>
                            <div class="flex space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?filter=<?php echo $filter; ?>&page=<?php echo $page - 1; ?>" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <a href="?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium <?php echo $i === $page ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-50'; ?>"><?php echo $i; ?></a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?filter=<?php echo $filter; ?>&page=<?php echo $page + 1; ?>" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 