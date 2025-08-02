<?php
/**
 * Live Server Database Diagnostic
 * Helps diagnose database connection issues on live servers
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Live Server Database Diagnostic</h1>";

// Test 1: Check if .env file exists
echo "<h2>1. Environment File Check</h2>";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "✅ .env file exists<br>";
    echo "📁 Path: " . realpath($envFile) . "<br>";
} else {
    echo "❌ .env file not found<br>";
    echo "📁 Expected path: " . realpath(__DIR__ . '/../') . '/.env<br>';
}

// Test 2: Check if config files exist
echo "<h2>2. Configuration Files Check</h2>";
$configFile = __DIR__ . '/../config/database.php';
if (file_exists($configFile)) {
    echo "✅ Database config file exists<br>";
} else {
    echo "❌ Database config file not found<br>";
}

$databaseClass = __DIR__ . '/../includes/Database.php';
if (file_exists($databaseClass)) {
    echo "✅ Database class file exists<br>";
} else {
    echo "❌ Database class file not found<br>";
}

// Test 3: Check PHP extensions
echo "<h2>3. PHP Extensions Check</h2>";
$requiredExtensions = ['pdo', 'pdo_pgsql', 'openssl'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext extension loaded<br>";
    } else {
        echo "❌ $ext extension not loaded<br>";
    }
}

// Test 4: Try to load environment variables
echo "<h2>4. Environment Variables Check</h2>";
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

if (loadEnv(__DIR__ . '/../.env')) {
    echo "✅ Environment variables loaded<br>";
    echo "📋 DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "<br>";
    echo "📋 DB_PORT: " . ($_ENV['DB_PORT'] ?? 'Not set') . "<br>";
    echo "📋 DB_NAME: " . ($_ENV['DB_NAME'] ?? 'Not set') . "<br>";
    echo "📋 DB_USER: " . ($_ENV['DB_USER'] ?? 'Not set') . "<br>";
    echo "📋 DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? 'Set' : 'Not set') . "<br>";
} else {
    echo "❌ Failed to load environment variables<br>";
}

// Test 5: Try database connection
echo "<h2>5. Database Connection Test</h2>";
try {
    require_once __DIR__ . '/../includes/Database.php';
    echo "✅ Database class loaded successfully<br>";
    
    $db = Database::getInstance();
    echo "✅ Database instance created<br>";
    
    // Test a simple query
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "✅ Database query successful<br>";
    echo "📊 Users in database: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database connection failed<br>";
    echo "🔍 Error: " . $e->getMessage() . "<br>";
    echo "📋 Error code: " . $e->getCode() . "<br>";
}

// Test 6: Check server information
echo "<h2>6. Server Information</h2>";
echo "🌐 PHP Version: " . phpversion() . "<br>";
echo "🖥️ Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "📁 Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "📂 Current Directory: " . getcwd() . "<br>";

// Test 7: Check file permissions
echo "<h2>7. File Permissions Check</h2>";
$filesToCheck = [
    __DIR__ . '/../.env',
    __DIR__ . '/../config/database.php',
    __DIR__ . '/../includes/Database.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $perms = substr(sprintf('%o', $perms), -4);
        echo "📄 " . basename($file) . ": " . $perms . "<br>";
    } else {
        echo "❌ " . basename($file) . ": File not found<br>";
    }
}

// Test 8: Network connectivity test
echo "<h2>8. Network Connectivity Test</h2>";
if (isset($_ENV['DB_HOST']) && isset($_ENV['DB_PORT'])) {
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ Network connection to $host:$port successful<br>";
        fclose($connection);
    } else {
        echo "❌ Network connection to $host:$port failed<br>";
        echo "🔍 Error: $errstr ($errno)<br>";
    }
} else {
    echo "⚠️ Cannot test network connectivity - missing host/port info<br>";
}

echo "<h2>🔧 Recommendations</h2>";
echo "<ul>";
echo "<li>If .env file is missing, create it with your database credentials</li>";
echo "<li>If database config is missing, check your file upload</li>";
echo "<li>If network connection fails, contact your hosting provider</li>";
echo "<li>If PHP extensions are missing, contact your hosting provider</li>";
echo "<li>Use fallback admin credentials (admin/admin123) for emergency access</li>";
echo "</ul>";

echo "<h2>🚨 Emergency Access</h2>";
echo "<p>If database connection fails, you can still access the admin panel using:</p>";
echo "<ul>";
echo "<li><strong>Username:</strong> admin</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";
echo "<p>This will use fallback authentication and allow you to access the admin dashboard.</p>";
?> 