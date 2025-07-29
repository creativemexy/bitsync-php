<?php
/**
 * Blog Posts Management
 * Admin interface for managing blog posts
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
    
    if ($action === 'create' || $action === 'update') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = $_POST['content'] ?? '';
        $category_id = intval($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $featured_image = trim($_POST['featured_image'] ?? '');
        
        // Generate slug if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }
        
        // Validate required fields
        if (empty($title) || empty($content)) {
            $error = 'Title and content are required';
        } else {
            try {
                if ($action === 'create') {
                    $post_data = [
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'category_id' => $category_id ?: null,
                        'author_id' => $_SESSION['admin_user_id'],
                        'status' => $status,
                        'is_featured' => $is_featured,
                        'meta_title' => $meta_title,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'featured_image' => $featured_image,
                        'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null
                    ];
                    
                    $post_id = $db->insert('blog_posts', $post_data);
                    
                    // Handle tags
                    if ($post_id && isset($_POST['tags']) && is_array($_POST['tags'])) {
                        foreach ($_POST['tags'] as $tag_id) {
                            $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)", [$post_id, $tag_id]);
                        }
                    }
                    
                    $message = 'Post created successfully!';
                } else {
                    $post_id = intval($_POST['post_id']);
                    
                    $post_data = [
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'category_id' => $category_id ?: null,
                        'status' => $status,
                        'is_featured' => $is_featured,
                        'meta_title' => $meta_title,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'featured_image' => $featured_image,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Set published_at if status changed to published
                    if ($status === 'published') {
                        $current_post = $db->fetchOne("SELECT published_at FROM blog_posts WHERE id = ?", [$post_id]);
                        if (!$current_post['published_at']) {
                            $post_data['published_at'] = date('Y-m-d H:i:s');
                        }
                    }
                    
                    $db->update('blog_posts', $post_data, 'id = ?', [$post_id]);
                    
                    // Update tags
                    $db->query("DELETE FROM blog_post_tags WHERE post_id = ?", [$post_id]);
                    if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                        foreach ($_POST['tags'] as $tag_id) {
                            $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)", [$post_id, $tag_id]);
                        }
                    }
                    
                    $message = 'Post updated successfully!';
                }
            } catch (Exception $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $post_id = intval($_POST['post_id'] ?? 0);
        if ($post_id) {
            try {
                $db->query("DELETE FROM blog_post_tags WHERE post_id = ?", [$post_id]);
                $db->query("DELETE FROM blog_posts WHERE id = ?", [$post_id]);
                $message = 'Post deleted successfully!';
            } catch (Exception $e) {
                $error = 'Error deleting post: ' . $e->getMessage();
            }
        }
    }
}

// Get posts for listing
$posts = $db->fetchAll("
    SELECT 
        bp.*,
        bc.name as category_name,
        array_agg(DISTINCT bt.name) as tags
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id
    LEFT JOIN blog_tags bt ON bpt.tag_id = bt.id
    GROUP BY bp.id, bc.name
    ORDER BY bp.created_at DESC
");

// Get categories for form
$categories = $db->fetchAll("SELECT id, name FROM blog_categories WHERE is_active = true ORDER BY name");

// Get tags for form
$tags = $db->fetchAll("SELECT id, name FROM blog_tags WHERE is_active = true ORDER BY name");

// Get post for editing
$edit_post = null;
if (isset($_GET['edit']) && intval($_GET['edit'])) {
    $edit_post = $db->fetchOne("
        SELECT 
            bp.*,
            array_agg(DISTINCT bpt.tag_id) as tag_ids
        FROM blog_posts bp
        LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id
        WHERE bp.id = ?
        GROUP BY bp.id
    ", [intval($_GET['edit'])]);
    
    if ($edit_post) {
        $edit_post['tag_ids'] = $edit_post['tag_ids'] ? explode(',', $edit_post['tag_ids']) : [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts Management - BitSync Admin</title>
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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Blog Posts Management</h1>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <a href="blog-categories.php" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-tags mr-2"></i>Categories
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
                            <?php echo $edit_post ? 'Edit Post' : 'Create New Post'; ?>
                        </h2>
                        
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="<?php echo $edit_post ? 'update' : 'create'; ?>">
                            <?php if ($edit_post): ?>
                            <input type="hidden" name="post_id" value="<?php echo $edit_post['id']; ?>">
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                                <input type="text" name="title" required 
                                       value="<?php echo htmlspecialchars($edit_post['title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Slug</label>
                                <input type="text" name="slug" 
                                       value="<?php echo htmlspecialchars($edit_post['slug'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Excerpt</label>
                                <textarea name="excerpt" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"><?php echo htmlspecialchars($edit_post['excerpt'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content *</label>
                                <textarea id="content" name="content" required><?php echo htmlspecialchars($edit_post['content'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo ($edit_post['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                                <select name="tags[]" multiple class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                    <?php foreach ($tags as $tag): ?>
                                    <option value="<?php echo $tag['id']; ?>" 
                                            <?php echo in_array($tag['id'], $edit_post['tag_ids'] ?? []) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Featured Image URL</label>
                                <input type="url" name="featured_image" 
                                       value="<?php echo htmlspecialchars($edit_post['featured_image'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                    <option value="draft" <?php echo ($edit_post['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo ($edit_post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                                       <?php echo ($edit_post['is_featured'] ?? 0) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Featured Post</label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" 
                                       value="<?php echo htmlspecialchars($edit_post['meta_title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"><?php echo htmlspecialchars($edit_post['meta_description'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Keywords</label>
                                <input type="text" name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($edit_post['meta_keywords'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <?php echo $edit_post ? 'Update Post' : 'Create Post'; ?>
                                </button>
                                <?php if ($edit_post): ?>
                                <a href="blog-posts.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Posts List -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Posts</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Views</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                    <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php if ($post['is_featured']): ?>
                                                <span class="text-yellow-500 mr-2" title="Featured">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        <?php echo htmlspecialchars($post['title']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        <?php echo htmlspecialchars($post['slug']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                       <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($post['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo $post['view_count']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="/blog/<?php echo $post['slug']; ?>" target="_blank" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?edit=<?php echo $post['id']; ?>" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
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