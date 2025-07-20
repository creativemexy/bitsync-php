<?php
session_start();
require_once 'config.php';

echo "<h1>BitSync Admin Debug</h1>";

echo "<h2>1. Session Status</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Admin Logged In: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No') . "<br>";
echo "Admin Username: " . ($_SESSION['admin_username'] ?? 'Not set') . "<br>";

echo "<h2>2. Configuration</h2>";
echo "Admin Username: " . ADMIN_USERNAME . "<br>";
echo "Content Directory: " . CONTENT_DIR . "<br>";
echo "Backup Directory: " . BACKUP_DIR . "<br>";

echo "<h2>3. Directory Status</h2>";
echo "Content Directory Exists: " . (file_exists(CONTENT_DIR) ? 'Yes' : 'No') . "<br>";
echo "Backup Directory Exists: " . (file_exists(BACKUP_DIR) ? 'Yes' : 'No') . "<br>";
echo "Content Directory Writable: " . (is_writable(CONTENT_DIR) ? 'Yes' : 'No') . "<br>";
echo "Backup Directory Writable: " . (is_writable(BACKUP_DIR) ? 'Yes' : 'No') . "<br>";

echo "<h2>4. Content Files</h2>";
$content_files = glob(CONTENT_DIR . '*.json');
if ($content_files) {
    foreach ($content_files as $file) {
        echo "File: " . basename($file) . " - Size: " . filesize($file) . " bytes<br>";
    }
} else {
    echo "No content files found<br>";
}

echo "<h2>5. Function Tests</h2>";
echo "getAllContent() Test: ";
try {
    $content = getAllContent();
    echo "Success - Found " . count($content) . " content items<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "loadContent('home') Test: ";
try {
    $home_content = loadContent('home');
    echo "Success - " . ($home_content ? 'Content loaded' : 'No content found') . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h2>6. PHP Info</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";
echo "Session Save Path Writable: " . (is_writable(session_save_path()) ? 'Yes' : 'No') . "<br>";

echo "<h2>7. Quick Actions</h2>";
echo "<a href='index.php'>Go to Login</a><br>";
echo "<a href='dashboard.php'>Go to Dashboard</a><br>";
echo "<a href='test.php'>Run Test File</a><br>";

echo "<h2>8. Create Test Content</h2>";
if (isset($_GET['create_test'])) {
    $test_content = [
        'hero_title' => 'Test Title',
        'hero_subtitle' => 'Test Subtitle',
        'hero_description' => 'Test Description',
        'stats' => [
            'clients' => '100+',
            'countries' => '10+',
            'projects' => '200+',
            'support' => '24/7'
        ]
    ];
    
    if (saveContent('home', $test_content)) {
        echo "✅ Test content created successfully!<br>";
    } else {
        echo "❌ Failed to create test content<br>";
    }
} else {
    echo "<a href='?create_test=1'>Create Test Content</a><br>";
}
?> 