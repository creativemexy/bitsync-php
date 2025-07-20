<?php
session_start();

// Simple routing system
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Remove .php extension if present
$path = str_replace('.php', '', $path);

// Define routes
$routes = [
    '' => 'pages/index.php',
    'about' => 'pages/about.php',
    'services' => 'pages/services.php',
    'solutions' => 'pages/solutions.php',
    'contact' => 'pages/contact.php'
];

// Check if route exists
if (array_key_exists($path, $routes)) {
    $page_file = $routes[$path];
} else {
    $page_file = 'pages/404.php';
}

// Include the layout
include 'includes/layout.php';
?> 