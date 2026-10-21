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

// Run PHP's own syntax checker in a subprocess if the deployment permits it.
// Paths are fixed here; no request value is used in a shell command.
if (isset($_GET['__syntax']) && $_GET['__syntax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    $files = array(
        'front_controller' => __DIR__ . '/../index.php',
        'ci_core' => __DIR__ . '/../system/core/CodeIgniter.php',
        'ci_common' => __DIR__ . '/../system/core/Common.php',
        'app_constants' => __DIR__ . '/../application/config/constants.php'
    );
    $result = array(
        'php' => PHP_VERSION,
        'php_binary' => PHP_BINARY,
        'shell_exec_available' => function_exists('shell_exec'),
        'checks' => array()
    );

    if (function_exists('shell_exec')) {
        foreach ($files as $key => $file) {
            if (!is_file($file)) {
                $result['checks'][$key] = array('exists' => false);
                continue;
            }
            $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1';
            $output = shell_exec($command);
            $result['checks'][$key] = array(
                'exists' => true,
                'output' => is_string($output) ? trim($output) : null
            );
        }
    } else {
        $result['note'] = 'shell_exec is disabled; syntax checks could not run.';
    }

    echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
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
