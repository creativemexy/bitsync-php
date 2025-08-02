<?php
/**
 * Email Client Handler
 * Manages email functionality for user dashboard
 */

class EmailClient {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Send email from user
     */
    public function sendEmail($fromUserId, $toEmail, $subject, $message, $attachments = []) {
        try {
            // Get sender information
            $sender = $this->db->fetchOne(
                "SELECT id, username, email, first_name, last_name FROM users WHERE id = :user_id",
                ['user_id' => $fromUserId]
            );
            
            if (!$sender) {
                return ['success' => false, 'message' => 'Sender not found'];
            }
            
            // Create email record
            $emailId = $this->db->insert('emails', [
                'from_user_id' => $fromUserId,
                'to_email' => $toEmail,
                'subject' => $subject,
                'message' => $message,
                'attachments' => json_encode($attachments),
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Try to send actual email (optional)
            $this->sendActualEmail($sender, $toEmail, $subject, $message);
            
            return ['success' => true, 'message' => 'Email sent successfully', 'email_id' => $emailId];
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to send email'];
        }
    }
    
    /**
     * Get user's sent emails
     */
    public function getSentEmails($userId, $limit = 20, $offset = 0) {
        try {
            return $this->db->fetchAll(
                "SELECT * FROM emails WHERE from_user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
                ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
            );
        } catch (Exception $e) {
            error_log("Get sent emails error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user's received emails
     */
    public function getReceivedEmails($userEmail, $limit = 20, $offset = 0) {
        try {
            return $this->db->fetchAll(
                "SELECT * FROM emails WHERE to_email = :email ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
                ['email' => $userEmail, 'limit' => $limit, 'offset' => $offset]
            );
        } catch (Exception $e) {
            error_log("Get received emails error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get email by ID
     */
    public function getEmail($emailId, $userId) {
        try {
            return $this->db->fetchOne(
                "SELECT e.*, u.username, u.first_name, u.last_name, u.email as sender_email 
                 FROM emails e 
                 LEFT JOIN users u ON e.from_user_id = u.id 
                 WHERE e.id = :email_id AND (e.from_user_id = :user_id OR e.to_email = (SELECT email FROM users WHERE id = :user_id))",
                ['email_id' => $emailId, 'user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Get email error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mark email as read
     */
    public function markAsRead($emailId, $userId) {
        try {
            return $this->db->update('emails', 
                ['is_read' => true, 'read_at' => date('Y-m-d H:i:s')], 
                'id = :email_id AND to_email = (SELECT email FROM users WHERE id = :user_id)', 
                ['email_id' => $emailId, 'user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Mark as read error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete email
     */
    public function deleteEmail($emailId, $userId) {
        try {
            return $this->db->delete('emails', 
                'id = :email_id AND (from_user_id = :user_id OR to_email = (SELECT email FROM users WHERE id = :user_id))', 
                ['email_id' => $emailId, 'user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Delete email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get email statistics
     */
    public function getEmailStats($userId) {
        try {
            $userEmail = $this->db->fetchOne(
                "SELECT email FROM users WHERE id = :user_id",
                ['user_id' => $userId]
            );
            
            if (!$userEmail) {
                return ['sent' => 0, 'received' => 0, 'unread' => 0];
            }
            
            $stats = $this->db->fetchOne(
                "SELECT 
                    COUNT(CASE WHEN from_user_id = :user_id THEN 1 END) as sent,
                    COUNT(CASE WHEN to_email = :email THEN 1 END) as received,
                    COUNT(CASE WHEN to_email = :email AND is_read = false THEN 1 END) as unread
                 FROM emails",
                ['user_id' => $userId, 'email' => $userEmail['email']]
            );
            
            return $stats;
        } catch (Exception $e) {
            error_log("Get email stats error: " . $e->getMessage());
            return ['sent' => 0, 'received' => 0, 'unread' => 0];
        }
    }
    
    /**
     * Search emails
     */
    public function searchEmails($userId, $query, $limit = 20) {
        try {
            $userEmail = $this->db->fetchOne(
                "SELECT email FROM users WHERE id = :user_id",
                ['user_id' => $userId]
            );
            
            if (!$userEmail) {
                return [];
            }
            
            return $this->db->fetchAll(
                "SELECT * FROM emails 
                 WHERE (from_user_id = :user_id OR to_email = :email)
                 AND (subject ILIKE :query OR message ILIKE :query OR to_email ILIKE :query)
                 ORDER BY created_at DESC 
                 LIMIT :limit",
                [
                    'user_id' => $userId, 
                    'email' => $userEmail['email'], 
                    'query' => "%$query%", 
                    'limit' => $limit
                ]
            );
        } catch (Exception $e) {
            error_log("Search emails error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Send actual email (optional - for when mail server is available)
     */
    private function sendActualEmail($sender, $toEmail, $subject, $message) {
        $fromName = $sender['first_name'] . ' ' . $sender['last_name'];
        $fromEmail = $sender['email'];
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'Reply-To: ' . $fromEmail
        ];
        
        // Try to send email, but don't fail if mail server is not configured
        $emailSent = @mail($toEmail, $subject, $message, implode("\r\n", $headers));
        
        // Log the attempt
        error_log("Email client attempt from {$fromEmail} to {$toEmail}: " . ($emailSent ? 'SUCCESS' : 'FAILED - Mail server not configured'));
        
        return $emailSent;
    }
    
    /**
     * Get email templates
     */
    public function getEmailTemplates() {
        return [
            'welcome' => [
                'subject' => 'Welcome to BitSync!',
                'message' => 'Welcome to our platform. We\'re excited to have you on board!'
            ],
            'meeting' => [
                'subject' => 'Meeting Invitation',
                'message' => 'You are invited to attend a meeting. Please let me know if you can make it.'
            ],
            'project' => [
                'subject' => 'Project Update',
                'message' => 'Here is the latest update on our project. Please review and let me know your thoughts.'
            ],
            'reminder' => [
                'subject' => 'Friendly Reminder',
                'message' => 'This is a friendly reminder about our upcoming deadline.'
            ]
        ];
    }
} 