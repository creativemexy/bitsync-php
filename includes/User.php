<?php
/**
 * User Management Class for BitSync Group
 * Handles user authentication, roles, and permissions
 */

class User {
    private $db;
    private $userData;
    private $roles;
    private $permissions;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userData = null;
        $this->roles = [];
        $this->permissions = [];
    }
    
    /**
     * Authenticate user with username and password
     */
    public function authenticate($username, $password) {
        try {
            $user = $this->db->fetchOne(
                "SELECT id, username, email, password_hash, first_name, last_name, is_active, is_admin, last_login 
                 FROM users WHERE username = :username AND is_active = true",
                ['username' => $username]
            );
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                            $this->db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = :user_id', 
                ['user_id' => $user['id']]
            );
                
                $this->userData = $user;
                $this->loadUserRoles();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Load user by ID
     */
    public function loadById($userId) {
        try {
            $user = $this->db->fetchOne(
                "SELECT id, username, email, password_hash, first_name, last_name, is_active, is_admin, last_login 
                 FROM users WHERE id = :user_id",
                ['user_id' => $userId]
            );
            
            if ($user) {
                $this->userData = $user;
                $this->loadUserRoles();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Load user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Load user roles and permissions
     */
    private function loadUserRoles() {
        if (!$this->userData) return;
        
        try {
            $roles = $this->db->fetchAll(
                "SELECT r.id, r.name, r.description, r.permissions 
                 FROM roles r 
                 INNER JOIN user_roles ur ON r.id = ur.role_id 
                 WHERE ur.user_id = :user_id AND r.is_active = true",
                ['user_id' => $this->userData['id']]
            );
            
            $this->roles = $roles;
            $this->permissions = [];
            
            foreach ($roles as $role) {
                $rolePermissions = json_decode($role['permissions'], true) ?: [];
                $this->permissions = array_merge($this->permissions, $rolePermissions);
            }
            
            // Remove duplicates
            $this->permissions = array_unique($this->permissions);
            
        } catch (Exception $e) {
            error_log("Load user roles error: " . $e->getMessage());
        }
    }
    
    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission) {
        if (!$this->userData) return false;
        
        // Admin users have all permissions
        if ($this->userData['is_admin']) return true;
        
        // Check for wildcard permission
        if (in_array('*', $this->permissions)) return true;
        
        return in_array($permission, $this->permissions);
    }
    
    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission($permissions) {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get user data
     */
    public function getUserData() {
        return $this->userData;
    }
    
    /**
     * Get user roles
     */
    public function getRoles() {
        return $this->roles;
    }
    
    /**
     * Get user permissions
     */
    public function getPermissions() {
        return $this->permissions;
    }
    
    /**
     * Create new user
     */
    public function createUser($userData) {
        try {
            // Hash password
            $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            unset($userData['password']);
            
            // Insert user
            $userId = $this->db->insert('users', $userData);
            
            // Assign roles if provided
            if (!empty($userData['roles'])) {
                foreach ($userData['roles'] as $roleId) {
                    $this->db->insert('user_roles', [
                        'user_id' => $userId,
                        'role_id' => $roleId,
                        'assigned_by' => $this->userData['id'] ?? null
                    ]);
                }
            }
            
            return $userId;
        } catch (Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            throw new Exception("Failed to create user");
        }
    }
    
    /**
     * Update user
     */
    public function updateUser($userId, $userData) {
        try {
            // Hash password if provided
            if (!empty($userData['password'])) {
                $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
                unset($userData['password']);
            }
            
            // Update user
            $this->db->update('users', $userData, 'id = ?', [$userId]);
            
            // Update roles if provided
            if (isset($userData['roles'])) {
                // Remove existing roles
                $this->db->delete('user_roles', 'user_id = :user_id', ['user_id' => $userId]);
                
                // Assign new roles
                foreach ($userData['roles'] as $roleId) {
                    $this->db->insert('user_roles', [
                        'user_id' => $userId,
                        'role_id' => $roleId,
                        'assigned_by' => $this->userData['id'] ?? null
                    ]);
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            throw new Exception("Failed to update user");
        }
    }
    
    /**
     * Delete user
     */
    public function deleteUser($userId) {
        try {
            // Don't allow self-deletion
            if ($userId === $this->userData['id']) {
                throw new Exception("Cannot delete your own account");
            }
            
            $this->db->delete('users', 'id = :user_id', ['user_id' => $userId]);
            return true;
        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            throw new Exception("Failed to delete user");
        }
    }
    
    /**
     * Get all users
     */
    public function getAllUsers() {
        try {
            return $this->db->fetchAll(
                "SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.is_active, u.is_admin, u.last_login, u.created_at,
                        COALESCE(array_agg(DISTINCT r.name) FILTER (WHERE r.name IS NOT NULL), ARRAY[]::text[]) as roles
                 FROM users u
                 LEFT JOIN user_roles ur ON u.id = ur.user_id
                 LEFT JOIN roles r ON ur.role_id = r.id
                 GROUP BY u.id, u.username, u.email, u.first_name, u.last_name, u.is_active, u.is_admin, u.last_login, u.created_at
                 ORDER BY u.created_at DESC"
            );
        } catch (Exception $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all roles
     */
    public function getAllRoles() {
        try {
            return $this->db->fetchAll(
                "SELECT id, name, description, permissions, is_active, created_at 
                 FROM roles 
                 ORDER BY name"
            );
        } catch (Exception $e) {
            error_log("Get all roles error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeUserId = null) {
        try {
            $sql = "SELECT id FROM users WHERE username = :username";
            $params = ['username' => $username];
            
            if ($excludeUserId) {
                $sql .= " AND id != :exclude_user_id";
                $params['exclude_user_id'] = $excludeUserId;
            }
            
            $user = $this->db->fetchOne($sql, $params);
            return $user !== false;
        } catch (Exception $e) {
            error_log("Username exists check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeUserId = null) {
        try {
            $sql = "SELECT id FROM users WHERE email = :email";
            $params = ['email' => $email];
            
            if ($excludeUserId) {
                $sql .= " AND id != :exclude_user_id";
                $params['exclude_user_id'] = $excludeUserId;
            }
            
            $user = $this->db->fetchOne($sql, $params);
            return $user !== false;
        } catch (Exception $e) {
            error_log("Email exists check error: " . $e->getMessage());
            return false;
        }
    }
} 