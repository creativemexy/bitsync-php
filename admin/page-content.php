<?php
/**
 * Page Content Management
 * Admin interface for managing page content in database
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';

$db = Database::getInstance();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        $page_slug = trim($_POST['page_slug'] ?? '');
        $page_title = trim($_POST['page_title'] ?? '');
        $page_description = trim($_POST['page_description'] ?? '');
        $content = $_POST['content'] ?? '';
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        
        // Validate required fields
        if (empty($page_slug) || empty($page_title)) {
            $error = 'Page slug and title are required';
        } else {
            try {
                // Check if page exists
                $existing_page = $db->fetchOne("SELECT id FROM page_content WHERE page_slug = ?", [$page_slug]);
                
                if ($existing_page) {
                    // Update existing page
                    $page_data = [
                        'page_title' => $page_title,
                        'page_description' => $page_description,
                        'content' => $content,
                        'meta_title' => $meta_title,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $db->update('page_content', $page_data, 'page_slug = ?', [$page_slug]);
                    $message = 'Page content updated successfully!';
                } else {
                    // Create new page
                    $page_data = [
                        'page_slug' => $page_slug,
                        'page_title' => $page_title,
                        'page_description' => $page_description,
                        'content' => $content,
                        'meta_title' => $meta_title,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords
                    ];
                    
                    $db->insert('page_content', $page_data);
                    $message = 'Page content created successfully!';
                }
            } catch (Exception $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $page_slug = $_POST['page_slug'] ?? '';
        if ($page_slug) {
            try {
                $db->query("DELETE FROM page_content WHERE page_slug = ?", [$page_slug]);
                $message = 'Page content deleted successfully!';
            } catch (Exception $e) {
                $error = 'Error deleting page: ' . $e->getMessage();
            }
        }
    }
}

// Get pages for listing
$pages = $db->fetchAll("
    SELECT * FROM page_content 
    ORDER BY updated_at DESC
");

// Get page for editing
$edit_page = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $edit_page = $db->fetchOne("SELECT * FROM page_content WHERE page_slug = ?", [$_GET['edit']]);
}

// Get current page content from files
$current_pages = [
    'about' => [
        'title' => 'About Us',
        'file' => '../pages/about.php'
    ],
    'services' => [
        'title' => 'Our Services',
        'file' => '../pages/services.php'
    ],
    'contact' => [
        'title' => 'Contact Us',
        'file' => '../pages/contact.php'
    ],
    'solutions' => [
        'title' => 'Solutions',
        'file' => '../pages/solutions.php'
    ]
];

// Function to extract content from PHP files
function extractContentFromFile($file_path) {
    if (!file_exists($file_path)) {
        return '';
    }
    
    $content = file_get_contents($file_path);
    
    // Remove PHP tags and extract HTML content
    $content = preg_replace('/<\?php.*?\?>/s', '', $content);
    $content = trim($content);
    
    return $content;
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <?php echo $edit_page ? 'Edit Page Content' : 'Save Page Content'; ?>
                        </h2>
                        
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="save">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Slug *</label>
                                <select name="page_slug" required 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                    <option value="">Select Page</option>
                                    <?php foreach ($current_pages as $slug => $page_info): ?>
                                    <option value="<?php echo $slug; ?>" 
                                            <?php echo ($edit_page['page_slug'] ?? '') === $slug ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($page_info['title']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Title *</label>
                                <input type="text" name="page_title" required 
                                       value="<?php echo htmlspecialchars($edit_page['page_title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Description</label>
                                <textarea name="page_description" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"><?php echo htmlspecialchars($edit_page['page_description'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                                <textarea id="content" name="content"><?php echo htmlspecialchars($edit_page['content'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" 
                                       value="<?php echo htmlspecialchars($edit_page['meta_title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"><?php echo htmlspecialchars($edit_page['meta_description'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Keywords</label>
                                <input type="text" name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($edit_page['meta_keywords'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Save Content
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
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Saved Page Content</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Page</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                    <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($page['page_slug']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                            <?php echo date('M j, Y', strtotime($page['updated_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="?edit=<?php echo $page['page_slug']; ?>" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/<?php echo $page['page_slug']; ?>" target="_blank"
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this page content?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="page_slug" value="<?php echo $page['page_slug']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 