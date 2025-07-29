<?php
/**
 * BitSync Search API
 * Handles search requests and returns JSON results
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        return false;
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, '"\'');
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

loadEnv(__DIR__ . '/.env');

require_once 'includes/Search.php';
require_once 'includes/Monitoring.php';

// Initialize monitoring
$monitoring = new Monitoring();
$monitoring->startRequest();

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
    } else {
        $input = $_GET;
    }
    
    $query = trim($input['q'] ?? $input['query'] ?? '');
    $filters = $input['filters'] ?? [];
    $page = max(1, intval($input['page'] ?? 1));
    $limit = min(50, max(1, intval($input['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    
    if (empty($query)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Search query is required',
            'results' => [],
            'total' => 0
        ]);
        $monitoring->endRequest();
        exit;
    }
    
    try {
        $search = new Search();
        $results = $search->search($query, $filters, $limit, $offset);
        
        // Log search for analytics
        $search->logSearch($query, $results['total'], $filters);
        
        // Track search performance
        $monitoring->trackPerformance('search_query', microtime(true) * 1000);
        
        // Add pagination info
        $results['pagination'] = [
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($results['total'] / $limit),
            'has_next' => $page < ceil($results['total'] / $limit),
            'has_prev' => $page > 1
        ];
        
        echo json_encode([
            'success' => true,
            'query' => $query,
            'filters' => $filters,
            'results' => $results['results'],
            'total' => $results['total'],
            'suggestions' => $results['suggestions'],
            'pagination' => $results['pagination']
        ]);
        
    } catch (Exception $e) {
        // Track error
        $monitoring->logError($e, [
            'query' => $query,
            'filters' => $filters
        ]);
        
        error_log("Search API error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Search failed. Please try again.',
            'results' => [],
            'total' => 0
        ]);
    }
    
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

$monitoring->endRequest(); 