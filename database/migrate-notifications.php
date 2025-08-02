<?php
/**
 * Notifications Database Migration
 * Creates the notifications table for the notification system
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "🔄 Creating notifications table...\n";
    
    // Create notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            type VARCHAR(20) DEFAULT 'info',
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            data JSONB DEFAULT '{}',
            is_read BOOLEAN DEFAULT false,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL
        )
    ");
    
    // Create indexes for better performance
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications(is_read)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type)
    ");
    
    echo "✅ Notifications table created successfully!\n";
    echo "📋 Table structure:\n";
    echo "   - id (UUID, Primary Key)\n";
    echo "   - user_id (UUID, Foreign Key to users)\n";
    echo "   - type (VARCHAR, Default: 'info')\n";
    echo "   - title (VARCHAR)\n";
    echo "   - message (TEXT)\n";
    echo "   - data (JSONB)\n";
    echo "   - is_read (BOOLEAN, Default: false)\n";
    echo "   - created_at (TIMESTAMP)\n";
    echo "   - read_at (TIMESTAMP, Nullable)\n";
    
    // Test the table
    $result = $pdo->query("SELECT COUNT(*) as count FROM notifications")->fetch();
    echo "✅ Table test successful - " . $result['count'] . " records found\n";
    
    // Create some sample notifications for testing
    echo "🔄 Creating sample notifications...\n";
    
    // Get a test user
    $testUser = $pdo->query("SELECT id FROM users LIMIT 1")->fetch();
    
    if ($testUser) {
        $sampleNotifications = [
            [
                'type' => 'info',
                'title' => 'Welcome to BitSync!',
                'message' => 'Welcome to your new workspace. Get started by exploring the dashboard.',
                'data' => ['action' => 'dashboard_tour']
            ],
            [
                'type' => 'success',
                'title' => 'Account Setup Complete',
                'message' => 'Your account has been successfully configured and is ready to use.',
                'data' => ['action' => 'account_ready']
            ],
            [
                'type' => 'warning',
                'title' => 'Password Update Recommended',
                'message' => 'Consider updating your password for better security.',
                'data' => ['action' => 'password_update']
            ]
        ];
        
        $count = 0;
        foreach ($sampleNotifications as $notification) {
            $pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, data)
                VALUES (:user_id, :type, :title, :message, :data)
            ")->execute([
                'user_id' => $testUser['id'],
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'data' => json_encode($notification['data'])
            ]);
            $count++;
        }
        
        echo "✅ Created $count sample notifications\n";
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} 