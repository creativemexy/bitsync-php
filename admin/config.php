<?php
// Admin Configuration
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'bitsync2024'); // Change this to a secure password
define('CONTENT_DIR', '../content/');
define('BACKUP_DIR', '../backups/');

// Create directories if they don't exist
if (!file_exists(CONTENT_DIR)) {
    mkdir(CONTENT_DIR, 0755, true);
}
if (!file_exists(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true);
}

// Content structure
$content_structure = [
    'home' => [
        'hero_title' => 'Transform Your Business',
        'hero_subtitle' => 'With cutting-edge technology solutions',
        'hero_description' => 'We deliver innovative digital solutions that drive growth and accelerate your business transformation.',
        'stats' => [
            'clients' => '500+',
            'countries' => '50+',
            'projects' => '1000+',
            'support' => '24/7'
        ]
    ],
    'about' => [
        'mission' => 'To empower businesses through innovative technology solutions',
        'vision' => 'To be the leading technology partner for digital transformation',
        'values' => [
            'Innovation' => 'Pushing boundaries with cutting-edge solutions',
            'Excellence' => 'Delivering exceptional quality in everything we do',
            'Integrity' => 'Building trust through transparent partnerships',
            'Collaboration' => 'Working together to achieve shared success'
        ]
    ],
    'services' => [
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
    ],
    'contact' => [
        'email' => 'info@bitsyncgroup.com',
        'phone' => '+234 (803) 381-8401',
        'address' => '22 Airport road, Mafoluku Lagos',
        'hours' => 'Monday - Friday: 9:00 AM - 6:00 PM'
    ]
];

// Helper functions
function saveContent($page, $data) {
    $file = CONTENT_DIR . $page . '.json';
    $backup = BACKUP_DIR . $page . '_' . date('Y-m-d_H-i-s') . '.json';
    
    // Create backup
    if (file_exists($file)) {
        copy($file, $backup);
    }
    
    // Save new content
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function loadContent($page) {
    $file = CONTENT_DIR . $page . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

function getAllContent() {
    $content = [];
    $files = glob(CONTENT_DIR . '*.json');
    foreach ($files as $file) {
        $page = basename($file, '.json');
        $content[$page] = json_decode(file_get_contents($file), true);
    }
    return $content;
}
?> 