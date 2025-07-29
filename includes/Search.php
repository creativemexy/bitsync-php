<?php
/**
 * BitSync Advanced Search System
 * Provides comprehensive search across all content types
 */

require_once __DIR__ . '/Database.php';

class Search {
    private $db;
    private $results = [];
    private $totalResults = 0;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Perform a comprehensive search across all content
     */
    public function search($query, $filters = [], $limit = 20, $offset = 0) {
        $query = trim($query);
        if (empty($query)) {
            return ['results' => [], 'total' => 0, 'suggestions' => []];
        }
        
        $this->results = [];
        $this->totalResults = 0;
        
        // Search across different content types
        $this->searchPages($query, $filters);
        $this->searchServices($query, $filters);
        $this->searchBlogPosts($query, $filters);
        $this->searchCaseStudies($query, $filters);
        $this->searchJobOpenings($query, $filters);
        
        // Sort results by relevance
        $this->sortByRelevance($query);
        
        // Apply pagination
        $paginatedResults = array_slice($this->results, $offset, $limit);
        
        // Get search suggestions
        $suggestions = $this->getSearchSuggestions($query);
        
        return [
            'results' => $paginatedResults,
            'total' => $this->totalResults,
            'suggestions' => $suggestions,
            'query' => $query,
            'filters' => $filters
        ];
    }
    
    /**
     * Search content pages
     */
    private function searchPages($query, $filters) {
        try {
            $sql = "SELECT 
                        'page' as type,
                        page_key as id,
                        title,
                        description,
                        content,
                        'pages/' || page_key as url,
                        created_at,
                        updated_at,
                        0 as relevance_score
                    FROM content_pages 
                    WHERE is_published = true 
                    AND (
                        LOWER(title) LIKE LOWER(?) 
                        OR LOWER(description) LIKE LOWER(?) 
                        OR LOWER(content) LIKE LOWER(?)
                    )";
            
            $searchTerm = "%$query%";
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
            
            foreach ($results as $result) {
                $result['relevance_score'] = $this->calculateRelevance($query, $result);
                $result['excerpt'] = $this->generateExcerpt($query, $result['content']);
                $this->results[] = $result;
                $this->totalResults++;
            }
        } catch (Exception $e) {
            error_log("Search pages error: " . $e->getMessage());
        }
    }
    
    /**
     * Search services
     */
    private function searchServices($query, $filters) {
        try {
            $sql = "SELECT 
                        'service' as type,
                        id,
                        title,
                        description,
                        content,
                        'services/' || slug as url,
                        category,
                        created_at,
                        updated_at,
                        0 as relevance_score
                    FROM services 
                    WHERE is_active = true 
                    AND (
                        LOWER(title) LIKE LOWER(?) 
                        OR LOWER(description) LIKE LOWER(?) 
                        OR LOWER(content) LIKE LOWER(?)
                        OR LOWER(category) LIKE LOWER(?)
                    )";
            
            $searchTerm = "%$query%";
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            
            foreach ($results as $result) {
                $result['relevance_score'] = $this->calculateRelevance($query, $result);
                $result['excerpt'] = $this->generateExcerpt($query, $result['content']);
                $this->results[] = $result;
                $this->totalResults++;
            }
        } catch (Exception $e) {
            error_log("Search services error: " . $e->getMessage());
        }
    }
    
    /**
     * Search blog posts
     */
    private function searchBlogPosts($query, $filters) {
        try {
            $sql = "SELECT 
                        'blog' as type,
                        id,
                        title,
                        excerpt,
                        content,
                        'blog/' || slug as url,
                        author,
                        tags,
                        created_at,
                        updated_at,
                        0 as relevance_score
                    FROM blog_posts 
                    WHERE is_published = true 
                    AND (
                        LOWER(title) LIKE LOWER(?) 
                        OR LOWER(excerpt) LIKE LOWER(?) 
                        OR LOWER(content) LIKE LOWER(?)
                        OR LOWER(tags) LIKE LOWER(?)
                    )";
            
            $searchTerm = "%$query%";
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            
            foreach ($results as $result) {
                $result['relevance_score'] = $this->calculateRelevance($query, $result);
                $result['excerpt'] = $this->generateExcerpt($query, $result['content']);
                $this->results[] = $result;
                $this->totalResults++;
            }
        } catch (Exception $e) {
            error_log("Search blog posts error: " . $e->getMessage());
        }
    }
    
