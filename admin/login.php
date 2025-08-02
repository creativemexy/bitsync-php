<?php
/**
 * BitSync Group Admin Login
 * Enhanced authentication with debugging and fallback options
 */

session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fallback admin credentials (for emergency access)
$FALLBACK_ADMIN = [
    'username' => 'admin',
    'password' => 'admin123',
    'password_hash' => password_hash('admin123', PASSWORD_DEFAULT)
];

$error = '';
$debug_info = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            // Try database authentication first
            require_once __DIR__ . '/../includes/Database.php';
            require_once __DIR__ . '/../includes/User.php';
            
            $db = Database::getInstance();
            $userManager = new User($db);
            
            if ($userManager->authenticate($username, $password)) {
                // Database login successful
                $userData = $userManager->getUserData();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $userData['id'];
                $_SESSION['admin_username'] = $userData['username'];
                $_SESSION['admin_user_data'] = $userData;
                $_SESSION['admin_permissions'] = $userManager->getPermissions();
                $_SESSION['auth_method'] = 'database';
                
                // Redirect to dashboard
                header('Location: index.php');
                exit;
            } else {
                // Try fallback authentication
                if ($username === $FALLBACK_ADMIN['username'] && 
                    password_verify($password, $FALLBACK_ADMIN['password_hash'])) {
                    
                    // Fallback login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = 'fallback';
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['auth_method'] = 'fallback';
                    
                    // Redirect to dashboard
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password';
                    $debug_info = 'Database authentication failed, fallback authentication also failed';
                }
            }
        } catch (Exception $e) {
            // Database connection failed, try fallback authentication
            if ($username === $FALLBACK_ADMIN['username'] && 
                password_verify($password, $FALLBACK_ADMIN['password_hash'])) {
                
                // Fallback login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = 'fallback';
                $_SESSION['admin_username'] = $username;
                $_SESSION['auth_method'] = 'fallback';
                
                // Redirect to dashboard
                header('Location: index.php');
                exit;
            } else {
                $error = 'Login failed. Please try again.';
                $debug_info = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Please enter both username and password';
    }
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Check database connection status
$db_status = 'Unknown';
$db_error = '';
try {
    require_once __DIR__ . '/../includes/Database.php';
    $db = Database::getInstance();
    $db_status = 'Connected';
} catch (Exception $e) {
    $db_status = 'Failed';
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitSync Admin - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                BitSync Admin
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sign in to your admin account
            </p>
        </div>
        
        <!-- Database Status Indicator -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Database Status:</span>
                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $db_status === 'Connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $db_status; ?>
                </span>
            </div>
            <?php if ($db_error): ?>
                <p class="text-xs text-red-600 mt-1"><?php echo htmlspecialchars($db_error); ?></p>
            <?php endif; ?>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($debug_info && isset($_GET['debug'])): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md text-sm">
                    <i class="fas fa-bug mr-2"></i>
                    Debug: <?php echo htmlspecialchars($debug_info); ?>
                </div>
            <?php endif; ?>
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Sign in
                </button>
            </div>
            
            <div class="text-center space-y-2">
                <p class="text-sm text-gray-600">
                    Default credentials: <code class="bg-gray-100 px-2 py-1 rounded">admin / admin123</code>
                </p>
                <p class="text-xs text-gray-500">
                    <?php if ($db_status !== 'Connected'): ?>
                        ⚠️ Database connection failed. Using fallback authentication.
                    <?php endif; ?>
                </p>
                <a href="forgot-password.php" class="text-blue-600 hover:text-blue-500 text-sm block">
                    <i class="fas fa-lock mr-1"></i>
                    Forgot your password?
                </a>
            </div>
        </form>
        
        <!-- Debug Link (remove in production) -->
        <?php if (isset($_GET['debug']) || $db_status !== 'Connected'): ?>
            <div class="text-center">
                <a href="?debug=1" class="text-xs text-blue-600 hover:text-blue-800">
                    Debug Mode: <?php echo isset($_GET['debug']) ? 'ON' : 'OFF'; ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html> 