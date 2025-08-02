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
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'is_admin' => isset($_POST['is_admin']) ? 1 : 0,
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
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'is_admin' => isset($_POST['is_admin']) ? 1 : 0,
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
                $isActive = $_POST['is_active'] === 'true' ? 1 : 0;
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
    if (is_string($user['user_roles'])) {
        $roles_string = trim($user['user_roles'], '{}');
        $user['roles'] = $roles_string ? explode(',', $roles_string) : [];
    } elseif (!is_array($user['user_roles'])) {
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

    <!-- Create User Modal -->
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New User</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_user">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Roles</label>
                        <div class="mt-2 space-y-2">
                            <?php foreach ($roles as $role): ?>
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($role['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_admin" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Admin</span>
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit User</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="edit_username" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="edit_email" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Roles</label>
                        <div class="mt-2 space-y-2" id="edit_roles">
                            <?php foreach ($roles as $role): ?>
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" class="edit-role-checkbox rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($role['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_admin" id="edit_is_admin" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Admin</span>
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        function openEditModal(userId) {
            // Get user data and populate form
            const user = <?php echo json_encode($users); ?>.find(u => u.id === userId);
            if (user) {
                document.getElementById('edit_user_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_first_name').value = user.first_name || '';
                document.getElementById('edit_last_name').value = user.last_name || '';
                document.getElementById('edit_is_active').checked = user.is_active;
                document.getElementById('edit_is_admin').checked = user.is_admin;
                
                // Clear and set role checkboxes
                const roleCheckboxes = document.querySelectorAll('.edit-role-checkbox');
                roleCheckboxes.forEach(checkbox => {
                    checkbox.checked = user.roles.includes(checkbox.nextElementSibling.textContent.trim());
                });
                
                document.getElementById('editModal').classList.remove('hidden');
            }
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
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
            }
        }
    </script>
</body>
</html> 