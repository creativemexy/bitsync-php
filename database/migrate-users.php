<?php
/**
 * Database Migration for User Role System
 * Creates users, roles, and user_roles tables
 */

require_once __DIR__ . '/../config/database.php';

try {
    $config = require __DIR__ . '/../config/database.php';
    $db = $config['cockroachdb'];
    
    $dsn = $config['dsn']();
    $pdo = new PDO($dsn, $db['username'], $db['password'], $db['options']);
    
    // Enable UUID extension
    $pdo->exec("CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\"");
    
    echo "Starting user role system migration...\n";
    
    // Drop existing tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS user_roles CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS roles CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS users CASCADE");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE users (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            is_active BOOLEAN DEFAULT true,
            is_admin BOOLEAN DEFAULT false,
            last_login TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create roles table
    $pdo->exec("
        CREATE TABLE roles (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            name VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            permissions JSONB DEFAULT '{}',
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create user_roles junction table
    $pdo->exec("
        CREATE TABLE user_roles (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            role_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
            assigned_by UUID REFERENCES users(id),
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(user_id, role_id)
        )
    ");
    
    // Insert default roles
    $defaultRoles = [
        ['name' => 'admin', 'description' => 'Full system administrator with all permissions', 'permissions' => '["*"]'],
        ['name' => 'manager', 'description' => 'Content manager with limited admin access', 'permissions' => '["content_manage", "blog_manage", "chat_view"]'],
        ['name' => 'editor', 'description' => 'Content editor with blog and page editing permissions', 'permissions' => '["blog_edit", "pages_edit"]'],
        ['name' => 'viewer', 'description' => 'Read-only access to admin dashboard', 'permissions' => '["dashboard_view", "analytics_view"]']
    ];
    
    $roleStmt = $pdo->prepare("
        INSERT INTO roles (name, description, permissions) 
        VALUES (?, ?, ?)
    ");
    
    foreach ($defaultRoles as $role) {
        $roleStmt->execute([$role['name'], $role['description'], $role['permissions']]);
    }
    
    // Create default admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin, is_active) 
        VALUES ('admin', 'admin@bitsync.com', '{$adminPassword}', 'System', 'Administrator', true, true)
    ");
    
    // Get admin user and admin role IDs
    $adminUser = $pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetch();
    $adminRole = $pdo->query("SELECT id FROM roles WHERE name = 'admin'")->fetch();
    
    // Assign admin role to admin user
    if ($adminUser && $adminRole) {
        $pdo->exec("
            INSERT INTO user_roles (user_id, role_id, assigned_by) 
            VALUES ('{$adminUser['id']}', '{$adminRole['id']}', '{$adminUser['id']}')
        ");
    }
    
    echo "✅ User role system migration completed successfully!\n";
    echo "Default admin user created: admin / admin123\n";
    echo "Default roles created: admin, manager, editor, viewer\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} 