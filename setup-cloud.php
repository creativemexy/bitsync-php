<?php
/**
 * BitSync Group Cloud Database Setup Script
 * Sets up CockroachDB Cloud instance
 */

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

// Load environment file
loadEnv(__DIR__ . '/.env');

echo "🚀 BitSync Group Cloud Database Setup\n";
echo "=====================================\n\n";

// Check if .env exists
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ .env file not found. Please copy env.example to .env and update with your credentials.\n";
    echo "cp env.example .env\n";
    echo "Then edit .env with your CockroachDB cloud password.\n";
    exit(1);
}

// Check required extensions
echo "1. Checking PHP extensions...\n";
$requiredExtensions = ['pdo', 'pdo_pgsql', 'json'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo "❌ Missing PHP extensions: " . implode(', ', $missingExtensions) . "\n";
    echo "Please install the missing extensions and try again.\n";
    exit(1);
}
echo "✅ All required extensions are available\n\n";

// Test database connection
echo "2. Testing database connection...\n";
try {
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance();
    $db->getConnection();
    echo "✅ Database connection successful\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your .env file and ensure your CockroachDB credentials are correct.\n";
    exit(1);
}

// Run migrations
echo "3. Running database migrations...\n";
try {
    require_once __DIR__ . '/database/migrate-simple.php';
    $migration = new SimpleMigration();
    $migration->migrate();
    echo "✅ Migrations completed successfully\n\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create admin user
echo "4. Setting up admin user...\n";
$adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';
$passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);

try {
    $db->query(
        "INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?) ON CONFLICT (username) DO UPDATE SET password_hash = ?",
        [
            $_ENV['ADMIN_USERNAME'] ?? 'admin',
            $_ENV['ADMIN_EMAIL'] ?? 'admin@bitsync.com',
            $passwordHash,
            'System Administrator',
            'admin',
            $passwordHash
        ]
    );
    echo "✅ Admin user created/updated\n\n";
} catch (Exception $e) {
    echo "⚠️  Admin user setup: " . $e->getMessage() . "\n\n";
}

// Update system settings
echo "5. Updating system settings...\n";
require_once __DIR__ . '/includes/ContentManager.php';
$contentManager = new ContentManager();

$settings = [
    'site_name' => $_ENV['APP_NAME'] ?? 'BitSync Group',
    'contact_email' => $_ENV['CONTACT_EMAIL'] ?? 'info@bitsync.com',
    'contact_phone' => $_ENV['CONTACT_PHONE'] ?? '+234 (803) 381-8401',
    'contact_address' => $_ENV['CONTACT_ADDRESS'] ?? 'Lagos, Nigeria',
    'social_facebook' => $_ENV['SOCIAL_FACEBOOK'] ?? '',
    'social_twitter' => $_ENV['SOCIAL_TWITTER'] ?? '',
    'social_linkedin' => $_ENV['SOCIAL_LINKEDIN'] ?? '',
    'newsletter_enabled' => $_ENV['NEWSLETTER_ENABLED'] ?? 'true'
];

foreach ($settings as $key => $value) {
    try {
        $contentManager->saveSetting($key, $value);
    } catch (Exception $e) {
        echo "⚠️  Setting $key: " . $e->getMessage() . "\n";
    }
}
echo "✅ System settings updated\n\n";

// Show success information
echo "🎉 Setup completed successfully!\n";
echo "==============================\n";
echo "Database Host: " . ($_ENV['DB_HOST'] ?? 'tangy-spirit-7966.jxf.cockroachlabs.cloud') . "\n";
echo "Database Name: " . ($_ENV['DB_NAME'] ?? 'ken') . "\n";
echo "Database User: " . ($_ENV['DB_USER'] ?? 'demilade') . "\n";
echo "Admin Username: " . ($_ENV['ADMIN_USERNAME'] ?? 'admin') . "\n";
echo "Admin Password: " . ($_ENV['ADMIN_PASSWORD'] ?? 'admin123') . "\n\n";

echo "📝 Next steps:\n";
echo "1. Your website is now connected to CockroachDB Cloud\n";
echo "2. Access your website to test the functionality\n";
echo "3. Use the admin panel to manage content\n";
echo "4. Monitor your database usage in CockroachDB Cloud console\n\n";

echo "🔧 Useful commands:\n";
echo "Test connection: php -r \"require 'includes/Database.php'; \$db = Database::getInstance(); echo 'Connected!';\"\n";
echo "Backup data: php database/backup.php\n";
echo "View logs: Check your web server error logs\n\n";

echo "🌐 CockroachDB Cloud Console:\n";
echo "Visit https://cockroachlabs.cloud to monitor your database usage and performance.\n"; 