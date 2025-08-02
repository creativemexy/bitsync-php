<?php
/**
 * Emails Database Migration
 * Creates the emails table for the email client functionality
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "🔄 Creating emails table...\n";
    
    // Create emails table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS emails (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            from_user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            to_email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            attachments JSONB DEFAULT '{}',
            status VARCHAR(20) DEFAULT 'sent',
            is_read BOOLEAN DEFAULT false,
            is_starred BOOLEAN DEFAULT false,
            is_deleted BOOLEAN DEFAULT false,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL
        )
    ");
    
    // Create indexes for better performance
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_emails_from_user_id ON emails(from_user_id)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_emails_to_email ON emails(to_email)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_emails_created_at ON emails(created_at)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_emails_status ON emails(status)
    ");
    
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_emails_is_read ON emails(is_read)
    ");
    
    echo "✅ Emails table created successfully!\n";
    echo "📋 Table structure:\n";
    echo "   - id (UUID, Primary Key)\n";
    echo "   - from_user_id (UUID, Foreign Key to users)\n";
    echo "   - to_email (VARCHAR)\n";
    echo "   - subject (VARCHAR)\n";
    echo "   - message (TEXT)\n";
    echo "   - attachments (JSONB)\n";
    echo "   - status (VARCHAR, Default: 'sent')\n";
    echo "   - is_read (BOOLEAN, Default: false)\n";
    echo "   - is_starred (BOOLEAN, Default: false)\n";
    echo "   - is_deleted (BOOLEAN, Default: false)\n";
    echo "   - created_at (TIMESTAMP)\n";
    echo "   - read_at (TIMESTAMP, Nullable)\n";
    echo "   - deleted_at (TIMESTAMP, Nullable)\n";
    
    // Test the table
    $result = $pdo->query("SELECT COUNT(*) as count FROM emails")->fetch();
    echo "✅ Table test successful - " . $result['count'] . " records found\n";
    
    // Create some sample emails for testing
    echo "🔄 Creating sample emails...\n";
    
    // Get test users
    $users = $pdo->query("SELECT id, email FROM users LIMIT 2")->fetchAll();
    
    if (count($users) >= 2) {
        $sampleEmails = [
            [
                'from_user_id' => $users[0]['id'],
                'to_email' => $users[1]['email'],
                'subject' => 'Welcome to BitSync Email System',
                'message' => 'Welcome to our new email system! You can now send and receive emails directly from your dashboard.',
                'status' => 'sent'
            ],
            [
                'from_user_id' => $users[1]['id'],
                'to_email' => $users[0]['email'],
                'subject' => 'Re: Welcome to BitSync Email System',
                'message' => 'Thank you! I\'m excited to try out the new email features.',
                'status' => 'sent'
            ],
            [
                'from_user_id' => $users[0]['id'],
                'to_email' => $users[1]['email'],
                'subject' => 'Project Collaboration',
                'message' => 'Let\'s work together on the new project. I\'ve attached the initial requirements.',
                'status' => 'sent'
            ]
        ];
        
        $count = 0;
        foreach ($sampleEmails as $email) {
            $pdo->prepare("
                INSERT INTO emails (from_user_id, to_email, subject, message, status)
                VALUES (:from_user_id, :to_email, :subject, :message, :status)
            ")->execute($email);
            $count++;
        }
        
        echo "✅ Created $count sample emails\n";
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} 