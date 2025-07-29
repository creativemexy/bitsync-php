<?php
/**
 * BitSync Group Admin Interface
 * Main admin dashboard and authentication
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
require_once __DIR__ . '/../includes/ContentManager.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$contentManager = new ContentManager();

// Get dashboard stats
$db = Database::getInstance();
$stats = [
    'pages' => $db->fetchOne("SELECT COUNT(*) as count FROM content_pages")['count'] ?? 0,
    'subscribers' => $db->fetchOne("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE is_active = true")['count'] ?? 0,
    'contacts' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = false")['count'] ?? 0,
    'services' => $db->fetchOne("SELECT COUNT(*) as count FROM services WHERE is_active = true")['count'] ?? 0
];

$recentPages = $db->fetchAll("SELECT page_key, title, updated_at FROM content_pages ORDER BY updated_at DESC LIMIT 5");
$recentContacts = $db->fetchAll("SELECT name, email, subject, created_at FROM contact_submissions ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitSync Admin - Dashboard</title>
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
                        <a href="index.php" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="pages.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Content</a>
                        <a href="subscribers.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Subscribers</a>
                        <a href="contacts.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Contacts</a>
                        <a href="monitoring.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Monitoring</a>
                        <a href="../analytics-dashboard" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Analytics</a>
                        <a href="chat-management.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Chat Management</a>
                        <a href="blog-posts.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Blog Posts</a>
                        <a href="blog-categories.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Categories</a>
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
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome to your BitSync content management system</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-3xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Content Pages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['pages']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="pages.php" class="font-medium text-blue-700 hover:text-blue-900">View all pages</a>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Newsletter Subscribers</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['subscribers']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="subscribers.php" class="font-medium text-green-700 hover:text-green-900">View subscribers</a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-comments text-3xl text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Unread Messages</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['contacts']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="contacts.php" class="font-medium text-yellow-700 hover:text-yellow-900">View messages</a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-cogs text-3xl text-purple-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Services</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['services']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="services.php" class="font-medium text-purple-700 hover:text-purple-900">View services</a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-3xl text-indigo-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Analytics Dashboard</dt>
                                <dd class="text-lg font-medium text-gray-900">Live Insights</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="../analytics-dashboard" class="font-medium text-indigo-700 hover:text-indigo-900">View analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Pages -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Page Updates</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentPages)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentPages as $page): ?>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($page['title']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($page['page_key']); ?></p>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($page['updated_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No recent page updates</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Contacts -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Contact Messages</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentContacts)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentContacts as $contact): ?>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($contact['name']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($contact['subject'] ?: 'No subject'); ?></p>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($contact['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No recent contact messages</p>
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
                    <a href="pages.php?action=new" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Page
                    </a>
                    <a href="services.php?action=new" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-cog mr-2"></i>
                        Add New Service
                    </a>
                    <a href="blog-posts.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-file-alt mr-2"></i>
                        Create Blog Post
                    </a>
                    <a href="blog-categories.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                        <i class="fas fa-tags mr-2"></i>
                        Manage Categories
                    </a>
                    <a href="settings.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <i class="fas fa-cog mr-2"></i>
                        Update Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add any JavaScript for interactivity here
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                // You can add AJAX calls here to refresh stats
            }, 30000);
        });
    </script>
</body>
</html> 