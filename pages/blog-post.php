<?php
/**
 * Individual Blog Post Page
 * Displays a single blog post with related content
 */

require_once __DIR__ . '/../includes/Database.php';

// Get post slug from URL
$post_slug = $_GET['slug'] ?? '';

if (empty($post_slug)) {
    header('Location: /blog');
    exit;
}

// Initialize variables
$post = null;
$related_posts = [];
$recent_posts = [];
$show_fallback = false;

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
        $show_fallback = true;
    } else {
        // Tables exist, proceed with normal functionality
        // Get post details
        $post = $db->fetchOne("
            SELECT 
                bp.*,
                bc.name as category_name,
                bc.slug as category_slug,
                array_agg(DISTINCT bt.name) as tags
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id
            LEFT JOIN blog_tags bt ON bpt.tag_id = bt.id
            WHERE bp.slug = ? AND bp.status = 'published' AND bp.is_active = true
            GROUP BY bp.id, bc.name, bc.slug
        ", [$post_slug]);

        if (!$post) {
            // Post not found, redirect to blog
            header('Location: /blog');
            exit;
        }

        // Increment view count
        $db->query("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?", [$post['id']]);

        // Get related posts
        $related_posts = $db->fetchAll("
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
            WHERE bp.id != ? 
            AND bp.status = 'published' 
            AND bp.is_active = true
            AND (bp.category_id = ? OR bp.id IN (
                SELECT DISTINCT bpt2.post_id 
                FROM blog_post_tags bpt1
                JOIN blog_post_tags bpt2 ON bpt1.tag_id = bpt2.tag_id
                WHERE bpt1.post_id = ?
            ))
            ORDER BY bp.published_at DESC
            LIMIT 3
        ", [$post['id'], $post['category_id'], $post['id']]);

        // Get recent posts
        $recent_posts = $db->fetchAll("
            SELECT 
                bp.id,
                bp.title,
                bp.slug,
                bp.published_at,
                bc.name as category_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            WHERE bp.id != ? 
            AND bp.status = 'published' 
            AND bp.is_active = true
            ORDER BY bp.published_at DESC
            LIMIT 5
        ", [$post['id']]);
    }
    
} catch (Exception $e) {
    // Database error, show fallback content
    $show_fallback = true;
}

// Set page metadata
if ($post) {
    $page_title = $post['meta_title'] ?? $post['title'] . ' - Blog - BitSync Group';
    $page_description = $post['meta_description'] ?? $post['excerpt'];
    $page_keywords = $post['meta_keywords'] ?? '';
    
    // Calculate reading time (rough estimate: 200 words per minute)
    $word_count = str_word_count(strip_tags($post['content']));
    $reading_time = max(1, round($word_count / 200));
} else {
    $page_title = 'Post Not Found - Blog - BitSync Group';
    $page_description = 'The requested blog post could not be found.';
}
?>

<?php if ($show_fallback): ?>
<!-- Fallback Content -->
<section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800 py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-yellow-800 dark:text-yellow-200 mb-4">Blog System Not Available</h2>
            <p class="text-yellow-700 dark:text-yellow-300 mb-6">
                The blog system needs to be initialized. Please set up the database and create blog posts first.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="/blog" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Blog
                </a>
                <a href="/admin/login.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>
                    Admin Panel
                </a>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800 py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Breadcrumb -->
            <nav class="flex justify-center mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="/" class="hover:text-blue-600 dark:hover:text-blue-400">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="/blog" class="hover:text-blue-600 dark:hover:text-blue-400">Blog</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>

            <!-- Category Badge -->
            <?php if ($post['category_name']): ?>
            <div class="mb-6">
                <a href="/blog?category=<?php echo $post['category_slug']; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 transition-colors">
                    <?php echo htmlspecialchars($post['category_name']); ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <!-- Meta Information -->
            <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-8">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <?php echo date('F j, Y', strtotime($post['published_at'])); ?>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <?php echo $reading_time; ?> min read
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <?php echo $post['view_count']; ?> views
                </div>
            </div>

            <!-- Excerpt -->
            <?php if ($post['excerpt']): ?>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                <?php echo htmlspecialchars($post['excerpt']); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Image -->
<?php if ($post['featured_image']): ?>
<section class="py-8 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
             alt="<?php echo htmlspecialchars($post['title']); ?>" 
             class="w-full h-64 md:h-96 object-cover rounded-2xl shadow-lg">
    </div>
</section>
<?php endif; ?>

<!-- Main Content -->
<section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Recent Posts -->
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Posts</h3>
                    <div class="space-y-4">
                        <?php foreach ($recent_posts as $recent): ?>
                        <article class="border-b border-gray-200 dark:border-slate-700 pb-4 last:border-b-0">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                                <a href="/blog/<?php echo $recent['slug']; ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <?php echo htmlspecialchars($recent['title']); ?>
                                </a>
                            </h4>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <span><?php echo date('M j, Y', strtotime($recent['published_at'])); ?></span>
                                <?php if ($recent['category_name']): ?>
                                <span class="mx-2">•</span>
                                <span><?php echo htmlspecialchars($recent['category_name']); ?></span>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tags -->
                <?php if (!empty($post['tags']) && $post['tags'][0] !== null): ?>
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($post['tags'] as $tag): ?>
                        <a href="/blog?tag=<?php echo strtolower(str_replace(' ', '-', $tag)); ?>" 
                           class="px-3 py-1 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-sm rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Article Content -->
            <div class="lg:col-span-3">
                <article class="prose prose-lg max-w-none dark:prose-invert">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-8">
                        <?php echo $post['content']; ?>
                    </div>
                </article>

                <!-- Share Buttons -->
                <div class="mt-8 bg-gray-50 dark:bg-slate-800 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Share this article</h3>
                    <div class="flex space-x-4">
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/blog/' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/blog/' . $post['slug']); ?>" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                            LinkedIn
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/blog/' . $post['slug']); ?>" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts -->
<?php if (!empty($related_posts)): ?>
<section class="py-16 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related_posts as $related): ?>
            <article class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <?php if ($related['featured_image']): ?>
                <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($related['title']); ?>" 
                     class="w-full h-48 object-cover">
                <?php endif; ?>
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">
                            <?php echo htmlspecialchars($related['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo date('M j, Y', strtotime($related['published_at'])); ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        <a href="/blog/<?php echo $related['slug']; ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <?php echo htmlspecialchars($related['title']); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        <?php echo htmlspecialchars($related['excerpt']); ?>
                    </p>
                    <a href="/blog/<?php echo $related['slug']; ?>" 
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
<?php endif; ?> 