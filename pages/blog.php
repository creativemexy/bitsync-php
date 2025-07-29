<?php
/**
 * Dynamic Blog Page
 * Displays blog posts from database with filtering and pagination
 */

require_once __DIR__ . '/../includes/Database.php';

// Initialize variables
$posts = [];
$categories = [];
$popular_tags = [];
$featured_posts = [];
$total_posts = 0;
$total_pages = 0;
$page = 1;
$category = '';
$search = '';
$tag = '';

// Check if database tables exist and handle gracefully
try {
    $db = Database::getInstance();
    
    // Test if blog tables exist
    $tables_exist = $db->fetchOne("
        SELECT COUNT(*) as count 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name IN ('blog_posts', 'blog_categories', 'blog_tags')
    ");
    
    if ($tables_exist['count'] < 3) {
        // Tables don't exist, show fallback content
        $page_title = 'Blog - BitSync Group';
        $page_description = 'Latest insights, tutorials, and updates from BitSync Group';
        $show_fallback = true;
    } else {
        // Tables exist, proceed with normal functionality
        $show_fallback = false;
        
        // Get query parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        $tag = $_GET['tag'] ?? '';
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // Build query conditions
        $where_conditions = ["bp.status = 'published'", "bp.is_active = true"];
        $params = [];

        if ($category) {
            $where_conditions[] = "bc.slug = ?";
            $params[] = $category;
        }

        if ($search) {
            $where_conditions[] = "(bp.title ILIKE ? OR bp.excerpt ILIKE ? OR bp.content ILIKE ?)";
            $search_term = "%$search%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }

        if ($tag) {
            $where_conditions[] = "bt.slug = ?";
            $params[] = $tag;
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Get total count for pagination
        $count_sql = "
            SELECT COUNT(DISTINCT bp.id) as total
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id
            LEFT JOIN blog_tags bt ON bpt.tag_id = bt.id
            WHERE $where_clause
        ";

        $total_result = $db->fetchOne($count_sql, $params);
        $total_posts = $total_result['total'];
        $total_pages = ceil($total_posts / $limit);

        // Get blog posts
        $posts_sql = "
            SELECT 
                bp.id,
                bp.title,
                bp.slug,
                bp.excerpt,
                bp.featured_image,
                bp.published_at,
                bp.view_count,
                bp.is_featured,
                bc.name as category_name,
                bc.slug as category_slug,
                array_agg(DISTINCT bt.name) as tags
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id
            LEFT JOIN blog_tags bt ON bpt.tag_id = bt.id
            WHERE $where_clause
            GROUP BY bp.id, bc.name, bc.slug
            ORDER BY bp.is_featured DESC, bp.published_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;

        $posts = $db->fetchAll($posts_sql, $params);

        // Get categories for filter
        $categories = $db->fetchAll("
            SELECT bc.name, bc.slug, COUNT(bp.id) as post_count
            FROM blog_categories bc
            LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.status = 'published' AND bp.is_active = true
            WHERE bc.is_active = true
            GROUP BY bc.id, bc.name, bc.slug
            ORDER BY bc.sort_order, bc.name
        ");

        // Get popular tags
        $popular_tags = $db->fetchAll("
            SELECT bt.name, bt.slug, COUNT(bpt.post_id) as post_count
            FROM blog_tags bt
            JOIN blog_post_tags bpt ON bt.id = bpt.tag_id
            JOIN blog_posts bp ON bpt.post_id = bp.id AND bp.status = 'published' AND bp.is_active = true
            WHERE bt.is_active = true
            GROUP BY bt.id, bt.name, bt.slug
            ORDER BY post_count DESC
            LIMIT 10
        ");

        // Get featured posts
        $featured_posts = $db->fetchAll("
            SELECT 
                bp.id,
                bp.title,
                bp.slug,
                bp.excerpt,
                bp.featured_image,
                bp.published_at,
                bc.name as category_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            WHERE bp.is_featured = true AND bp.status = 'published' AND bp.is_active = true
            ORDER BY bp.published_at DESC
            LIMIT 3
        ");

        // Set page metadata
        $page_title = 'Blog - BitSync Group';
        $page_description = 'Latest insights, tutorials, and updates from BitSync Group';

        if ($category) {
            $cat_info = $db->fetchOne("SELECT name, description FROM blog_categories WHERE slug = ?", [$category]);
            if ($cat_info) {
                $page_title = $cat_info['name'] . ' - Blog - BitSync Group';
                $page_description = $cat_info['description'] ?? 'Latest ' . strtolower($cat_info['name']) . ' articles and insights';
            }
        }

        if ($search) {
            $page_title = 'Search Results for "' . htmlspecialchars($search) . '" - Blog - BitSync Group';
            $page_description = 'Search results for "' . htmlspecialchars($search) . '"';
        }
    }
    
} catch (Exception $e) {
    // Database error, show fallback content
    $page_title = 'Blog - BitSync Group';
    $page_description = 'Latest insights, tutorials, and updates from BitSync Group';
    $show_fallback = true;
}
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                <?php echo $page_title; ?>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                <?php echo $page_description; ?>
            </p>
            
            <?php if (!$show_fallback): ?>
            <!-- Search Bar -->
            <form method="GET" class="max-w-md mx-auto mb-8">
                <div class="relative">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search articles..." 
                           class="w-full px-4 py-3 pl-12 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                    <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($show_fallback): ?>
<!-- Fallback Content -->
<section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-yellow-800 dark:text-yellow-200 mb-4">Blog System Setup Required</h2>
            <p class="text-yellow-700 dark:text-yellow-300 mb-6">
                The blog system needs to be initialized. Please run the database migration to set up the blog tables.
            </p>
            <div class="space-y-4">
                <div class="bg-white dark:bg-slate-800 rounded-lg p-4 text-left">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Setup Steps:</h3>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        <li>Configure your database credentials in the <code class="bg-gray-100 dark:bg-slate-700 px-1 rounded">.env</code> file</li>
                        <li>Run the migration: <code class="bg-gray-100 dark:bg-slate-700 px-1 rounded">php database/migrate-blog.php</code></li>
                        <li>Access the admin panel to create blog posts</li>
                    </ol>
                </div>
                <div class="flex justify-center space-x-4">
                    <a href="/admin/login.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-cog mr-2"></i>
                        Admin Panel
                    </a>
                    <a href="/admin/test-db.php" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-database mr-2"></i>
                        Test Database
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Sample Blog Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <article class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                            Technology
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Coming Soon
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        Getting Started with Modern Web Development
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Learn the fundamentals of modern web development and the tools you need to succeed in today's digital landscape.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Coming Soon</span>
                    </div>
                </div>
            </article>
            
            <article class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">
                            Mobile Development
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Coming Soon
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        The Future of Mobile App Development
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Explore the latest trends and technologies shaping the future of mobile app development.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Coming Soon</span>
                    </div>
                </div>
            </article>
            
            <article class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-medium rounded-full">
                            Business
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Coming Soon
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        Digital Transformation Strategies
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Discover effective strategies for digital transformation and business innovation.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600 dark:text-blue-400 font-medium">Coming Soon</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>
<?php else: ?>
<!-- Featured Posts -->
<?php if (!empty($featured_posts) && $page == 1 && !$search && !$category && !$tag): ?>
<section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Featured Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($featured_posts as $post): ?>
            <article class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <?php if ($post['featured_image']): ?>
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     class="w-full h-48 object-cover">
                <?php endif; ?>
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                            <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        <a href="/blog/<?php echo $post['slug']; ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        <?php echo htmlspecialchars($post['excerpt']); ?>
                    </p>
                    <a href="/blog/<?php echo $post['slug']; ?>" 
                       class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        Read More
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Content -->
<section class="py-16 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Categories -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Categories</h3>
                    <div class="space-y-2">
                        <a href="/blog" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            All Posts (<?php echo $total_posts; ?>)
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="/blog?category=<?php echo $cat['slug']; ?>" 
                           class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo $category === $cat['slug'] ? 'text-blue-600 dark:text-blue-400 font-medium' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?> (<?php echo $cat['post_count']; ?>)
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Popular Tags -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Popular Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($popular_tags as $tag_item): ?>
                        <a href="/blog?tag=<?php echo $tag_item['slug']; ?>" 
                           class="px-3 py-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-sm rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300 transition-colors <?php echo $tag === $tag_item['slug'] ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : ''; ?>">
                            <?php echo htmlspecialchars($tag_item['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Blog Posts -->
            <div class="lg:col-span-3">
                <?php if (empty($posts)): ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No posts found</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        <?php if ($search): ?>
                            No posts found for "<?php echo htmlspecialchars($search); ?>"
                        <?php elseif ($category): ?>
                            No posts found in this category
                        <?php elseif ($tag): ?>
                            No posts found with this tag
                        <?php else: ?>
                            No blog posts available yet. <a href="/admin/blog-posts.php" class="text-blue-600 hover:text-blue-700">Create your first post</a>
                        <?php endif; ?>
                    </p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($posts as $post): ?>
                    <article class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <?php if ($post['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>" 
                             class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                                    <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                <a href="/blog/<?php echo $post['slug']; ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                            
                            <!-- Tags -->
                            <?php if (!empty($post['tags']) && $post['tags'][0] !== null): ?>
                            <div class="flex flex-wrap gap-1 mb-4">
                                <?php foreach ($post['tags'] as $post_tag): ?>
                                <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 text-xs rounded">
                                    <?php echo htmlspecialchars($post_tag); ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between">
                                <a href="/blog/<?php echo $post['slug']; ?>" 
                                   class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                    Read More
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo $post['view_count']; ?> views
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                           class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                           class="px-4 py-2 rounded-lg transition-colors <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                           class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            Next
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?> 