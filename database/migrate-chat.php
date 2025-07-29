<?php
/**
 * Chat System Database Migration
 * Creates tables for enhanced live chat functionality
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Create chat_sessions table
    $db->query("
        CREATE TABLE IF NOT EXISTS chat_sessions (
            id SERIAL PRIMARY KEY,
            session_id VARCHAR(255) UNIQUE NOT NULL,
            user_id VARCHAR(255),
            user_agent TEXT,
            ip_address VARCHAR(45),
            started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create chat_messages table
    $db->query("
        CREATE TABLE IF NOT EXISTS chat_messages (
            id SERIAL PRIMARY KEY,
            session_id VARCHAR(255) NOT NULL,
            user_id VARCHAR(255),
            message TEXT NOT NULL,
            message_type VARCHAR(20) DEFAULT 'text',
            file_name VARCHAR(255),
            file_size INTEGER,
            file_type VARCHAR(100),
            file_url VARCHAR(500),
            is_read BOOLEAN DEFAULT FALSE,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (session_id) REFERENCES chat_sessions(session_id) ON DELETE CASCADE
        )
    ");
    
    // Create chat_quick_responses table
    $db->query("
        CREATE TABLE IF NOT EXISTS chat_quick_responses (
            id SERIAL PRIMARY KEY,
            category VARCHAR(100) NOT NULL,
            response_text TEXT NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            sort_order INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create chat_analytics table
    $db->query("
        CREATE TABLE IF NOT EXISTS chat_analytics (
            id SERIAL PRIMARY KEY,
            session_id VARCHAR(255),
            event_type VARCHAR(50) NOT NULL,
            event_data JSONB,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert default quick responses
    $defaultResponses = [
        ['Web Development', 'I need help with web development'],
        ['Services', 'Tell me about your services'],
        ['Quote', 'I want to get a quote'],
        ['Consultation', 'Schedule a consultation'],
        ['Contact', 'How can I contact you?'],
        ['Pricing', 'What are your pricing options?']
    ];
    
    foreach ($defaultResponses as $index => $response) {
        $db->query(
            "INSERT INTO chat_quick_responses (category, response_text, sort_order) VALUES (?, ?, ?) ON CONFLICT DO NOTHING",
            [$response[0], $response[1], $index + 1]
        );
    }
    
    // Create indexes for better performance
    $db->query("CREATE INDEX IF NOT EXISTS idx_chat_messages_session_id ON chat_messages(session_id)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_chat_messages_timestamp ON chat_messages(timestamp)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_chat_sessions_status ON chat_sessions(status)");
    $db->query("CREATE INDEX IF NOT EXISTS idx_chat_analytics_event_type ON chat_analytics(event_type)");
    
    echo "✅ Chat system database migration completed successfully!\n";
    echo "📊 Created tables:\n";
    echo "   - chat_sessions\n";
    echo "   - chat_messages\n";
    echo "   - chat_quick_responses\n";
    echo "   - chat_analytics\n";
    echo "🔧 Added indexes for optimal performance\n";
    echo "💬 Inserted default quick responses\n";
    
} catch (Exception $e) {
    echo "❌ Error during chat migration: " . $e->getMessage() . "\n";
    exit(1);
}
?> 