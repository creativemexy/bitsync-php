<?php
/**
 * Page Content Management
 * Admin interface for managing page content
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$message = '';
$error = '';
$pages = [];
$edit_page = null;
$db_available = false;

// Try to connect to database
try {
    require_once __DIR__ . '/../includes/Database.php';
    $db = Database::getInstance();
    $db_available = true;
    
    // Handle form submissions only if database is available
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            $page_name = trim($_POST['page_name'] ?? '');
            $page_title = trim($_POST['page_title'] ?? '');
            $page_description = trim($_POST['page_description'] ?? '');
            $page_keywords = trim($_POST['page_keywords'] ?? '');
            $content = $_POST['content'] ?? '';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Validate required fields
            if (empty($page_name) || empty($page_title)) {
                $error = 'Page name and title are required';
            } else {
                try {
                    if ($action === 'create') {
                        $page_data = [
                            'page_name' => $page_name,
                            'page_title' => $page_title,
                            'page_description' => $page_description,
                            'page_keywords' => $page_keywords,
                            'content' => $content,
                            'is_active' => $is_active
                        ];
                        
                        $db->insert('page_content', $page_data);
                        $message = 'Page content created successfully!';
                    } else {
                        $page_id = intval($_POST['page_id']);
                        
                        $page_data = [
                            'page_name' => $page_name,
                            'page_title' => $page_title,
                            'page_description' => $page_description,
                            'page_keywords' => $page_keywords,
                            'content' => $content,
                            'is_active' => $is_active,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $db->update('page_content', $page_data, 'id = ?', [$page_id]);
                        $message = 'Page content updated successfully!';
                    }
                } catch (Exception $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'delete') {
            $page_id = intval($_POST['page_id'] ?? 0);
            if ($page_id) {
                try {
                    $db->query("DELETE FROM page_content WHERE id = ?", [$page_id]);
                    $message = 'Page content deleted successfully!';
                } catch (Exception $e) {
                    $error = 'Error deleting page content: ' . $e->getMessage();
                }
            }
        }
    }

    // Get pages for listing
    $pages = $db->fetchAll("SELECT * FROM page_content ORDER BY page_name");

    // Get page for editing
    if (isset($_GET['edit']) && intval($_GET['edit'])) {
        $edit_page = $db->fetchOne("SELECT * FROM page_content WHERE id = ?", [intval($_GET['edit'])]);
    }
    
} catch (Exception $e) {
    // Database connection failed
    $error = 'Database connection failed. Please check your database configuration.';
    $db_available = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Content Management - BitSync Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if ($db_available): ?>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            height: 400
        });
    </script>
    <?php endif; ?>
</head>
<body class="bg-gray-50 dark:bg-slate-900">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white dark:bg-slate-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Page Content Management</h1>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <a href="blog-posts.php" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-file-alt mr-2"></i>Blog Posts
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Messages -->
            <?php if ($message): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if (!$db_available): ?>
            <!-- Database Connection Error -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-8 mb-8">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-yellow-800 dark:text-yellow-200 mb-4 text-center">Database Connection Required</h2>
                <p class="text-yellow-700 dark:text-yellow-300 mb-6 text-center">
                    The page content management system requires a database connection. Please configure your database settings first.
                </p>
                <div class="bg-white dark:bg-slate-800 rounded-lg p-4 text-left">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Setup Steps:</h3>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        <li>Configure your database credentials in the <code class="bg-gray-100 dark:bg-slate-700 px-1 rounded">.env</code> file</li>
                        <li>Run the migration: <code class="bg-gray-100 dark:bg-slate-700 px-1 rounded">php database/migrate-blog.php</code></li>
                        <li>Refresh this page to access content management</li>
                    </ol>
                </div>
                <div class="flex justify-center space-x-4 mt-6">
                    <a href="test-db.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-database mr-2"></i>
                        Test Database
                    </a>
                    <a href="create-admin.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Admin User
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <?php echo $edit_page ? 'Edit Page Content' : 'Create New Page Content'; ?>
                        </h2>
                        
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="<?php echo $edit_page ? 'update' : 'create'; ?>">
                            <?php if ($edit_page): ?>
                            <input type="hidden" name="page_id" value="<?php echo $edit_page['id']; ?>">
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Name *</label>
                                <input type="text" name="page_name" required 
                                       value="<?php echo htmlspecialchars($edit_page['page_name'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">e.g., home, about, services</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Title *</label>
                                <input type="text" name="page_title" required 
                                       value="<?php echo htmlspecialchars($edit_page['page_title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                                <textarea name="page_description" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"><?php echo htmlspecialchars($edit_page['page_description'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Keywords</label>
                                <input type="text" name="page_keywords" 
                                       value="<?php echo htmlspecialchars($edit_page['page_keywords'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                                <textarea id="content" name="content"><?php echo htmlspecialchars($edit_page['content'] ?? ''); ?></textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       <?php echo ($edit_page['is_active'] ?? 1) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Active</label>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <?php echo $edit_page ? 'Update Content' : 'Create Content'; ?>
                                </button>
                                <?php if ($edit_page): ?>
                                <a href="page-content.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pages List -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Page Content</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Page Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Updated</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                    <?php if (empty($pages)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No page content found. Create your first page content using the form on the left.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($page['page_name']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($page['page_title']); ?>
                                            </div>
                                            <?php if ($page['page_description']): ?>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo htmlspecialchars(substr($page['page_description'], 0, 50)) . (strlen($page['page_description']) > 50 ? '...' : ''); ?>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                       <?php echo $page['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $page['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo date('M j, Y', strtotime($page['updated_at'] ?? $page['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="?edit=<?php echo $page['id']; ?>" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this page content?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 