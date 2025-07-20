<?php
// Content loader for dynamic content management
function getContent($page, $key = null) {
    $content_file = __DIR__ . '/../content/' . $page . '.json';
    
    if (file_exists($content_file)) {
        $content = json_decode(file_get_contents($content_file), true);
        
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
    
    return null;
}

// Helper function to get content with fallback
function getContentWithFallback($page, $key, $fallback = '') {
    $content = getContent($page, $key);
    return $content !== null ? $content : $fallback;
}

// Get current page content
$current_page = $_GET['page'] ?? 'home';
$page_content = getContent($current_page);

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