    /**
     * Search case studies
     */
    private function searchCaseStudies($query, $filters) {
        try {
            $sql = "SELECT 
                        'case_study' as type,
                        id,
                        title,
                        description,
                        content,
                        'case-studies/' || slug as url,
                        client_name,
                        industry,
                        technologies,
                        created_at,
                        updated_at,
                        0 as relevance_score
                    FROM case_studies 
                    WHERE is_published = true 
                    AND (
                        LOWER(title) LIKE LOWER(?) 
                        OR LOWER(description) LIKE LOWER(?) 
                        OR LOWER(content) LIKE LOWER(?)
                        OR LOWER(client_name) LIKE LOWER(?)
                        OR LOWER(industry) LIKE LOWER(?)
                        OR LOWER(technologies) LIKE LOWER(?)
                    )";
            
            $searchTerm = "%$query%";
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            
            foreach ($results as $result) {
                $result['relevance_score'] = $this->calculateRelevance($query, $result);
                $result['excerpt'] = $this->generateExcerpt($query, $result['content']);
                $this->results[] = $result;
                $this->totalResults++;
            }
        } catch (Exception $e) {
            error_log("Search case studies error: " . $e->getMessage());
        }
    }
    
    /**
     * Search job openings
     */
    private function searchJobOpenings($query, $filters) {
        try {
            $sql = "SELECT 
                        'job' as type,
                        id,
                        title,
                        description,
                        requirements,
                        'careers/' || slug as url,
                        department,
                        location,
                        employment_type,
                        created_at,
                        updated_at,
                        0 as relevance_score
                    FROM job_openings 
                    WHERE is_active = true 
                    AND (
                        LOWER(title) LIKE LOWER(?) 
                        OR LOWER(description) LIKE LOWER(?) 
                        OR LOWER(requirements) LIKE LOWER(?)
                        OR LOWER(department) LIKE LOWER(?)
                        OR LOWER(location) LIKE LOWER(?)
                    )";
            
            $searchTerm = "%$query%";
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            
            foreach ($results as $result) {
                $result['relevance_score'] = $this->calculateRelevance($query, $result);
                $result['excerpt'] = $this->generateExcerpt($query, $result['description']);
                $this->results[] = $result;
                $this->totalResults++;
            }
        } catch (Exception $e) {
            error_log("Search job openings error: " . $e->getMessage());
        }
    }
    
    /**
     * Calculate relevance score for a result
     */
    private function calculateRelevance($query, $result) {
        $score = 0;
        $query = strtolower($query);
        $words = explode(' ', $query);
        
        // Title match (highest weight)
        if (isset($result['title'])) {
            $title = strtolower($result['title']);
            foreach ($words as $word) {
                if (strpos($title, $word) !== false) {
                    $score += 10;
                }
            }
        }
        
        // Exact phrase match
        if (isset($result['title']) && strpos(strtolower($result['title']), $query) !== false) {
            $score += 20;
        }
        
        // Description match
        if (isset($result['description'])) {
            $description = strtolower($result['description']);
            foreach ($words as $word) {
                if (strpos($description, $word) !== false) {
                    $score += 5;
                }
            }
        }
        
        // Content match
        if (isset($result['content'])) {
            $content = strtolower($result['content']);
            foreach ($words as $word) {
                $count = substr_count($content, $word);
                $score += $count * 2;
            }
        }
        
        // Category/type match
        if (isset($result['category']) && strpos(strtolower($result['category']), $query) !== false) {
            $score += 8;
        }
        
        // Recent content bonus
        if (isset($result['created_at'])) {
            $created = strtotime($result['created_at']);
            $daysOld = (time() - $created) / (60 * 60 * 24);
            if ($daysOld < 30) {
                $score += 3;
            } elseif ($daysOld < 90) {
                $score += 1;
            }
        }
        
        return $score;
    }
    
