<?php
/**
 * Development router for PHP built-in web server.
 *
 * Usage: php -S localhost:8080 dev_router.php
 * Serves static files from the project root and routes everything
 * else through the CodeIgniter front controller (index.php).
 *
 * NOTE: This file is only for local development (php -S). Remove it
 * from a production deployment.
 */

$root = 'C:/xampp/htdocs/tk25';

if (PHP_SAPI !== 'cli-server') {
    return false;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the requested path is an existing file, serve it directly.
$requested = $root . '/' . ltrim($uri, '/');
if ($uri !== '/' && is_file($requested)) {
    return false;
}

// Otherwise, hand off to the front controller.
require $root . '/index.php';
