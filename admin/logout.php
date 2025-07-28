<?php
/**
 * BitSync Group Admin Logout
 * Ends admin session and redirects to login
 */

session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit; 