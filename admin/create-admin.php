<?php
/**
 * Create Admin User
 * Emergency script to create admin user in database
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        echo "<p style='color: red;'>❌ .env file not found</p>";
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

echo "<h1>Create Admin User</h1>";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($username) && !empty($password)) {
        try {
            require_once __DIR__ . '/../includes/Database.php';
            $db = Database::getInstance();
            
            // Check if user already exists
            $existing_user = $db->fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
            
            if ($existing_user) {
                echo "<p style='color: orange;'>⚠️ User '$username' already exists. Updating password...</p>";
                
                // Update password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $db->update('users', 
                    ['password_hash' => $password_hash, 'is_active' => true], 
                    'username = ?', 
                    [$username]
                );
                
                echo "<p style='color: green;'>✅ User '$username' password updated successfully!</p>";
            } else {
                // Create new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $user_data = [
                    'username' => $username,
                    'password_hash' => $password_hash,
                    'email' => $email,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $user_id = $db->insert('users', $user_data);
                
                if ($user_id) {
                    echo "<p style='color: green;'>✅ User '$username' created successfully with ID: $user_id</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create user</p>";
                }
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Please provide username and password</p>";
    }
}

// Check if users table exists
try {
    require_once __DIR__ . '/../includes/Database.php';
    $db = Database::getInstance();
    
    $tables = $db->fetchAll("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'users'");
    
    if (empty($tables)) {
        echo "<p style='color: red;'>❌ Users table does not exist. Creating table...</p>";
        
        // Create users table
        $create_table_sql = "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                is_active BOOLEAN DEFAULT true,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $db->query($create_table_sql);
        echo "<p style='color: green;'>✅ Users table created successfully!</p>";
    } else {
        echo "<p style='color: green;'>✅ Users table exists</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create Admin User
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Create or update admin user for emergency access
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? 'admin'); ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Password"
                           value="<?php echo htmlspecialchars($_POST['password'] ?? 'admin123'); ?>">
                </div>
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Email (optional)"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? 'admin@bitsync.com'); ?>">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create/Update User
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Default: <code class="bg-gray-100 px-2 py-1 rounded">admin / admin123</code>
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    ⚠️ Delete this file after creating the user for security
                </p>
            </div>
        </form>
        
        <div class="text-center">
            <a href="login.php" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to Login
            </a>
        </div>
    </div>
</body>
</html> 