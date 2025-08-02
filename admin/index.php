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
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden lg:ml-6 lg:flex lg:space-x-1">
                        <a href="index.php" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        
                        <!-- Content Management -->
                        <div class="relative group">
                            <button class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-file-alt mr-1"></i>Content
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="pages.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file mr-2"></i>Pages
                                </a>
                                <a href="blog-posts.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-blog mr-2"></i>Blog Posts
                                </a>
                                <a href="blog-categories.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tags mr-2"></i>Categories
                                </a>
                            </div>
                        </div>
                        
                        <!-- User Management -->
                        <div class="relative group">
                            <button class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-users mr-1"></i>Users
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="users.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Manage Users
                                </a>
                                <a href="roles.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-tag mr-2"></i>Roles & Permissions
                                </a>
                            </div>
                        </div>
                        
                        <!-- Communication -->
                        <div class="relative group">
                            <button class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-comments mr-1"></i>Communication
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="subscribers.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-envelope mr-2"></i>Newsletter Subscribers
                                </a>
                                <a href="contacts.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-address-book mr-2"></i>Contact Messages
                                </a>
                                <a href="chat-management.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-comment-dots mr-2"></i>Live Chat
                                </a>
                            </div>
                        </div>
                        
                        <!-- Analytics & Monitoring -->
                        <div class="relative group">
                            <button class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-chart-line mr-1"></i>Analytics
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="../analytics-dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-bar mr-2"></i>Dashboard
                                </a>
                                <a href="monitoring.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-server mr-2"></i>System Monitoring
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="lg:hidden">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-700 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
                
                <!-- User menu -->
                <div class="flex items-center">
                    <div class="relative group">
                        <button class="flex items-center text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-user-circle mr-2 text-lg"></i>
                            <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="px-4 py-2 text-sm text-gray-500 border-b">
                                <i class="fas fa-user mr-2"></i>Administrator
                            </div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="lg:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-gray-50">
                <a href="index.php" class="bg-blue-100 text-blue-700 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Content</h3>
                    <a href="pages.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-file mr-2"></i>Pages
                    </a>
                    <a href="blog-posts.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-blog mr-2"></i>Blog Posts
                    </a>
                    <a href="blog-categories.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-tags mr-2"></i>Categories
                    </a>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</h3>
                    <a href="users.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-user mr-2"></i>Manage Users
                    </a>
                    <a href="roles.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-user-tag mr-2"></i>Roles & Permissions
                    </a>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Communication</h3>
                    <a href="subscribers.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-envelope mr-2"></i>Newsletter Subscribers
                    </a>
                    <a href="contacts.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-address-book mr-2"></i>Contact Messages
                    </a>
                    <a href="chat-management.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-comment-dots mr-2"></i>Live Chat
                    </a>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Analytics</h3>
                    <a href="../analytics-dashboard" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-chart-bar mr-2"></i>Dashboard
                    </a>
                    <a href="monitoring.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-server mr-2"></i>System Monitoring
                    </a>
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
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Auto-refresh stats every 30 seconds
            setInterval(function() {
                // You can add AJAX calls here to refresh stats
            }, 30000);
        });
    </script>
</body>
</html> 