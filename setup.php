<?php
/**
 * BitSync Group Setup Script
 * Initializes CockroachDB and runs migrations
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

echo "🚀 BitSync Group Setup Script\n";
echo "==============================\n\n";

// Check if Docker is running
echo "1. Checking Docker...\n";
$dockerRunning = shell_exec('docker info > /dev/null 2>&1; echo $?');
if (trim($dockerRunning) !== '0') {
    echo "❌ Docker is not running. Please start Docker and try again.\n";
    exit(1);
}
echo "✅ Docker is running\n\n";

// Start CockroachDB
echo "2. Starting CockroachDB...\n";
$startCommand = 'docker-compose up -d cockroachdb';
$result = shell_exec($startCommand . ' 2>&1');
echo $result;

// Wait for CockroachDB to be ready
echo "3. Waiting for CockroachDB to be ready...\n";
$maxAttempts = 30;
$attempt = 0;

while ($attempt < $maxAttempts) {
    $checkCommand = 'docker exec bitsync-cockroach cockroach node status --insecure 2>/dev/null | grep -q "is_live"';
    $result = shell_exec($checkCommand . '; echo $?');
    
    if (trim($result) === '0') {
        echo "✅ CockroachDB is ready\n\n";
        break;
    }
    
    echo "⏳ Waiting... (attempt " . ($attempt + 1) . "/$maxAttempts)\n";
    sleep(2);
    $attempt++;
}

if ($attempt >= $maxAttempts) {
    echo "❌ CockroachDB failed to start properly\n";
    exit(1);
}

// Create database
echo "4. Creating database...\n";
$createDbCommand = 'docker exec bitsync-cockroach cockroach sql --insecure --execute="CREATE DATABASE IF NOT EXISTS bitsync;"';
$result = shell_exec($createDbCommand . ' 2>&1');
echo "✅ Database created\n\n";

// Run migrations
echo "5. Running database migrations...\n";
require_once __DIR__ . '/database/migrate.php';
$migration = new DatabaseMigration();
$migration->migrate();
echo "✅ Migrations completed\n\n";

// Create admin user
echo "6. Setting up admin user...\n";
require_once __DIR__ . '/includes/Database.php';
$db = Database::getInstance();

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
echo "7. Updating system settings...\n";
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

// Show access information
echo "🎉 Setup completed successfully!\n";
echo "==============================\n";
echo "CockroachDB Admin UI: http://localhost:8080\n";
echo "Database Host: localhost:26257\n";
echo "Database Name: bitsync\n";
echo "Admin Username: " . ($_ENV['ADMIN_USERNAME'] ?? 'admin') . "\n";
echo "Admin Password: " . ($_ENV['ADMIN_PASSWORD'] ?? 'admin123') . "\n\n";

echo "📝 Next steps:\n";
echo "1. Copy env.example to .env and update with your settings\n";
echo "2. Access the admin panel at your website URL\n";
echo "3. Start managing your content through the database\n\n";

echo "🛠️  Useful commands:\n";
echo "Stop database: docker-compose down\n";
echo "View logs: docker-compose logs cockroachdb\n";
echo "Backup data: php database/backup.php\n"; 