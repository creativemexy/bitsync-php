<?php
/**
 * BitSync Group Admin - Services Management
 * Manage services and offerings
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check permissions
$hasContentPermission = isset($_SESSION['admin_permissions']) && 
    (in_array('*', $_SESSION['admin_permissions']) || 
     in_array('content_manage', $_SESSION['admin_permissions']));

if (!$hasContentPermission && $_SESSION['auth_method'] !== 'fallback') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';

$db = Database::getInstance();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create_service':
                $serviceData = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'icon' => trim($_POST['icon']),
                    'is_active' => isset($_POST['is_active']),
                    'sort_order' => (int)($_POST['sort_order'] ?? 0)
                ];
                
                // Validation
                if (empty($serviceData['name'])) {
                    throw new Exception('Service name is required');
                }
                
                $db->insert('services', $serviceData);
                $message = 'Service created successfully';
                break;
                
            case 'update_service':
                $serviceId = $_POST['service_id'];
                $serviceData = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'icon' => trim($_POST['icon']),
                    'is_active' => isset($_POST['is_active']),
                    'sort_order' => (int)($_POST['sort_order'] ?? 0)
                ];
                
                // Validation
                if (empty($serviceData['name'])) {
                    throw new Exception('Service name is required');
                }
                
                $db->update('services', $serviceData, 'id = :service_id', ['service_id' => $serviceId]);
                $message = 'Service updated successfully';
                break;
                
            case 'delete_service':
                $serviceId = $_POST['service_id'];
                $db->delete('services', 'id = :service_id', ['service_id' => $serviceId]);
                $message = 'Service deleted successfully';
                break;
                
            case 'toggle_service_status':
                $serviceId = $_POST['service_id'];
                $isActive = $_POST['is_active'] === 'true';
                $db->update('services', ['is_active' => $isActive], 'id = :service_id', ['service_id' => $serviceId]);
                $message = 'Service status updated successfully';
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all services
$services = $db->fetchAll("SELECT * FROM services ORDER BY sort_order, name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - BitSync Admin</title>
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
                        <h1 class="text-2xl font-bold text-gray-900">Services Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Service
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

            <!-- Services Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Services</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($service['name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            <?php echo htmlspecialchars($service['description']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?> text-2xl text-gray-600"></i>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $service['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $service['sort_order']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="openEditModal('<?php echo $service['id']; ?>')" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="toggleServiceStatus('<?php echo $service['id']; ?>', <?php echo $service['is_active'] ? 'false' : 'true'; ?>)" class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-<?php echo $service['is_active'] ? 'ban' : 'check'; ?>"></i>
                                            </button>
                                            <button onclick="deleteService('<?php echo $service['id']; ?>')" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <!-- Create Service Modal -->
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Service</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_service">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Name</label>
                        <input type="text" name="name" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Icon (FontAwesome class)</label>
                        <input type="text" name="icon" placeholder="fas fa-cog" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" value="0" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
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
                            Create Service
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
        
        function openEditModal(serviceId) {
            alert('Edit service functionality will be implemented');
        }
        
        function toggleServiceStatus(serviceId, isActive) {
            if (confirm('Are you sure you want to ' + (isActive ? 'activate' : 'deactivate') + ' this service?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_service_status">
                    <input type="hidden" name="service_id" value="${serviceId}">
                    <input type="hidden" name="is_active" value="${isActive}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteService(serviceId) {
            if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_service">
                    <input type="hidden" name="service_id" value="${serviceId}">
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