<?php
/**
 * BitSync User Dashboard
 * Google Workspace-like interface for regular users
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Notification.php';

$db = Database::getInstance();
$userManager = new User($db);
$notification = new Notification($db);

// Load user data
$userManager->loadById($_SESSION['user_id']);
$userData = $userManager->getUserData();
$userRoles = $userManager->getRoles();
$userPermissions = $userManager->getPermissions();

// Get quick stats
$stats = [
    'documents' => 0,
    'projects' => 0,
    'collaborations' => 0
];

// Get notification stats
$notificationStats = $notification->getStats($_SESSION['user_id']);
$unreadCount = $notification->getUnreadCount($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitSync Workspace - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        workspace: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-workspace-600">BitSync Workspace</h1>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="ml-8 flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" placeholder="Search in Workspace..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-workspace-500 focus:border-workspace-500">
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notificationBell" class="p-2 text-gray-400 hover:text-gray-500 relative">
                            <i class="fas fa-bell text-lg"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg opacity-0 invisible transition-all duration-200 z-50 border border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                                    <?php if ($unreadCount > 0): ?>
                                        <button id="markAllRead" class="text-sm text-workspace-600 hover:text-workspace-700">
                                            Mark all as read
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be loaded here -->
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-lg mb-2"></i>
                                    <p>Loading notifications...</p>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200">
                                <a href="#" class="text-sm text-workspace-600 hover:text-workspace-700">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <button class="p-2 text-gray-400 hover:text-gray-500">
                        <i class="fas fa-cog text-lg"></i>
                    </button>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 rounded-full bg-workspace-500 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    <?php echo strtoupper(substr($userData['first_name'] ?? $userData['username'], 0, 1)); ?>
                                </span>
                            </div>
                            <span class="text-sm font-medium"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm border-r border-gray-200 min-h-screen">
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-workspace-700 bg-workspace-50 rounded-md">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                    
                    <div class="pt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Workspace</h3>
                        <div class="mt-2 space-y-1">
                            <a href="documents.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-file-alt mr-3"></i>
                                Documents
                            </a>
                            <a href="projects.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-project-diagram mr-3"></i>
                                Projects
                            </a>
                            <a href="calendar.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-calendar mr-3"></i>
                                Calendar
                            </a>
                            <a href="collaborations.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-users mr-3"></i>
                                Collaborations
                            </a>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tools</h3>
                        <div class="mt-2 space-y-1">
                            <a href="notes.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-sticky-note mr-3"></i>
                                Notes
                            </a>
                            <a href="tasks.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-tasks mr-3"></i>
                                Tasks
                            </a>
                            <a href="analytics.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                                <i class="fas fa-chart-line mr-3"></i>
                                Analytics
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Welcome back, <?php echo htmlspecialchars($userData['first_name']); ?>!</h1>
                    <p class="mt-2 text-gray-600">Here's what's happening in your workspace today.</p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file-alt text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Documents</dt>
                                        <dd class="text-lg font-medium text-gray-900"><?php echo $stats['documents']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="documents.php" class="font-medium text-blue-700 hover:text-blue-900">View all documents</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-project-diagram text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Projects</dt>
                                        <dd class="text-lg font-medium text-gray-900"><?php echo $stats['projects']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="projects.php" class="font-medium text-green-700 hover:text-green-900">View all projects</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Collaborations</dt>
                                        <dd class="text-lg font-medium text-gray-900"><?php echo $stats['collaborations']; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="collaborations.php" class="font-medium text-purple-700 hover:text-purple-900">View collaborations</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <button onclick="createDocument()" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-workspace-500 hover:bg-workspace-50 transition-colors">
                                <i class="fas fa-plus text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">New Document</span>
                            </button>
                            <button onclick="createProject()" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-workspace-500 hover:bg-workspace-50 transition-colors">
                                <i class="fas fa-folder-plus text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">New Project</span>
                            </button>
                            <button onclick="createNote()" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-workspace-500 hover:bg-workspace-50 transition-colors">
                                <i class="fas fa-sticky-note text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Quick Note</span>
                            </button>
                            <button onclick="scheduleMeeting()" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-workspace-500 hover:bg-workspace-50 transition-colors">
                                <i class="fas fa-calendar-plus text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Schedule Meeting</span>
                            </button>
                            <a href="email.php" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-workspace-500 hover:bg-workspace-50 transition-colors">
                                <i class="fas fa-envelope text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Email</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Notification functionality
        let notificationDropdown = document.getElementById('notificationDropdown');
        let notificationBell = document.getElementById('notificationBell');
        let notificationList = document.getElementById('notificationList');
        let markAllReadBtn = document.getElementById('markAllRead');
        
        // Toggle notification dropdown
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('opacity-0');
            notificationDropdown.classList.toggle('invisible');
            
            if (!notificationDropdown.classList.contains('invisible')) {
                loadNotifications();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                notificationDropdown.classList.add('opacity-0', 'invisible');
            }
        });
        
        // Load notifications
        function loadNotifications() {
            fetch('../notification-api.php?action=list&limit=10')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNotifications(data.data);
                    } else {
                        notificationList.innerHTML = '<div class="p-4 text-center text-red-500"><p>Failed to load notifications</p></div>';
                    }
                })
                .catch(error => {
                    notificationList.innerHTML = '<div class="p-4 text-center text-red-500"><p>Error loading notifications</p></div>';
                });
        }
        
        // Display notifications
        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="fas fa-bell-slash text-lg mb-2"></i><p>No notifications</p></div>';
                return;
            }
            
            let html = '';
            notifications.forEach(notification => {
                const isUnread = !notification.is_read;
                const typeInfo = getNotificationTypeInfo(notification.type);
                const timeAgo = getTimeAgo(notification.created_at);
                
                html += `
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 ${isUnread ? 'bg-blue-50' : ''}" data-id="${notification.id}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="${typeInfo.icon} ${typeInfo.color} text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">${timeAgo}</span>
                                        ${isUnread ? '<span class="w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    ${isUnread ? `<button onclick="markAsRead('${notification.id}')" class="text-xs text-workspace-600 hover:text-workspace-700">Mark as read</button>` : ''}
                                    <button onclick="deleteNotification('${notification.id}')" class="text-xs text-red-600 hover:text-red-700">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            notificationList.innerHTML = html;
        }
        
        // Get notification type info
        function getNotificationTypeInfo(type) {
            const types = {
                'info': { icon: 'fas fa-info-circle', color: 'text-blue-500' },
                'success': { icon: 'fas fa-check-circle', color: 'text-green-500' },
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'text-yellow-500' },
                'error': { icon: 'fas fa-times-circle', color: 'text-red-500' },
                'system': { icon: 'fas fa-cog', color: 'text-gray-500' },
                'update': { icon: 'fas fa-sync-alt', color: 'text-purple-500' }
            };
            return types[type] || types['info'];
        }
        
        // Get time ago
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
            return Math.floor(diffInSeconds / 86400) + 'd ago';
        }
        
        // Mark notification as read
        function markAsRead(notificationId) {
            fetch('../notification-api.php?action=mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `notification_id=${notificationId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    updateNotificationCount();
                }
            });
        }
        
        // Delete notification
        function deleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                fetch('../notification-api.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `notification_id=${notificationId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        updateNotificationCount();
                    }
                });
            }
        }
        
        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('../notification-api.php?action=mark-all-read', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        updateNotificationCount();
                    }
                });
            });
        }
        
        // Update notification count
        function updateNotificationCount() {
            fetch('../notification-api.php?action=count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const count = data.data.unread_count;
                        const badge = notificationBell.querySelector('span');
                        
                        if (count > 0) {
                            if (badge) {
                                badge.textContent = count > 99 ? '99+' : count;
                            } else {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center';
                                newBadge.textContent = count > 99 ? '99+' : count;
                                notificationBell.appendChild(newBadge);
                            }
                        } else if (badge) {
                            badge.remove();
                        }
                    }
                });
        }
        
        // Update notification count every 30 seconds
        setInterval(updateNotificationCount, 30000);
        
        // Quick action functions
        function createDocument() {
            alert('Document creation will be implemented');
        }
        
        function createProject() {
            alert('Project creation will be implemented');
        }
        
        function createNote() {
            alert('Note creation will be implemented');
        }
        
        function scheduleMeeting() {
            alert('Meeting scheduling will be implemented');
        }
    </script>
</body>
</html> 