<?php
/**
 * Monitoring System Migration
 * Creates monitoring tables in CockroachDB
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

loadEnv(__DIR__ . '/../.env');

require_once '../includes/Database.php';

class MonitoringMigration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function migrate() {
        echo "🚀 Starting Monitoring System Migration\n";
        echo "=====================================\n\n";
        
        // Read monitoring schema
        $schemaFile = __DIR__ . '/monitoring-schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("Monitoring schema file not found: $schemaFile");
        }
        
        $schema = file_get_contents($schemaFile);
        $statements = $this->parseSQLStatements($schema);
        
        echo "📋 Found " . count($statements) . " SQL statements to execute\n\n";
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $index => $statement) {
            $statement = trim($statement);
            if (empty($statement)) continue;
            
            echo "Executing statement " . ($index + 1) . "... ";
            
            try {
                $this->db->query($statement);
                echo "✅ Success\n";
                $successCount++;
            } catch (Exception $e) {
                echo "❌ Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
        
        echo "\n📊 Migration Summary:\n";
        echo "===================\n";
        echo "✅ Successful: $successCount\n";
        echo "❌ Errors: $errorCount\n";
        echo "📋 Total: " . count($statements) . "\n\n";
        
        if ($errorCount === 0) {
            echo "🎉 Monitoring system migration completed successfully!\n";
            echo "\n📋 Created Tables:\n";
            echo "- request_logs (Request tracking)\n";
            echo "- system_errors (Error logging)\n";
            echo "- page_views (Page view analytics)\n";
            echo "- form_submissions (Form tracking)\n";
            echo "- performance_metrics (Performance data)\n";
            echo "- system_health (Health snapshots)\n";
            echo "- user_sessions (Session tracking)\n";
            echo "- api_usage (API monitoring)\n";
            echo "- security_events (Security logging)\n";
            echo "\n📊 Created Views:\n";
            echo "- recent_activity (Recent site activity)\n";
            echo "- daily_stats (Daily statistics)\n";
            echo "- performance_summary (Performance overview)\n";
        } else {
            echo "⚠️  Migration completed with errors. Please check the output above.\n";
        }
    }
    
    private function parseSQLStatements($sql) {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Split by semicolon
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        return $statements;
    }
}

// Run migration if called directly
if (php_sapi_name() === 'cli' || isset($_GET['run'])) {
    try {
        $migration = new MonitoringMigration();
        $migration->migrate();
    } catch (Exception $e) {
        echo "❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} 