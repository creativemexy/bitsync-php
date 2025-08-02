<?php
/**
 * Notification Handler
 * Manages notifications for user dashboard
 */

class Notification {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Create a new notification
     */
    public function create($userId, $type, $title, $message, $data = []) {
        try {
            return $this->db->insert('notifications', [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get notifications for a user
     */
    public function getForUser($userId, $limit = 20, $offset = 0) {
        try {
            return $this->db->fetchAll(
                "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
                ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
            );
        } catch (Exception $e) {
            error_log("Get notifications error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId) {
        try {
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = false",
                ['user_id' => $userId]
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Get unread count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId) {
        try {
            return $this->db->update('notifications', 
                ['is_read' => true], 
                'id = :id AND user_id = :user_id', 
                ['id' => $notificationId, 'user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Mark as read error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId) {
        try {
            return $this->db->update('notifications', 
                ['is_read' => true], 
                'user_id = :user_id', 
                ['user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Mark all as read error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete notification
     */
    public function delete($notificationId, $userId) {
        try {
            return $this->db->delete('notifications', 
                'id = :id AND user_id = :user_id', 
                ['id' => $notificationId, 'user_id' => $userId]
            );
        } catch (Exception $e) {
            error_log("Delete notification error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get notification types
     */
    public function getTypes() {
        return [
            'info' => ['icon' => 'fas fa-info-circle', 'color' => 'text-blue-500'],
            'success' => ['icon' => 'fas fa-check-circle', 'color' => 'text-green-500'],
            'warning' => ['icon' => 'fas fa-exclamation-triangle', 'color' => 'text-yellow-500'],
            'error' => ['icon' => 'fas fa-times-circle', 'color' => 'text-red-500'],
            'system' => ['icon' => 'fas fa-cog', 'color' => 'text-gray-500'],
            'update' => ['icon' => 'fas fa-sync-alt', 'color' => 'text-purple-500']
        ];
    }
    
    /**
     * Create system notification
     */
    public function createSystemNotification($title, $message, $type = 'info') {
        try {
            // Get all active users
            $users = $this->db->fetchAll(
                "SELECT id FROM users WHERE is_active = true"
            );
            
            $count = 0;
            foreach ($users as $user) {
                if ($this->create($user['id'], $type, $title, $message)) {
                    $count++;
                }
            }
            
            return $count;
        } catch (Exception $e) {
            error_log("System notification error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get notification statistics
     */
    public function getStats($userId) {
        try {
            $stats = $this->db->fetchOne(
                "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN is_read = false THEN 1 END) as unread,
                    COUNT(CASE WHEN type = 'info' THEN 1 END) as info,
                    COUNT(CASE WHEN type = 'success' THEN 1 END) as success,
                    COUNT(CASE WHEN type = 'warning' THEN 1 END) as warning,
                    COUNT(CASE WHEN type = 'error' THEN 1 END) as error
                 FROM notifications 
                 WHERE user_id = :user_id",
                ['user_id' => $userId]
            );
            
            return $stats;
        } catch (Exception $e) {
            error_log("Get stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'unread' => 0,
                'info' => 0,
                'success' => 0,
                'warning' => 0,
                'error' => 0
            ];
        }
    }
} 