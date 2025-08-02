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

$db = Database::getInstance();
$userManager = new User($db);

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
                    <button class="p-2 text-gray-400 hover:text-gray-500">
                        <i class="fas fa-bell text-lg"></i>
                    </button>
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