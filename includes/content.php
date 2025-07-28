<?php
// Load environment variables
if (!function_exists('loadEnv')) {
    function loadEnv($file) {
        if (!file_exists($file)) {
            return false;
        }
        
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, '"\'');
            
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
        return true;
    }
}

// Load environment file
loadEnv(__DIR__ . '/../.env');

// Content loader for dynamic content management using database
function getContent($page, $key = null) {
    static $contentCache = [];
    
    // Check cache first
    if (isset($contentCache[$page])) {
        $content = $contentCache[$page];
    } else {
        // Try to load from database first
        $content = getContentFromDatabase($page);
        
        // Fallback to JSON file if database fails
        if ($content === null) {
            $content = getContentFromJson($page);
        }
        
        // Cache the result
        $contentCache[$page] = $content;
    }
    
    if ($content === null) {
        return null;
    }
    
    if ($key === null) {
        return $content;
    }
    
    // Handle nested keys like 'stats.clients'
    $keys = explode('.', $key);
    $value = $content;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return null;
        }
    }
    
    return $value;
}

// Get content from database
function getContentFromDatabase($page) {
    try {
        require_once __DIR__ . '/ContentManager.php';
        $contentManager = new ContentManager();
        return $contentManager->getPageContent($page);
    } catch (Exception $e) {
        // Log error but don't show to user
        error_log("Database content load failed for page '$page': " . $e->getMessage());
        return null;
    }
}

// Fallback: Get content from JSON file
function getContentFromJson($page) {
    $content_file = __DIR__ . '/../backups/content/' . $page . '.json';
    
    if (file_exists($content_file)) {
        $content = json_decode(file_get_contents($content_file), true);
        return $content;
    }
    
    return null;
}

// Get system settings from database
function getSetting($key, $default = null) {
    static $settingsCache = null;
    
    if ($settingsCache === null) {
        try {
            require_once __DIR__ . '/ContentManager.php';
            $contentManager = new ContentManager();
            $settingsCache = $contentManager->getAllSettings();
        } catch (Exception $e) {
            error_log("Settings load failed: " . $e->getMessage());
            $settingsCache = [];
        }
    }
    
    return $settingsCache[$key] ?? $default;
}

// Get page metadata from database
function getPageMetadata($page) {
    try {
        require_once __DIR__ . '/Database.php';
        $db = Database::getInstance();
        
        $result = $db->fetchOne(
            "SELECT title, description, meta_title, meta_description FROM content_pages WHERE page_key = ? AND is_published = true",
            [$page]
        );
        
        return $result;
    } catch (Exception $e) {
        error_log("Page metadata load failed for '$page': " . $e->getMessage());
        return null;
    }
}

// Initialize page variables
$current_page = $_GET['page'] ?? 'home';

// Get page metadata
$pageMetadata = getPageMetadata($current_page);
if ($pageMetadata) {
    $page_title = $pageMetadata['meta_title'] ?: $pageMetadata['title'];
    $page_description = $pageMetadata['meta_description'] ?: $pageMetadata['description'];
} else {
    // Fallback metadata
    $page_title = ucfirst($current_page) . ' - BitSync Group';
    $page_description = 'BitSync Group - Professional technology solutions and digital transformation services.';
}

// Common content functions
function getHeroTitle() {
    global $current_page;
    return getContentWithFallback($current_page, 'hero_title', 'Transform Your Business');
}

function getHeroSubtitle() {
    global $current_page;
    return getContentWithFallback($current_page, 'hero_subtitle', 'With cutting-edge technology solutions');
}

function getHeroDescription() {
    global $current_page;
    return getContentWithFallback($current_page, 'hero_description', 'We deliver innovative digital solutions that drive growth and accelerate your business transformation.');
}

function getStats($stat) {
    return getContentWithFallback('home', 'stats.' . $stat, '500+');
}

function getMission() {
    return getContentWithFallback('about', 'mission', 'To empower businesses through innovative technology solutions');
}

function getVision() {
    return getContentWithFallback('about', 'vision', 'To be the leading technology partner for digital transformation');
}

function getValues() {
    $values = getContent('about', 'values');
    if (!$values) {
        return [
            'Innovation' => 'Pushing boundaries with cutting-edge solutions',
            'Excellence' => 'Delivering exceptional quality in everything we do',
            'Integrity' => 'Building trust through transparent partnerships',
            'Collaboration' => 'Working together to achieve shared success'
        ];
    }
    return $values;
}

function getServices() {
    $services = getContent('services', 'services');
    if (!$services) {
        return [
            'web_development' => [
                'title' => 'Web Development',
                'description' => 'Custom web applications and digital experiences',
                'features' => ['React', 'Vue.js', 'Node.js', 'PHP', 'Laravel']
            ],
            'mobile_development' => [
                'title' => 'Mobile Development',
                'description' => 'Native and cross-platform mobile applications',
                'features' => ['iOS', 'Android', 'React Native', 'Flutter']
            ],
            'cloud_solutions' => [
                'title' => 'Cloud Solutions',
                'description' => 'Scalable cloud infrastructure and services',
                'features' => ['AWS', 'Azure', 'Google Cloud', 'DevOps']
            ],
            'blockchain' => [
                'title' => 'Blockchain Technology',
                'description' => 'Decentralized solutions and smart contracts',
                'features' => ['Ethereum', 'Hyperledger', 'Smart Contracts', 'DeFi']
            ]
        ];
    }
    return $services;
}

function getContactInfo($field) {
    return getContentWithFallback('contact', $field, 'Contact information not available');
}
?> 