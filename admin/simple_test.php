<?php
echo "<h1>Admin Panel Test</h1>";
echo "<p>✅ Admin panel is working!</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";

echo "<h2>Session Test:</h2>";
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    echo "<p>✅ Session is active</p>";
    echo "<p>Username: " . ($_SESSION['admin_username'] ?? 'Not set') . "</p>";
} else {
    echo "<p>❌ No active session</p>";
}

echo "<h2>File Access Test:</h2>";
echo "<p>Dashboard.php exists: " . (file_exists('dashboard.php') ? '✅ Yes' : '❌ No') . "</p>";
echo "<p>Config.php exists: " . (file_exists('config.php') ? '✅ Yes' : '❌ No') . "</p>";

echo "<h2>Links:</h2>";
echo "<a href='index.php'>Login Page</a><br>";
echo "<a href='dashboard.php'>Dashboard</a><br>";
echo "<a href='debug.php'>Debug Info</a><br>";

echo "<h2>Test Complete!</h2>";
?> 