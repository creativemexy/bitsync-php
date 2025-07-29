<?php
/**
 * Blog System Database Migration
 * Creates tables for dynamic blog functionality
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Create blog_categories table
    $db->query("
        CREATE TABLE IF NOT EXISTS blog_categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT true,
            sort_order INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create blog_posts table
    $db->query("
        CREATE TABLE IF NOT EXISTS blog_posts (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            excerpt TEXT,
            content LONGTEXT NOT NULL,
            featured_image VARCHAR(500),
            category_id INTEGER,
            author_id INTEGER,
            status VARCHAR(20) DEFAULT 'draft',
            published_at TIMESTAMP NULL,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords VARCHAR(500),
            view_count INTEGER DEFAULT 0,
            is_featured BOOLEAN DEFAULT false,
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
        )
    ");
    
    // Create blog_tags table
    $db->query("
        CREATE TABLE IF NOT EXISTS blog_tags (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create blog_post_tags table (many-to-many relationship)
    $db->query("
        CREATE TABLE IF NOT EXISTS blog_post_tags (
            id SERIAL PRIMARY KEY,
            post_id INTEGER NOT NULL,
            tag_id INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE,
            UNIQUE(post_id, tag_id)
        )
    ");
    
    // Create page_content table for saving current page content
    $db->query("
        CREATE TABLE IF NOT EXISTS page_content (
            id SERIAL PRIMARY KEY,
            page_slug VARCHAR(255) UNIQUE NOT NULL,
            page_title VARCHAR(255) NOT NULL,
            page_description TEXT,
            content LONGTEXT,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords VARCHAR(500),
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert default blog categories
    $defaultCategories = [
        ['Technology', 'technology', 'Latest technology trends and insights'],
        ['Web Development', 'web-development', 'Web development tutorials and tips'],
        ['Mobile Development', 'mobile-development', 'Mobile app development guides'],
        ['Business', 'business', 'Business insights and strategies'],
        ['Case Studies', 'case-studies', 'Real-world project case studies']
    ];
    
    foreach ($defaultCategories as $category) {
        $db->query(
            "INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?) ON CONFLICT DO NOTHING",
            $category
        );
    }
    
    // Insert default blog tags
    $defaultTags = [
        'PHP', 'JavaScript', 'React', 'Vue', 'Angular', 'Node.js', 'Python', 'Mobile', 'Web', 'Design'
    ];
    
    foreach ($defaultTags as $tag) {
        $slug = strtolower(str_replace(' ', '-', $tag));
        $db->query(
            "INSERT INTO blog_tags (name, slug) VALUES (?, ?) ON CONFLICT DO NOTHING",
            [$tag, $slug]
        );
    }
    
    // Insert sample blog posts
    $samplePosts = [
        [
            'title' => 'Getting Started with Modern Web Development',
            'slug' => 'getting-started-with-modern-web-development',
            'excerpt' => 'Learn the fundamentals of modern web development and the tools you need to succeed.',
            'content' => '<h2>Introduction to Modern Web Development</h2><p>Modern web development has evolved significantly over the past decade. With the rise of JavaScript frameworks, cloud computing, and mobile-first design, developers need to stay updated with the latest trends and technologies.</p><h3>Key Technologies</h3><ul><li>HTML5 and CSS3</li><li>JavaScript ES6+</li><li>React, Vue, and Angular</li><li>Node.js and Express</li><li>Cloud platforms</li></ul><p>This comprehensive guide will walk you through the essential concepts and tools needed for modern web development.</p>',
            'category_id' => 2,
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'meta_title' => 'Modern Web Development Guide - BitSync Group',
            'meta_description' => 'Learn modern web development fundamentals and best practices with our comprehensive guide.',
            'is_featured' => true
        ],
        [
            'title' => 'The Future of Mobile App Development',
            'slug' => 'future-of-mobile-app-development',
            'excerpt' => 'Explore the latest trends and technologies shaping the future of mobile app development.',
            'content' => '<h2>The Future of Mobile App Development</h2><p>Mobile app development continues to evolve with new technologies and user expectations. From AI integration to cross-platform development, the landscape is constantly changing.</p><h3>Emerging Trends</h3><ul><li>Artificial Intelligence and Machine Learning</li><li>Cross-platform frameworks</li><li>Progressive Web Apps</li><li>5G technology</li><li>Augmented Reality</li></ul><p>Stay ahead of the curve by understanding these emerging trends and technologies.</p>',
            'category_id' => 3,
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'meta_title' => 'Future of Mobile App Development - BitSync Group',
            'meta_description' => 'Discover the latest trends and technologies in mobile app development.',
            'is_featured' => true
        ]
    ];
    
    foreach ($samplePosts as $post) {
        $postId = $db->insert('blog_posts', $post);
        
        // Add tags to posts
        if ($postId) {
            if (strpos($post['title'], 'Web Development') !== false) {
                $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, (SELECT id FROM blog_tags WHERE slug = 'web'))", [$postId]);
                $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, (SELECT id FROM blog_tags WHERE slug = 'javascript'))", [$postId]);
            } else {
                $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, (SELECT id FROM blog_tags WHERE slug = 'mobile'))", [$postId]);
                $db->query("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, (SELECT id FROM blog_tags WHERE slug = 'design'))", [$postId]);
            }
        }
    }
    
    // Insert current page content
    $currentPages = [
        [
            'page_slug' => 'about',
            'page_title' => 'About Us - BitSync Group',
            'page_description' => 'Learn about BitSync Group, a global technology powerhouse delivering cutting-edge solutions.',
            'content' => '<h1>About BitSync Group</h1><p>BitSync Group is a global technology powerhouse delivering cutting-edge solutions in consumer electronics, enterprise systems, and innovative consulting services.</p>',
            'meta_title' => 'About Us - BitSync Group',
            'meta_description' => 'Learn about BitSync Group, a global technology powerhouse delivering cutting-edge solutions.'
        ],
        [
            'page_slug' => 'services',
            'page_title' => 'Our Services - BitSync Group',
            'page_description' => 'Comprehensive technology services including web development, mobile apps, and consulting.',
            'content' => '<h1>Our Services</h1><p>We offer comprehensive technology solutions including web development, mobile applications, cloud services, and strategic consulting.</p>',
            'meta_title' => 'Our Services - BitSync Group',
            'meta_description' => 'Comprehensive technology services including web development, mobile apps, and consulting.'
        ],
        [
            'page_slug' => 'contact',
            'page_title' => 'Contact Us - BitSync Group',
            'page_description' => 'Get in touch with BitSync Group for your technology needs.',
            'content' => '<h1>Contact Us</h1><p>Ready to start your next project? Get in touch with our team today.</p>',
            'meta_title' => 'Contact Us - BitSync Group',
            'meta_description' => 'Get in touch with BitSync Group for your technology needs.'
        ]
    ];
    
    foreach ($currentPages as $page) {
        $db->query(
            "INSERT INTO page_content (page_slug, page_title, page_description, content, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?) ON CONFLICT (page_slug) DO UPDATE SET content = EXCLUDED.content, updated_at = CURRENT_TIMESTAMP",
            [$page['page_slug'], $page['page_title'], $page['page_description'], $page['content'], $page['meta_title'], $page['meta_description']]
        );
    }
    
    // Create indexes for better performance
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_posts_slug ON blog_posts(slug)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_posts_status ON blog_posts(status)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_posts_published_at ON blog_posts(published_at)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_posts_category_id ON blog_posts(category_id)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_categories_slug ON blog_categories(slug)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_blog_tags_slug ON blog_tags(slug)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_page_content_slug ON page_content(page_slug)");
    
    echo "✅ Blog system database migration completed successfully!\n";
    echo "📊 Created tables:\n";
    echo "   - blog_categories\n";
    echo "   - blog_posts\n";
    echo "   - blog_tags\n";
    echo "   - blog_post_tags\n";
    echo "   - page_content\n";
    echo "🔧 Added indexes for optimal performance\n";
    echo "📝 Inserted default categories and sample posts\n";
    echo "💾 Saved current page content to database\n";
    
} catch (Exception $e) {
    echo "❌ Error during blog migration: " . $e->getMessage() . "\n";
    exit(1);
}
?> 