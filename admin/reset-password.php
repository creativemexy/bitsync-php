<?php
/**
 * Admin Reset Password
 * Allows admins to reset their password using a token
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/PasswordReset.php';

$token = $_GET['token'] ?? '';
$message = '';
$error = '';
$userData = null;

// Verify token
if (!empty($token)) {
    try {
        $db = Database::getInstance();
        $passwordReset = new PasswordReset($db);
        
        $result = $passwordReset->verifyToken($token);
        
        if ($result['success']) {
            $userData = $result['data'];
        } else {
            $error = $result['message'];
        }
        
    } catch (Exception $e) {
        $error = 'Failed to verify reset token';
    }
} else {
    $error = 'Invalid reset link';
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userData) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword)) {
        $error = 'Please enter a new password';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        try {
            $db = Database::getInstance();
            $passwordReset = new PasswordReset($db);
            
            $result = $passwordReset->resetPassword($token, $newPassword);
            
            if ($result['success']) {
                $message = $result['message'];
                // Redirect to login after 3 seconds
                header('refresh:3;url=login.php');
            } else {
                $error = $result['message'];
            }
            
        } catch (Exception $e) {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BitSync Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100">
                    <i class="fas fa-key text-green-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Reset your password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    <?php if ($userData): ?>
                        Setting new password for <strong><?php echo htmlspecialchars($userData['email']); ?></strong>
                    <?php else: ?>
                        Enter your new password below
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if ($message): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <p class="mt-2 text-sm">Redirecting to login page...</p>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($userData && !$message): ?>
                <form class="mt-8 space-y-6" method="POST">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <div class="mt-1">
                            <input id="new_password" name="new_password" type="password" required 
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                   placeholder="Enter your new password"
                                   minlength="8">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="mt-1">
                            <input id="confirm_password" name="confirm_password" type="password" required 
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                   placeholder="Confirm your new password"
                                   minlength="8">
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-save text-green-500 group-hover:text-green-400"></i>
                            </span>
                            Reset Password
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="text-center">
                <a href="login.php" class="text-blue-600 hover:text-blue-500 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to login
                </a>
            </div>
            
            <?php if ($userData): ?>
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Security Tips
                    </h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Use a strong, unique password</li>
                        <li>• Include letters, numbers, and symbols</li>
                        <li>• Avoid common words or patterns</li>
                        <li>• Consider using a password manager</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 