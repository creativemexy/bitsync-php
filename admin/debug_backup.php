<?php
require_once 'config.php';

echo "<h1>Backup Debug Test</h1>";

echo "<h2>1. Directory Check</h2>";
echo "Content Directory: " . CONTENT_DIR . " - Exists: " . (file_exists(CONTENT_DIR) ? 'Yes' : 'No') . "<br>";
echo "Backup Directory: " . BACKUP_DIR . " - Exists: " . (file_exists(BACKUP_DIR) ? 'Yes' : 'No') . "<br>";
echo "Content Directory Writable: " . (is_writable(CONTENT_DIR) ? 'Yes' : 'No') . "<br>";
echo "Backup Directory Writable: " . (is_writable(BACKUP_DIR) ? 'Yes' : 'No') . "<br>";

echo "<h2>2. Manual Backup Test</h2>";
$test_file = CONTENT_DIR . 'test.json';
$backup_file = BACKUP_DIR . 'test_' . date('Y-m-d_H-i-s') . '.json';

// Create a test file
$test_data = ['test' => 'data'];
file_put_contents($test_file, json_encode($test_data));

echo "Test file created: " . (file_exists($test_file) ? 'Yes' : 'No') . "<br>";

// Try to backup
if (file_exists($test_file)) {
    $backup_result = copy($test_file, $backup_file);
    echo "Backup copy result: " . ($backup_result ? 'Success' : 'Failed') . "<br>";
    echo "Backup file exists: " . (file_exists($backup_file) ? 'Yes' : 'No') . "<br>";
}

echo "<h2>3. Test saveContent Function</h2>";
$test_content = [
    'title' => 'Test Title',
    'description' => 'Test Description'
];

$result = saveContent('test_page', $test_content);
echo "saveContent result: " . ($result ? 'Success' : 'Failed') . "<br>";

echo "<h2>4. Check for Backup Files</h2>";
$backup_files = glob(BACKUP_DIR . '*.json');
if ($backup_files) {
    foreach ($backup_files as $file) {
        echo "Backup: " . basename($file) . " - " . filesize($file) . " bytes<br>";
    }
} else {
    echo "No backup files found<br>";
}

echo "<h2>5. Cleanup</h2>";
// Clean up test files
if (file_exists($test_file)) unlink($test_file);
if (file_exists($backup_file)) unlink($backup_file);
if (file_exists(CONTENT_DIR . 'test_page.json')) unlink(CONTENT_DIR . 'test_page.json');

echo "Test files cleaned up<br>";

echo "<h2>Debug Complete!</h2>";
?> 