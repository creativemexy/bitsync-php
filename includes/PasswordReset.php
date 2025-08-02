<?php
/**
 * Password Reset Handler
 * Manages password reset functionality for admin and regular users
 */

class PasswordReset {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Generate a secure reset token
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Create a password reset request
     */
    public function createResetRequest($email, $userType = 'user') {
        try {
            // Check if user exists
            $user = $this->db->fetchOne(
                "SELECT id, username, email, first_name, last_name FROM users WHERE email = :email AND is_active = true",
                ['email' => $email]
            );
            
            if (!$user) {
                return ['success' => false, 'message' => 'No active account found with this email address'];
            }
            
            // Generate reset token
            $token = $this->generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing reset requests for this user
            $this->db->delete('password_resets', 'user_id = :user_id', ['user_id' => $user['id']]);
            
            // Create new reset request
            $this->db->insert('password_resets', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expiresAt,
                'user_type' => $userType,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Send reset email
            $resetLink = $this->getResetLink($token, $userType);
            $this->sendResetEmail($user, $resetLink);
            
            return ['success' => true, 'message' => 'Password reset instructions sent to your email'];
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process reset request'];
        }
    }
    
    /**
     * Verify reset token
     */
    public function verifyToken($token) {
        try {
            $reset = $this->db->fetchOne(
                "SELECT pr.*, u.username, u.email, u.first_name, u.last_name 
                 FROM password_resets pr 
                 JOIN users u ON pr.user_id = u.id 
                 WHERE pr.token = :token AND pr.expires_at > NOW()",
                ['token' => $token]
            );
            
            if (!$reset) {
                return ['success' => false, 'message' => 'Invalid or expired reset token'];
            }
            
            return ['success' => true, 'data' => $reset];
            
        } catch (Exception $e) {
            error_log("Token verification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to verify token'];
        }
    }
    
    /**
     * Reset password using token
     */
    public function resetPassword($token, $newPassword) {
        try {
            // Verify token
            $result = $this->verifyToken($token);
            if (!$result['success']) {
                return $result;
            }
            
            $reset = $result['data'];
            
            // Update password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->update('users', 
                ['password_hash' => $passwordHash], 
                'id = :user_id', 
                ['user_id' => $reset['user_id']]
            );
            
            // Delete reset token
            $this->db->delete('password_resets', 'token = :token', ['token' => $token]);
            
            // Send confirmation email
            $this->sendPasswordChangedEmail($reset);
            
            return ['success' => true, 'message' => 'Password updated successfully'];
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to reset password'];
        }
    }
    
    /**
     * Get reset link based on user type
     */
    private function getResetLink($token, $userType) {
        $baseUrl = $this->getBaseUrl();
        
        if ($userType === 'admin') {
            return $baseUrl . '/admin/reset-password.php?token=' . $token;
        } else {
            return $baseUrl . '/user/reset-password.php?token=' . $token;
        }
    }
    
    /**
     * Get base URL
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Send reset email
     */
    private function sendResetEmail($user, $resetLink) {
        $to = $user['email'];
        $subject = 'Password Reset Request - BitSync';
        
        $message = "
        <html>
        <head>
            <title>Password Reset Request</title>
        </head>
        <body>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;'>
                <h2 style='color: #0ea5e9;'>Password Reset Request</h2>
                <p>Hello " . htmlspecialchars($user['first_name'] ?? $user['username']) . ",</p>
                <p>We received a request to reset your password for your BitSync account.</p>
                <p>Click the button below to reset your password:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$resetLink' style='background-color: #0ea5e9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Reset Password</a>
                </div>
                <p>This link will expire in 1 hour for security reasons.</p>
                <p>If you didn't request this password reset, please ignore this email.</p>
                <p>Best regards,<br>The BitSync Team</p>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: BitSync <noreply@bitsync.com>',
            'Reply-To: support@bitsync.com'
        ];
        
        // Try to send email, but don't fail if mail server is not configured
        $emailSent = @mail($to, $subject, $message, implode("\r\n", $headers));
        
        // Log the attempt
        error_log("Password reset email attempt for {$user['email']}: " . ($emailSent ? 'SUCCESS' : 'FAILED - Mail server not configured'));
        
        return $emailSent;
    }
    
    /**
     * Send password changed confirmation email
     */
    private function sendPasswordChangedEmail($user) {
        $to = $user['email'];
        $subject = 'Password Changed - BitSync';
        
        $message = "
        <html>
        <head>
            <title>Password Changed</title>
        </head>
        <body>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;'>
                <h2 style='color: #10b981;'>Password Changed Successfully</h2>
                <p>Hello " . htmlspecialchars($user['first_name'] ?? $user['username']) . ",</p>
                <p>Your BitSync account password has been successfully changed.</p>
                <p>If you didn't make this change, please contact support immediately.</p>
                <p>Best regards,<br>The BitSync Team</p>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: BitSync <noreply@bitsync.com>',
            'Reply-To: support@bitsync.com'
        ];
        
        // Try to send email, but don't fail if mail server is not configured
        $emailSent = @mail($to, $subject, $message, implode("\r\n", $headers));
        
        // Log the attempt
        error_log("Password changed email attempt for {$user['email']}: " . ($emailSent ? 'SUCCESS' : 'FAILED - Mail server not configured'));
        
        return $emailSent;
    }
    
    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens() {
        try {
            $this->db->delete('password_resets', 'expires_at < NOW()');
            return true;
        } catch (Exception $e) {
            error_log("Cleanup error: " . $e->getMessage());
            return false;
        }
    }
} 