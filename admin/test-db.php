<?php
/**
 * Database Connection Test
 * Diagnose database connection issues on live server
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        echo "<p style='color: red;'>❌ .env file not found</p>";
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
    echo "<p style='color: green;'>✅ .env file loaded</p>";
    return true;
}

loadEnv(__DIR__ . '/../.env');

// Display environment variables (masked for security)
echo "<h2>Environment Variables</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";
echo "<tr><td>DB_HOST</td><td>" . ($_ENV['DB_HOST'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DB_PORT</td><td>" . ($_ENV['DB_PORT'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DB_NAME</td><td>" . ($_ENV['DB_NAME'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DB_USER</td><td>" . ($_ENV['DB_USER'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DB_PASSWORD</td><td>" . (empty($_ENV['DB_PASSWORD']) ? 'Empty' : 'Set') . "</td></tr>";
echo "</table>";

// Test basic PHP extensions
echo "<h2>PHP Extensions</h2>";
$required_extensions = ['pdo', 'pdo_pgsql', 'openssl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ $ext extension loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ $ext extension not loaded</p>";
    }
}

// Test network connectivity
echo "<h2>Network Connectivity</h2>";
$host = $_ENV['DB_HOST'] ?? 'tangy-spirit-7966.jxf.cockroachlabs.cloud';
$port = $_ENV['DB_PORT'] ?? '26257';

$connection = @fsockopen($host, $port, $errno, $errstr, 10);
if ($connection) {
    echo "<p style='color: green;'>✅ Network connection to $host:$port successful</p>";
    fclose($connection);
} else {
    echo "<p style='color: red;'>❌ Network connection to $host:$port failed: $errstr ($errno)</p>";
}

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    require_once __DIR__ . '/../includes/Database.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Test a simple query
    try {
        $result = $db->fetchOne("SELECT 1 as test");
        echo "<p style='color: green;'>✅ Database query successful</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test users table
    try {
        $users = $db->fetchAll("SELECT COUNT(*) as count FROM users");
        echo "<p style='color: green;'>✅ Users table accessible: " . $users[0]['count'] . " users found</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Users table error: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    
    // Try direct PDO connection
    echo "<h3>Direct PDO Connection Test</h3>";
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=" . ($_ENV['DB_NAME'] ?? 'ken');
        $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'demilade', $_ENV['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        echo "<p style='color: green;'>✅ Direct PDO connection successful</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Direct PDO connection failed: " . $e->getMessage() . "</p>";
    }
}

// Test SSL connection
echo "<h2>SSL Connection Test</h2>";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=" . ($_ENV['DB_NAME'] ?? 'ken') . ";sslmode=require";
    $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'demilade', $_ENV['DB_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "<p style='color: green;'>✅ SSL connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ SSL connection failed: " . $e->getMessage() . "</p>";
}

// Server information
echo "<h2>Server Information</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Item</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Server Software</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>Current Directory</td><td>" . getcwd() . "</td></tr>";
echo "</table>";

// File permissions
echo "<h2>File Permissions</h2>";
$files_to_check = [
    '../.env' => 'Environment file',
    '../config/database.php' => 'Database config',
    '../includes/Database.php' => 'Database class'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $readable = is_readable($file) ? 'Yes' : 'No';
        echo "<p style='color: green;'>✅ $description: Readable ($readable), Permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ $description: File not found</p>";
    }
}

echo "<h2>Recommendations</h2>";
echo "<ul>";
echo "<li>Check if your hosting provider allows outbound connections to port 26257</li>";
echo "<li>Verify that your .env file has the correct database credentials</li>";
echo "<li>Ensure your hosting provider supports PDO and PostgreSQL extensions</li>";
echo "<li>Check if SSL certificates are required for your database connection</li>";
echo "<li>Contact your hosting provider if network connectivity fails</li>";
echo "</ul>";

echo "<p><strong>Note:</strong> Delete this file after testing for security reasons.</p>";
?> 