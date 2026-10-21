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

// Vercel serverless entry point. Keep the original application front controller.
require __DIR__ . '/../index.php';
