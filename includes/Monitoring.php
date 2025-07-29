<?php
/**
 * BitSync Monitoring System
 * Tracks performance, errors, user analytics, and system health
 */

require_once __DIR__ . '/Database.php';

class Monitoring {
    private $db;
    private $startTime;
    private $requestId;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->startTime = microtime(true);
        $this->requestId = uniqid('req_', true);
    }
    
    /**
     * Start monitoring a request
     */
    public function startRequest() {
        $this->logRequest([
            'request_id' => $this->requestId,
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'started_at' => date('Y-m-d H:i:s'),
            'memory_start' => memory_get_usage()
        ]);
    }
    
    /**
     * End monitoring a request
     */
    public function endRequest() {
        $endTime = microtime(true);
        $duration = ($endTime - $this->startTime) * 1000; // Convert to milliseconds
        $memoryEnd = memory_get_usage();
        $memoryPeak = memory_get_peak_usage();
        
        $this->updateRequest([
            'request_id' => $this->requestId,
            'duration_ms' => round($duration, 2),
            'memory_used' => $memoryEnd - $this->getRequestMemoryStart(),
            'memory_peak' => $memoryPeak,
            'ended_at' => date('Y-m-d H:i:s'),
            'status_code' => http_response_code()
        ]);
    }
    
    /**
     * Log an error
     */
    public function logError($error, $context = []) {
        try {
            $this->db->insert('system_errors', [
                'request_id' => $this->requestId,
                'error_type' => get_class($error),
                'error_message' => $error->getMessage(),
                'error_file' => $error->getFile(),
                'error_line' => $error->getLine(),
                'error_trace' => $error->getTraceAsString(),
                'context' => json_encode($context),
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip_address' => $this->getClientIP(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Failed to log error to database: " . $e->getMessage());
        }
    }
    
    /**
     * Track page view
     */
    public function trackPageView($page, $title = '') {
        try {
            $this->db->insert('page_views', [
                'request_id' => $this->requestId,
                'page' => $page,
                'title' => $title,
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'referer' => $_SERVER['HTTP_REFERER'] ?? '',
                'session_id' => session_id() ?: null,
                'viewed_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Failed to track page view: " . $e->getMessage());
        }
    }
    
    /**
     * Track form submission
     */
    public function trackFormSubmission($formType, $success, $data = []) {
        try {
            $this->db->insert('form_submissions', [
                'request_id' => $this->requestId,
                'form_type' => $formType,
                'success' => $success,
                'data' => json_encode($data),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'submitted_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Failed to track form submission: " . $e->getMessage());
        }
    }
    
    /**
     * Track performance metric
     */
    public function trackPerformance($metric, $value, $unit = 'ms') {
        try {
            $this->db->insert('performance_metrics', [
                'request_id' => $this->requestId,
                'metric_name' => $metric,
                'metric_value' => $value,
                'metric_unit' => $unit,
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'recorded_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Failed to track performance metric: " . $e->getMessage());
        }
    }
    
    /**
     * Get system health status
     */
    public function getSystemHealth() {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'disk_space' => $this->checkDiskSpace(),
            'memory_usage' => $this->checkMemoryUsage(),
            'uptime' => $this->getUptime(),
            'last_error' => $this->getLastError(),
            'active_users' => $this->getActiveUsers()
        ];
        
        return $health;
    }
    
    /**
     * Get analytics data
     */
    public function getAnalytics($period = '24h') {
        $analytics = [
            'page_views' => $this->getPageViews($period),
            'unique_visitors' => $this->getUniqueVisitors($period),
            'form_submissions' => $this->getFormSubmissions($period),
            'errors' => $this->getErrors($period),
            'performance' => $this->getPerformanceMetrics($period),
            'popular_pages' => $this->getPopularPages($period)
        ];
        
        return $analytics;
    }
    
    /**
     * Check database health
     */
    private function checkDatabaseHealth() {
        try {
            $start = microtime(true);
            $this->db->query("SELECT 1");
            $duration = (microtime(true) - $start) * 1000;
            
            return [
                'status' => 'healthy',
                'response_time' => round($duration, 2),
                'connected' => true
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'connected' => false
            ];
        }
    }
    
    /**
     * Check disk space
     */
    private function checkDiskSpace() {
        $total = disk_total_space(__DIR__);
        $free = disk_free_space(__DIR__);
        $used = $total - $free;
        $percentage = ($used / $total) * 100;
        
        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round($percentage, 2)
        ];
    }
    
    /**
     * Check memory usage
     */
    private function checkMemoryUsage() {
        $memory = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);
        $limit = ini_get('memory_limit');
        
        return [
            'current' => $this->formatBytes($memory),
            'peak' => $this->formatBytes($peak),
            'limit' => $limit
        ];
    }
    
    /**
     * Get system uptime
     */
    private function getUptime() {
        $uptime = shell_exec('uptime -p 2>/dev/null') ?: 'Unknown';
        return trim($uptime);
    }
    
    /**
     * Get last error
     */
    private function getLastError() {
        try {
            $error = $this->db->fetchOne(
                "SELECT * FROM system_errors ORDER BY created_at DESC LIMIT 1"
            );
            return $error;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get active users (last 15 minutes)
     */
    private function getActiveUsers() {
        try {
            $result = $this->db->fetchOne(
                "SELECT COUNT(DISTINCT ip_address) as count FROM page_views WHERE viewed_at >= NOW() - INTERVAL '15 minutes'"
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get page views for period
     */
    private function getPageViews($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM page_views WHERE viewed_at >= NOW() - INTERVAL ?",
                [$interval]
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get unique visitors for period
     */
    private function getUniqueVisitors($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchOne(
                "SELECT COUNT(DISTINCT ip_address) as count FROM page_views WHERE viewed_at >= NOW() - INTERVAL ?",
                [$interval]
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get form submissions for period
     */
    private function getFormSubmissions($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM form_submissions WHERE submitted_at >= NOW() - INTERVAL ?",
                [$interval]
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get errors for period
     */
    private function getErrors($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM system_errors WHERE created_at >= NOW() - INTERVAL ?",
                [$interval]
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get performance metrics for period
     */
    private function getPerformanceMetrics($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchAll(
                "SELECT metric_name, AVG(metric_value) as avg_value, MAX(metric_value) as max_value FROM performance_metrics WHERE recorded_at >= NOW() - INTERVAL ? GROUP BY metric_name",
                [$interval]
            );
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get popular pages for period
     */
    private function getPopularPages($period) {
        try {
            $interval = $this->getInterval($period);
            $result = $this->db->fetchAll(
                "SELECT page, COUNT(*) as views FROM page_views WHERE viewed_at >= NOW() - INTERVAL ? GROUP BY page ORDER BY views DESC LIMIT 10",
                [$interval]
            );
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Helper methods
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getInterval($period) {
        $intervals = [
            '1h' => '1 hour',
            '24h' => '24 hours',
            '7d' => '7 days',
            '30d' => '30 days'
        ];
        return $intervals[$period] ?? '24 hours';
    }
    
    private function logRequest($data) {
        try {
            $this->db->insert('request_logs', $data);
        } catch (Exception $e) {
            error_log("Failed to log request: " . $e->getMessage());
        }
    }
    
    private function updateRequest($data) {
        try {
            $this->db->query(
                "UPDATE request_logs SET duration_ms = ?, memory_used = ?, memory_peak = ?, ended_at = ?, status_code = ? WHERE request_id = ?",
                [$data['duration_ms'], $data['memory_used'], $data['memory_peak'], $data['ended_at'], $data['status_code'], $data['request_id']]
            );
        } catch (Exception $e) {
            error_log("Failed to update request: " . $e->getMessage());
        }
    }
    
    private function getRequestMemoryStart() {
        try {
            $result = $this->db->fetchOne(
                "SELECT memory_start FROM request_logs WHERE request_id = ?",
                [$this->requestId]
            );
            return $result['memory_start'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
} 