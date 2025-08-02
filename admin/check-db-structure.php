<?php
/**
 * Database Structure Check and Fix
 * Diagnoses and fixes database structure issues
 */

require_once __DIR__ . '/../includes/Database.php';

try {
    $db = Database::getInstance();
    
    echo "🔍 Checking database structure...\n\n";
    
    // Check if users table exists
    $tables = $db->fetchAll("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tableNames = array_column($tables, 'table_name');
    
    echo "📋 Existing tables: " . implode(', ', $tableNames) . "\n\n";
    
    if (!in_array('users', $tableNames)) {
        echo "❌ Users table does not exist. Running migration...\n";
        require_once __DIR__ . '/../database/migrate-users.php';
        echo "✅ Migration completed.\n\n";
    } else {
        echo "✅ Users table exists.\n";
        
        // Check users table structure
        $columns = $db->fetchAll("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users'");
        echo "📊 Users table columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column['column_name']} ({$column['data_type']})\n";
        }
        echo "\n";
        
        // Check if required columns exist
        $requiredColumns = ['id', 'username', 'email', 'password_hash', 'first_name', 'last_name', 'is_active', 'is_admin'];
        $existingColumns = array_column($columns, 'column_name');
        
        $missingColumns = array_diff($requiredColumns, $existingColumns);
        if (!empty($missingColumns)) {
            echo "❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
            echo "🔄 Recreating users table...\n";
            
            // Drop and recreate users table
            $db->query("DROP TABLE IF EXISTS user_roles CASCADE");
            $db->query("DROP TABLE IF EXISTS users CASCADE");
            
            // Recreate users table
            $db->query("
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
            
            // Recreate user_roles table
            $db->query("
                CREATE TABLE user_roles (
                    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                    role_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
                    assigned_by UUID REFERENCES users(id),
                    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(user_id, role_id)
                )
            ");
            
            // Create default admin user
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $db->query("
                INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin, is_active) 
                VALUES ('admin', 'admin@bitsync.com', '{$adminPassword}', 'System', 'Administrator', true, true)
            ");
            
            // Get admin user and admin role IDs
            $adminUser = $db->fetchOne("SELECT id FROM users WHERE username = 'admin'");
            $adminRole = $db->fetchOne("SELECT id FROM roles WHERE name = 'admin'");
            
            // Assign admin role to admin user
            if ($adminUser && $adminRole) {
                $db->query("
                    INSERT INTO user_roles (user_id, role_id, assigned_by) 
                    VALUES ('{$adminUser['id']}', '{$adminRole['id']}', '{$adminUser['id']}')
                ");
            }
            
            echo "✅ Users table recreated successfully.\n";
        } else {
            echo "✅ All required columns exist.\n";
        }
    }
    
    // Check roles table
    if (!in_array('roles', $tableNames)) {
        echo "❌ Roles table does not exist. Creating...\n";
        $db->query("
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
        
        // Insert default roles
        $defaultRoles = [
            ['name' => 'admin', 'description' => 'Full system administrator with all permissions', 'permissions' => '["*"]'],
            ['name' => 'manager', 'description' => 'Content manager with limited admin access', 'permissions' => '["content_manage", "blog_manage", "chat_view"]'],
            ['name' => 'editor', 'description' => 'Content editor with blog and page editing permissions', 'permissions' => '["blog_edit", "pages_edit"]'],
            ['name' => 'viewer', 'description' => 'Read-only access to admin dashboard', 'permissions' => '["dashboard_view", "analytics_view"]']
        ];
        
        $roleStmt = $db->prepare("
            INSERT INTO roles (name, description, permissions) 
            VALUES (?, ?, ?)
        ");
        
        foreach ($defaultRoles as $role) {
            $roleStmt->execute([$role['name'], $role['description'], $role['permissions']]);
        }
        
        echo "✅ Roles table created with default roles.\n";
    }
    
    // Test user authentication
    echo "\n🧪 Testing user authentication...\n";
    require_once __DIR__ . '/../includes/User.php';
    $userManager = new User($db);
    
    if ($userManager->authenticate('admin', 'admin123')) {
        echo "✅ Admin authentication successful.\n";
        $userData = $userManager->getUserData();
        echo "📋 User data: " . json_encode($userData, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ Admin authentication failed.\n";
    }
    
    echo "\n✅ Database structure check completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
} 