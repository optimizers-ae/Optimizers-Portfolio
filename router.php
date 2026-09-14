<?php
declare(strict_types=1);

$rawUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($rawUri, PHP_URL_PATH) ?? '';

// Keep stale admin asset URLs out of front routing.
if (preg_match('~^/admin/assets(?:/|$)~', $path)) {
    http_response_code(404);
    exit;
}

$filePath = __DIR__ . $path;

// Serve existing files (images, css, js, fonts) directly
if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// Keep clean admin entry points on the admin shell instead of the public router.
if (preg_match('~^/admin(?:/(?:index|cases|services|settings))?/?$~', $path)) {
    require __DIR__ . '/admin/index.php';
    exit;
}

// Delegate everything else to index.php (which contains front-routing)
require __DIR__ . '/index.php';