    /**
     * Generate excerpt with highlighted search terms
     */
    private function generateExcerpt($query, $content, $length = 200) {
        if (empty($content)) {
            return '';
        }
        
        // Find the position of the first search term
        $query = strtolower($query);
        $content_lower = strtolower($content);
        $words = explode(' ', $query);
        
        $position = -1;
        foreach ($words as $word) {
            $pos = strpos($content_lower, $word);
            if ($pos !== false && ($position === -1 || $pos < $position)) {
                $position = $pos;
            }
        }
        
        if ($position === -1) {
            // No search terms found, take from beginning
            $excerpt = substr($content, 0, $length);
        } else {
            // Start from search term position
            $start = max(0, $position - 50);
            $excerpt = substr($content, $start, $length);
        }
        
        // Add ellipsis if truncated
        if (strlen($content) > $length) {
            $excerpt .= '...';
        }
        
        // Highlight search terms
        foreach ($words as $word) {
            $excerpt = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $excerpt);
        }
        
        return $excerpt;
    }
    
    /**
     * Sort results by relevance score
     */
    private function sortByRelevance($query) {
        usort($this->results, function($a, $b) {
            return $b['relevance_score'] - $a['relevance_score'];
        });
    }
    
    /**
     * Get search suggestions based on popular searches and content
     */
    private function getSearchSuggestions($query) {
        $suggestions = [];
        
        try {
            // Get popular search terms from search logs
            $sql = "SELECT search_term, COUNT(*) as count 
                    FROM search_logs 
                    WHERE search_term LIKE ? 
                    GROUP BY search_term 
                    ORDER BY count DESC 
                    LIMIT 5";
            
            $results = $this->db->fetchAll($sql, ["%$query%"]);
            foreach ($results as $result) {
                $suggestions[] = $result['search_term'];
            }
            
            // Get suggestions from content titles
            $sql = "SELECT title FROM content_pages 
                    WHERE LOWER(title) LIKE LOWER(?) 
                    AND is_published = true 
                    LIMIT 3";
            
            $results = $this->db->fetchAll($sql, ["%$query%"]);
            foreach ($results as $result) {
                $suggestions[] = $result['title'];
            }
            
            // Get service categories
            $sql = "SELECT DISTINCT category FROM services 
                    WHERE LOWER(category) LIKE LOWER(?) 
                    AND is_active = true 
                    LIMIT 3";
            
            $results = $this->db->fetchAll($sql, ["%$query%"]);
            foreach ($results as $result) {
                $suggestions[] = $result['category'];
            }
            
        } catch (Exception $e) {
            error_log("Get search suggestions error: " . $e->getMessage());
        }
        
        return array_unique(array_slice($suggestions, 0, 10));
    }
    
    /**
     * Log search query for analytics
     */
    public function logSearch($query, $resultsCount, $filters = []) {
        try {
            $this->db->insert('search_logs', [
                'search_term' => $query,
                'results_count' => $resultsCount,
                'filters' => json_encode($filters),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'searched_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Failed to log search: " . $e->getMessage());
        }
    }
    
    /**
     * Get search statistics
     */
    public function getSearchStats($period = '24h') {
        try {
            $interval = $this->getInterval($period);
            
            $sql = "SELECT 
                        COUNT(*) as total_searches,
                        COUNT(DISTINCT search_term) as unique_terms,
                        AVG(results_count) as avg_results,
                        COUNT(CASE WHEN results_count = 0 THEN 1 END) as zero_results
                    FROM search_logs 
                    WHERE searched_at >= NOW() - INTERVAL ?";
            
            $stats = $this->db->fetchOne($sql, [$interval]);
            
            // Get popular search terms
            $sql = "SELECT search_term, COUNT(*) as count 
                    FROM search_logs 
                    WHERE searched_at >= NOW() - INTERVAL ? 
                    GROUP BY search_term 
                    ORDER BY count DESC 
                    LIMIT 10";
            
            $popularTerms = $this->db->fetchAll($sql, [$interval]);
            
            return [
                'stats' => $stats,
                'popular_terms' => $popularTerms
            ];
            
        } catch (Exception $e) {
            error_log("Get search stats error: " . $e->getMessage());
            return ['stats' => [], 'popular_terms' => []];
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
    
    private function getInterval($period) {
        $intervals = [
            '1h' => '1 hour',
            '24h' => '24 hours',
            '7d' => '7 days',
            '30d' => '30 days'
        ];
        return $intervals[$period] ?? '24 hours';
    }
} 