<?php
/**
 * Database Configuration for BitSync Group
 * CockroachDB Cloud Configuration
 */

return [
    'cockroachdb' => [
        'host' => $_ENV['DB_HOST'] ?? 'tangy-spirit-7966.jxf.cockroachlabs.cloud',
        'port' => $_ENV['DB_PORT'] ?? '26257',
        'database' => $_ENV['DB_NAME'] ?? 'ken',
        'username' => $_ENV['DB_USER'] ?? 'demilade',
        'password' => $_ENV['DB_PASSWORD'] ?? 'd66tiYy5ssCYTlX_r70lZA',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
        'ssl' => [
            'enabled' => true, // Enable SSL for cloud connection
            'ca' => $_ENV['DB_SSL_CA'] ?? null,
            'cert' => $_ENV['DB_SSL_CERT'] ?? null,
            'key' => $_ENV['DB_SSL_KEY'] ?? null,
        ]
    ],
    
    // Connection string for PDO
    'dsn' => function() {
        $config = require __DIR__ . '/database.php';
        $db = $config['cockroachdb'];
        
        $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['database']}";
        
        if ($db['ssl']['enabled']) {
            $dsn .= ";sslmode=require";
        }
        
        return $dsn;
    }
]; 