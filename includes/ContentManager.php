<?php
/**
 * Content Manager for BitSync Group
 * Handles content operations with CockroachDB
 */

require_once __DIR__ . '/Database.php';

class ContentManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get page content by key
     */
    public function getPageContent($pageKey) {
        try {
            $result = $this->db->fetchOne(
                "SELECT * FROM content_pages WHERE page_key = ? AND is_published = true",
                [$pageKey]
            );
            
            if ($result) {
                return json_decode($result['content'], true);
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting page content: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Save page content
     */
    public function savePageContent($pageKey, $content, $title = null, $description = null) {
        try {
            $existing = $this->db->fetchOne(
                "SELECT id FROM content_pages WHERE page_key = ?",
                [$pageKey]
            );
            
            $data = [
                'page_key' => $pageKey,
                'content' => json_encode($content),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($title) $data['title'] = $title;
            if ($description) $data['description'] = $description;
            
            if ($existing) {
                // Update existing page
                $this->db->update('content_pages', $data, 'page_key = ?', [$pageKey]);
                return $existing['id'];
            } else {
                // Create new page
                $data['created_at'] = date('Y-m-d H:i:s');
                return $this->db->insert('content_pages', $data);
            }
        } catch (Exception $e) {
            error_log("Error saving page content: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get all pages
     */
    public function getAllPages() {
        try {
            return $this->db->fetchAll(
                "SELECT page_key, title, description, is_published, created_at, updated_at FROM content_pages ORDER BY created_at DESC"
            );
        } catch (Exception $e) {
            error_log("Error getting all pages: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete page
     */
    public function deletePage($pageKey) {
        try {
            return $this->db->delete('content_pages', 'page_key = ?', [$pageKey]);
        } catch (Exception $e) {
            error_log("Error deleting page: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get system settings
     */
    public function getSetting($key, $default = null) {
        try {
            $result = $this->db->fetchOne(
                "SELECT setting_value, setting_type FROM system_settings WHERE setting_key = ?",
                [$key]
            );
            
            if ($result) {
                switch ($result['setting_type']) {
                    case 'boolean':
                        return filter_var($result['setting_value'], FILTER_VALIDATE_BOOLEAN);
                    case 'number':
                        return is_numeric($result['setting_value']) ? (float)$result['setting_value'] : $default;
                    case 'json':
                        return json_decode($result['setting_value'], true);
                    default:
                        return $result['setting_value'];
                }
            }
            
            return $default;
        } catch (Exception $e) {
            error_log("Error getting setting: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Save system setting
     */
    public function saveSetting($key, $value, $type = 'string', $description = null) {
        try {
            $existing = $this->db->fetchOne(
                "SELECT id FROM system_settings WHERE setting_key = ?",
                [$key]
            );
            
            $data = [
                'setting_key' => $key,
                'setting_value' => is_array($value) ? json_encode($value) : (string)$value,
                'setting_type' => $type,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($description) $data['description'] = $description;
            
            if ($existing) {
                // Use direct query for update to avoid parameter binding issues
                $sql = "UPDATE system_settings SET setting_value = ?, setting_type = ?, updated_at = ?";
                $params = [$data['setting_value'], $data['setting_type'], $data['updated_at']];
                
                if ($description) {
                    $sql .= ", description = ?";
                    $params[] = $description;
                }
                
                $sql .= " WHERE setting_key = ?";
                $params[] = $key;
                
                $this->db->query($sql, $params);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('system_settings', $data);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error saving setting: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get all settings
     */
    public function getAllSettings() {
        try {
            $settings = $this->db->fetchAll("SELECT * FROM system_settings ORDER BY setting_key");
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting['setting_key']] = $this->getSetting($setting['setting_key']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error getting all settings: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Backup content to JSON files (for compatibility)
     */
    public function backupToJson() {
        try {
            $pages = $this->db->fetchAll("SELECT page_key, content FROM content_pages WHERE is_published = true");
            $backupDir = __DIR__ . '/../backups/' . date('Y-m-d_H-i-s');
            
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            foreach ($pages as $page) {
                $content = json_decode($page['content'], true);
                $filePath = $backupDir . '/' . $page['page_key'] . '.json';
                file_put_contents($filePath, json_encode($content, JSON_PRETTY_PRINT));
            }
            
            return $backupDir;
        } catch (Exception $e) {
            error_log("Error backing up to JSON: " . $e->getMessage());
            throw $e;
        }
    }
} 