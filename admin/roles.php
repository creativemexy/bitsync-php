<?php
/**
 * BitSync Group Admin - Role Management
 * Manage user roles and permissions
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
            case 'create_role':
                $roleData = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'permissions' => json_encode($_POST['permissions'] ?? []),
                    'is_active' => isset($_POST['is_active'])
                ];
                
                // Validation
                if (empty($roleData['name'])) {
                    throw new Exception('Role name is required');
                }
                
                $db->insert('roles', $roleData);
                $message = 'Role created successfully';
                break;
                
            case 'update_role':
                $roleId = $_POST['role_id'];
                $roleData = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'permissions' => json_encode($_POST['permissions'] ?? []),
                    'is_active' => isset($_POST['is_active'])
                ];
                
                // Validation
                if (empty($roleData['name'])) {
                    throw new Exception('Role name is required');
                }
                
                $db->update('roles', $roleData, 'id = ?', [$roleId]);
                $message = 'Role updated successfully';
                break;
                
            case 'delete_role':
                $roleId = $_POST['role_id'];
                $db->delete('roles', 'id = ?', [$roleId]);
                $message = 'Role deleted successfully';
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all roles
$roles = $userManager->getAllRoles();

// Available permissions
$availablePermissions = [
    'dashboard_view' => 'View Dashboard',
    'user_manage' => 'Manage Users',
    'content_manage' => 'Manage Content',
    'blog_manage' => 'Manage Blog',
    'blog_edit' => 'Edit Blog Posts',
    'pages_edit' => 'Edit Pages',
    'chat_view' => 'View Chat',
    'analytics_view' => 'View Analytics',
    'subscribers_manage' => 'Manage Subscribers',
    'contacts_manage' => 'Manage Contacts'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - BitSync Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-900">Role Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Role
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

            <!-- Roles Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Roles</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($role['name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($role['description']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <?php 
                                            $permissions = json_decode($role['permissions'], true) ?: [];
                                            foreach ($permissions as $permission): 
                                                if (isset($availablePermissions[$permission])):
                                            ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?php echo htmlspecialchars($availablePermissions[$permission]); ?>
                                                </span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $role['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="openEditModal('<?php echo $role['id']; ?>')" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($role['name'] !== 'admin'): ?>
                                                <button onclick="deleteRole('<?php echo $role['id']; ?>')" class="text-red-600 hover:text-red-900">
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

    <!-- Create Role Modal -->
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Role</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_role">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role Name</label>
                        <input type="text" name="name" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <div class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                            <?php foreach ($availablePermissions as $key => $label): ?>
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create Role
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
        
        function openEditModal(roleId) {
            alert('Edit role functionality will be implemented');
        }
        
        function deleteRole(roleId) {
            if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_role">
                    <input type="hidden" name="role_id" value="${roleId}">
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