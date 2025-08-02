<?php
/**
 * BitSync Admin - Login Guide
 * Simple guide for user login
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guide - BitSync Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    BitSync Admin Access
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    How to log in to the admin dashboard
                </p>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Login Information
                        </h3>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                            <h4 class="font-medium text-blue-900 mb-2">Default Admin Account</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Username:</strong> admin</div>
                                <div><strong>Password:</strong> admin123</div>
                                <div><strong>Email:</strong> admin@bitsync.com</div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <h4 class="font-medium text-yellow-900 mb-2">Important Notes</h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• This is the default admin account created during setup</li>
                                <li>• Change the password after first login for security</li>
                                <li>• You can create additional users from the admin dashboard</li>
                                <li>• Each user can have different roles and permissions</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-users text-green-500 mr-2"></i>
                            User Roles Available
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-crown text-red-500"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Admin</div>
                                    <div class="text-sm text-gray-500">Full system access</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-tie text-blue-500"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Manager</div>
                                    <div class="text-sm text-gray-500">Content and blog management</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-edit text-green-500"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Editor</div>
                                    <div class="text-sm text-gray-500">Blog and page editing</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-eye text-purple-500"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Viewer</div>
                                    <div class="text-sm text-gray-500">Read-only dashboard access</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="login.php" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Go to Login
                        </a>
                        <a href="../" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md text-center">
                            <i class="fas fa-home mr-2"></i>
                            Back to Site
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    Need help? Contact your system administrator
                </p>
            </div>
        </div>
    </div>
</body>
</html> 