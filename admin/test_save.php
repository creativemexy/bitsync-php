<?php
require_once 'config.php';

echo "<h1>Testing Save Functionality</h1>";

// Test 1: Save home content
echo "<h2>Test 1: Saving Home Content</h2>";
$test_home_content = [
    'hero_title' => 'Test Hero Title',
    'hero_subtitle' => 'Test Hero Subtitle',
    'hero_description' => 'This is a test description to verify save functionality.',
    'stats' => [
        'clients' => '999+',
        'countries' => '99+',
        'projects' => '1999+',
        'support' => '24/7'
    ]
];

if (saveContent('home', $test_home_content)) {
    echo "✅ Home content saved successfully!<br>";
} else {
    echo "❌ Failed to save home content<br>";
}

// Test 2: Load and verify
echo "<h2>Test 2: Loading Saved Content</h2>";
$loaded_content = loadContent('home');
if ($loaded_content) {
    echo "✅ Content loaded successfully!<br>";
    echo "Hero Title: " . ($loaded_content['hero_title'] ?? 'Not found') . "<br>";
    echo "Clients: " . ($loaded_content['stats']['clients'] ?? 'Not found') . "<br>";
} else {
    echo "❌ Failed to load content<br>";
}

// Test 3: Check backup creation
echo "<h2>Test 3: Checking Backup Creation</h2>";
$backup_files = glob(BACKUP_DIR . 'home_*.json');
if ($backup_files) {
    echo "✅ Backup created: " . basename(end($backup_files)) . "<br>";
} else {
    echo "❌ No backup found<br>";
}

// Test 4: Test about page
echo "<h2>Test 4: Testing About Page</h2>";
$test_about_content = [
    'mission' => 'Test mission statement',
    'vision' => 'Test vision statement',
    'values' => [
        'Innovation' => 'Test innovation value',
        'Excellence' => 'Test excellence value',
        'Integrity' => 'Test integrity value',
        'Collaboration' => 'Test collaboration value'
    ]
];

if (saveContent('about', $test_about_content)) {
    echo "✅ About content saved successfully!<br>";
} else {
    echo "❌ Failed to save about content<br>";
}

// Test 5: List all content files
echo "<h2>Test 5: Current Content Files</h2>";
$content_files = glob(CONTENT_DIR . '*.json');
if ($content_files) {
    foreach ($content_files as $file) {
        echo "📄 " . basename($file) . " - " . filesize($file) . " bytes<br>";
    }
} else {
    echo "❌ No content files found<br>";
}

// Test 6: Test getAllContent function
echo "<h2>Test 6: Testing getAllContent Function</h2>";
$all_content = getAllContent();
echo "Found " . count($all_content) . " content pages:<br>";
foreach ($all_content as $page => $content) {
    echo "- " . $page . " (" . (is_array($content) ? count($content) : '1') . " items)<br>";
}

echo "<h2>Test Complete!</h2>";
echo "<a href='dashboard.php'>Go to Dashboard</a><br>";
echo "<a href='debug.php'>Go to Debug</a><br>";
?> 