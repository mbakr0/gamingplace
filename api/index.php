<?php
// Temporary, non-sensitive runtime diagnostic. Remove after deployment is verified.
if (isset($_GET['__health']) && $_GET['__health'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'php' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'mysqli' => extension_loaded('mysqli'),
        'curl' => extension_loaded('curl'),
        'system_dir' => is_dir(__DIR__ . '/../system'),
        'application_dir' => is_dir(__DIR__ . '/../application'),
        'controller_file' => is_file(__DIR__ . '/../application/controllers/Products.php')
    ));
    exit;
}

// Log entry before loading CodeIgniter, so we can distinguish a Vercel function
// startup issue from a failure inside the application bootstrap.
$requestId = $_SERVER['HTTP_X_VERCEL_ID'] ?? $_SERVER['HTTP_X_REQUEST_ID'] ?? 'unavailable';
error_log('[GamingPlace] ' . json_encode(array(
    'level' => 'entrypoint',
    'request_id' => $requestId,
    'time' => gmdate('c'),
    'message' => 'Vercel PHP entrypoint reached',
    'php_version' => PHP_VERSION,
    'sapi' => PHP_SAPI,
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'uri' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
    'front_controller_exists' => is_file(__DIR__ . '/../index.php')
), JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));

require __DIR__ . '/../index.php';
