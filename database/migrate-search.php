<?php
/**
 * Search System Migration
 * Creates search tables in CockroachDB
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

require_once __DIR__ . '/../includes/Database.php';

class SearchMigration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function migrate() {
        echo "🚀 Starting Search System Migration\n";
        echo "==================================\n\n";
        
        // Read search schema
        $schemaFile = __DIR__ . '/search-schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("Search schema file not found: $schemaFile");
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
            echo "🎉 Search system migration completed successfully!\n";
            echo "\n📋 Created Tables:\n";
            echo "- search_logs (Search query tracking)\n";
            echo "- search_analytics (Search analytics)\n";
            echo "- search_suggestions (Search suggestions)\n";
            echo "- search_clicks (Result click tracking)\n";
            echo "\n📊 Created Views:\n";
            echo "- search_trends (Search trends over time)\n";
            echo "- popular_search_terms (Most popular searches)\n";
            echo "- search_performance (Search success rates)\n";
            echo "\n🔍 Search Features:\n";
            echo "- Real-time search across all content\n";
            echo "- Advanced filtering by content type\n";
            echo "- Search analytics and insights\n";
            echo "- Popular search suggestions\n";
            echo "- Click tracking for optimization\n";
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
        $migration = new SearchMigration();
        $migration->migrate();
    } catch (Exception $e) {
        echo "❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} 