<?php
/**
 * BitSync Group Admin - User Management
 * Manage users, roles, and permissions
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check permissions
$hasUserPermission = isset($_SESSION['admin_permissions']) && 
    (in_array('*', $_SESSION['admin_permissions']) || 
     in_array('user_manage', $_SESSION['admin_permissions']));

if (!$hasUserPermission && $_SESSION['auth_method'] !== 'fallback') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';

$db = Database::getInstance();
$userManager = new User($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create_user':
                $userData = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'password' => $_POST['password'],
                    'first_name' => trim($_POST['first_name']),
                    'last_name' => trim($_POST['last_name']),
                    'is_active' => isset($_POST['is_active']),
                    'is_admin' => isset($_POST['is_admin']),
                    'roles' => $_POST['roles'] ?? []
                ];
                
                // Validation
                if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
                    throw new Exception('Username, email, and password are required');
                }
                
                if ($userManager->usernameExists($userData['username'])) {
                    throw new Exception('Username already exists');
                }
                
                if ($userManager->emailExists($userData['email'])) {
                    throw new Exception('Email already exists');
                }
                
                $userManager->createUser($userData);
                $message = 'User created successfully';
                break;
                
            case 'update_user':
                $userId = $_POST['user_id'];
                $userData = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'first_name' => trim($_POST['first_name']),
                    'last_name' => trim($_POST['last_name']),
                    'is_active' => isset($_POST['is_active']),
                    'is_admin' => isset($_POST['is_admin']),
                    'roles' => $_POST['roles'] ?? []
                ];
                
                // Add password if provided
                if (!empty($_POST['password'])) {
                    $userData['password'] = $_POST['password'];
                }
                
                // Validation
                if (empty($userData['username']) || empty($userData['email'])) {
                    throw new Exception('Username and email are required');
                }
                
                if ($userManager->usernameExists($userData['username'], $userId)) {
                    throw new Exception('Username already exists');
                }
                
                if ($userManager->emailExists($userData['email'], $userId)) {
                    throw new Exception('Email already exists');
                }
                
                $userManager->updateUser($userId, $userData);
                $message = 'User updated successfully';
                break;
                
            case 'delete_user':
                $userId = $_POST['user_id'];
                $userManager->deleteUser($userId);
                $message = 'User deleted successfully';
                break;
                
            case 'toggle_user_status':
                $userId = $_POST['user_id'];
                $isActive = $_POST['is_active'] === 'true';
                $userManager->updateUser($userId, ['is_active' => $isActive]);
                $message = 'User status updated successfully';
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users and roles
$users = $userManager->getAllUsers();
$roles = $userManager->getAllRoles();

// Process user roles for display
foreach ($users as &$user) {
    if (is_string($user['roles'])) {
        $roles_string = trim($user['roles'], '{}');
        $user['roles'] = $roles_string ? explode(',', $roles_string) : [];
    } elseif (!is_array($user['roles'])) {
        $user['roles'] = [];
    }
}
unset($user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - BitSync Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add User
                        </button>
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

            <!-- Users Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Users</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @<?php echo htmlspecialchars($user['username']); ?>
                                                    <?php if ($user['is_admin']): ?>
                                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Admin
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($user['roles'] as $role): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?php echo htmlspecialchars($role); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="openEditModal('<?php echo $user['id']; ?>')" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['id'] !== $_SESSION['admin_user_id']): ?>
                                                <button onclick="toggleUserStatus('<?php echo $user['id']; ?>', <?php echo $user['is_active'] ? 'false' : 'true'; ?>)" class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fas fa-<?php echo $user['is_active'] ? 'ban' : 'check'; ?>"></i>
                                                </button>
                                                <button onclick="deleteUser('<?php echo $user['id']; ?>')" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openCreateModal() {
            alert('Create user functionality will be implemented');
        }
        
        function openEditModal(userId) {
            alert('Edit user functionality will be implemented');
        }
        
        function toggleUserStatus(userId, isActive) {
            if (confirm('Are you sure you want to ' + (isActive ? 'activate' : 'deactivate') + ' this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_user_status">
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="is_active" value="${isActive}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 