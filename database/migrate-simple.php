<?php
/**
 * Simple Database Migration Script for BitSync Group
 * Handles CockroachDB schema creation step by step
 */

require_once __DIR__ . '/../includes/Database.php';

class SimpleMigration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function migrate() {
        try {
            echo "Starting simple database migration...\n";
            
            // Create tables one by one
            $this->createUsersTable();
            $this->createContentPagesTable();
            $this->createNewsletterSubscribersTable();
            $this->createContactSubmissionsTable();
            $this->createServicesTable();
            $this->createCaseStudiesTable();
            $this->createBlogPostsTable();
            $this->createJobOpeningsTable();
            $this->createJobApplicationsTable();
            $this->createSystemSettingsTable();
            
            // Insert initial data
            $this->insertInitialData();
            
            // Migrate existing JSON content
            $this->migrateExistingData();
            
            echo "Simple migration completed successfully!\n";
            
        } catch (Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    private function createUsersTable() {
        echo "Creating users table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            role VARCHAR(20) DEFAULT 'admin',
            is_active BOOLEAN DEFAULT true,
            last_login TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Users table created\n";
    }
    
    private function createContentPagesTable() {
        echo "Creating content_pages table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS content_pages (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            page_key VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            content JSONB,
            meta_title VARCHAR(255),
            meta_description TEXT,
            is_published BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Content pages table created\n";
    }
    
    private function createNewsletterSubscribersTable() {
        echo "Creating newsletter_subscribers table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            email VARCHAR(255) UNIQUE NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            is_active BOOLEAN DEFAULT true,
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            unsubscribed_at TIMESTAMP,
            source VARCHAR(100) DEFAULT 'website'
        )";
        $this->db->query($sql);
        echo "✅ Newsletter subscribers table created\n";
    }
    
    private function createContactSubmissionsTable() {
        echo "Creating contact_submissions table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS contact_submissions (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            company VARCHAR(100),
            subject VARCHAR(255),
            message TEXT NOT NULL,
            ip_address INET,
            user_agent TEXT,
            is_read BOOLEAN DEFAULT false,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Contact submissions table created\n";
    }
    
    private function createServicesTable() {
        echo "Creating services table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS services (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            slug VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            content JSONB,
            icon VARCHAR(100),
            image_url VARCHAR(255),
            is_featured BOOLEAN DEFAULT false,
            sort_order INTEGER DEFAULT 0,
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Services table created\n";
    }
    
    private function createCaseStudiesTable() {
        echo "Creating case_studies table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS case_studies (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            slug VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            content JSONB,
            client_name VARCHAR(255),
            industry VARCHAR(100),
            results JSONB,
            image_url VARCHAR(255),
            is_featured BOOLEAN DEFAULT false,
            is_published BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Case studies table created\n";
    }
    
    private function createBlogPostsTable() {
        echo "Creating blog_posts table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            slug VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            excerpt TEXT,
            content TEXT NOT NULL,
            author_id UUID,
            featured_image VARCHAR(255),
            tags TEXT[],
            is_published BOOLEAN DEFAULT false,
            published_at TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Blog posts table created\n";
    }
    
    private function createJobOpeningsTable() {
        echo "Creating job_openings table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS job_openings (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            requirements JSONB,
            benefits JSONB,
            location VARCHAR(100),
            type VARCHAR(50),
            department VARCHAR(100),
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Job openings table created\n";
    }
    
    private function createJobApplicationsTable() {
        echo "Creating job_applications table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS job_applications (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            job_id UUID,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            resume_url VARCHAR(255),
            cover_letter TEXT,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ Job applications table created\n";
    }
    
    private function createSystemSettingsTable() {
        echo "Creating system_settings table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS system_settings (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'string',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
        echo "✅ System settings table created\n";
    }
    
    private function insertInitialData() {
        echo "Inserting initial data...\n";
        
        // Insert admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?) ON CONFLICT (username) DO NOTHING",
            ['admin', 'admin@bitsync.com', $adminPassword, 'System Administrator', 'admin']
        );
        echo "✅ Admin user created\n";
        
        // Insert system settings
        $settings = [
            ['site_name', 'BitSync Group', 'string', 'Website name'],
            ['site_description', 'Professional technology solutions and digital transformation services', 'string', 'Website description'],
            ['contact_email', 'info@bitsync.com', 'string', 'Primary contact email'],
            ['contact_phone', '+234 (803) 381-8401', 'string', 'Primary contact phone'],
            ['contact_address', 'Lagos, Nigeria', 'string', 'Company address'],
            ['social_facebook', '', 'string', 'Facebook URL'],
            ['social_twitter', '', 'string', 'Twitter URL'],
            ['social_linkedin', '', 'string', 'LinkedIn URL'],
            ['newsletter_enabled', 'true', 'boolean', 'Enable newsletter subscription'],
            ['maintenance_mode', 'false', 'boolean', 'Enable maintenance mode']
        ];
        
        foreach ($settings as $setting) {
            $this->db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?) ON CONFLICT (setting_key) DO NOTHING",
                $setting
            );
        }
        echo "✅ System settings created\n";
    }
    
    private function migrateExistingData() {
        echo "Migrating existing JSON data...\n";
        
        $contentDir = __DIR__ . '/../content';
        $jsonFiles = glob($contentDir . '/*.json');
        
        foreach ($jsonFiles as $file) {
            $pageKey = basename($file, '.json');
            $content = json_decode(file_get_contents($file), true);
            
            if ($content) {
                $this->migratePageContent($pageKey, $content);
            }
        }
    }
    
    private function migratePageContent($pageKey, $content) {
        // Check if page already exists
        $existing = $this->db->fetchOne(
            "SELECT id FROM content_pages WHERE page_key = ?",
            [$pageKey]
        );
        
        if (!$existing) {
            $title = $this->getPageTitle($pageKey);
            $description = $this->getPageDescription($pageKey);
            
            $this->db->insert('content_pages', [
                'page_key' => $pageKey,
                'title' => $title,
                'description' => $description,
                'content' => json_encode($content),
                'meta_title' => $title,
                'meta_description' => $description
            ]);
            
            echo "✅ Migrated page: {$pageKey}\n";
        }
    }
    
    private function getPageTitle($pageKey) {
        $titles = [
            'home' => 'BitSync Group - Technology Solutions',
            'about' => 'About Us - BitSync Group',
            'services' => 'Our Services - BitSync Group',
            'contact' => 'Contact Us - BitSync Group'
        ];
        
        return $titles[$pageKey] ?? ucfirst($pageKey) . ' - BitSync Group';
    }
    
    private function getPageDescription($pageKey) {
        $descriptions = [
            'home' => 'Professional technology solutions and digital transformation services',
            'about' => 'Learn about BitSync Group and our mission to transform businesses',
            'services' => 'Explore our comprehensive range of technology services',
            'contact' => 'Get in touch with BitSync Group for your technology needs'
        ];
        
        return $descriptions[$pageKey] ?? 'BitSync Group - ' . ucfirst($pageKey);
    }
}

// Run migration if called directly
if (php_sapi_name() === 'cli') {
    $migration = new SimpleMigration();
    $migration->migrate();
} 