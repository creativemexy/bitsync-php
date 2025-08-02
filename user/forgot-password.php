<?php
/**
 * User Forgot Password
 * Allows regular users to request password reset
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/PasswordReset.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($email)) {
        try {
            $db = Database::getInstance();
            $passwordReset = new PasswordReset($db);
            
            $result = $passwordReset->createResetRequest($email, 'user');
            
            if ($result['success']) {
                $message = $result['message'];
            } else {
                $error = $result['message'];
            }
            
        } catch (Exception $e) {
            $error = 'Failed to process request. Please try again.';
        }
    } else {
        $error = 'Please enter your email address';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - BitSync Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        workspace: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-workspace-100">
                    <i class="fas fa-lock text-workspace-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Forgot your password?
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your email address and we'll send you a link to reset your password
                </p>
            </div>
            
            <form class="mt-8 space-y-6" method="POST">
                <?php if ($message): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required 
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-workspace-500 focus:border-workspace-500 focus:z-10 sm:text-sm"
                               placeholder="Enter your email address">
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-workspace-600 hover:bg-workspace-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-workspace-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane text-workspace-500 group-hover:text-workspace-400"></i>
                        </span>
                        Send Reset Link
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="login.php" class="text-workspace-600 hover:text-workspace-500 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to login
                    </a>
                </div>
            </form>
            
            <div class="mt-6 bg-workspace-50 border border-workspace-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-workspace-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    What happens next?
                </h3>
                <ul class="text-sm text-workspace-700 space-y-1">
                    <li>• We'll send a reset link to your email</li>
                    <li>• The link will expire in 1 hour</li>
                    <li>• You can only use the link once</li>
                    <li>• Check your spam folder if you don't receive it</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html> 