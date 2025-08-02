<?php
/**
 * Password Reset Database Migration
 * Creates the password_resets table for password reset functionality
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "🔄 Creating password_resets table...\n";
    
    // Create password_resets table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            token VARCHAR(255) UNIQUE NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            user_type VARCHAR(20) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            used_at TIMESTAMP NULL
        )
    ");
    
    // Create index for faster token lookups
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token)
    ");
    
    // Create index for cleanup operations
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_password_resets_expires ON password_resets(expires_at)
    ");
    
    // Create index for user lookups
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_password_resets_user_id ON password_resets(user_id)
    ");
    
    echo "✅ Password reset table created successfully!\n";
    echo "📋 Table structure:\n";
    echo "   - id (UUID, Primary Key)\n";
    echo "   - user_id (UUID, Foreign Key to users)\n";
    echo "   - token (VARCHAR, Unique)\n";
    echo "   - expires_at (TIMESTAMP)\n";
    echo "   - user_type (VARCHAR, Default: 'user')\n";
    echo "   - created_at (TIMESTAMP)\n";
    echo "   - used_at (TIMESTAMP, Nullable)\n";
    
    // Test the table
    $result = $pdo->query("SELECT COUNT(*) as count FROM password_resets")->fetch();
    echo "✅ Table test successful - " . $result['count'] . " records found\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} 