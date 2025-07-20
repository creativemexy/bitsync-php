<?php
echo "<h1>Redirect Path Test</h1>";

echo "<h2>Current Directory Structure:</h2>";
echo "Current working directory: " . getcwd() . "<br>";
echo "Script location: " . __FILE__ . "<br>";

echo "<h2>URL Paths:</h2>";
echo "Relative to admin: ./dashboard.php<br>";
echo "Absolute path: /bitsync/admin/dashboard.php<br>";

echo "<h2>File Existence Check:</h2>";
echo "dashboard.php exists: " . (file_exists('dashboard.php') ? 'Yes' : 'No') . "<br>";
echo "index.php exists: " . (file_exists('index.php') ? 'Yes' : 'No') . "<br>";

echo "<h2>Test Links:</h2>";
echo "<a href='./dashboard.php'>Test ./dashboard.php</a><br>";
echo "<a href='/bitsync/admin/dashboard.php'>Test /bitsync/admin/dashboard.php</a><br>";
echo "<a href='index.php'>Test index.php</a><br>";

echo "<h2>Server Info:</h2>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";

echo "<h2>Test Complete!</h2>";
?> 