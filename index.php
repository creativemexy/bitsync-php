<?php
session_start();

// Load environment variables
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

loadEnv(__DIR__ . '/.env');

// Initialize monitoring
try {
    require_once 'includes/Monitoring.php';
    $monitoring = new Monitoring();
    $monitoring->startRequest();
} catch (Exception $e) {
    // Log error but don't break the site
    error_log("Monitoring initialization failed: " . $e->getMessage());
}

// Simple routing system
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Remove .php extension if present
$path = str_replace('.php', '', $path);

// Define routes
$routes = [
    '' => 'pages/index.php',
    'about' => 'pages/about.php',
    'services' => 'pages/services.php',
    'solutions' => 'pages/solutions.php',
    'contact' => 'pages/contact.php',
    'search' => 'pages/search.php',
    'web-development' => 'pages/web-development.php',
    'mobile-development' => 'pages/mobile-development.php',
    'cloud-solutions' => 'pages/cloud-solutions.php',
    'blockchain-technology' => 'pages/blockchain-technology.php',
    'ai-machine-learning' => 'pages/ai-machine-learning.php',
    'digital-transformation' => 'pages/digital-transformation.php',
    'careers' => 'pages/careers.php',
    'blog' => 'pages/blog.php',
    'case-studies' => 'pages/case-studies.php',
    'healthcare-solutions' => 'pages/healthcare-solutions.php',
    'financial-services' => 'pages/financial-services.php',
    'manufacturing-solutions' => 'pages/manufacturing-solutions.php',
    'retail-ecommerce' => 'pages/retail-ecommerce.php',
    'analytics-dashboard' => 'pages/analytics-dashboard.php',
    'offline' => 'pages/offline.php',
    'pwa-install' => 'pages/pwa-install.php'
];

// Check if route exists
if (array_key_exists($path, $routes)) {
    $page_file = $routes[$path];
    $current_page = $path ?: 'home';
} else {
    $page_file = 'pages/404.php';
    $current_page = '404';
}

// Track page view
if (isset($monitoring)) {
    try {
        $monitoring->trackPageView($current_page);
    } catch (Exception $e) {
        error_log("Failed to track page view: " . $e->getMessage());
    }
}

// Include the layout
include 'includes/layout.php';

// End monitoring
if (isset($monitoring)) {
    try {
        $monitoring->endRequest();
    } catch (Exception $e) {
        error_log("Failed to end monitoring: " . $e->getMessage());
    }
}
?> 