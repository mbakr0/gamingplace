<?php
// Temporary diagnostics. Remove after the deployment issue is resolved.
// Debug output is opt-in via /?debug=1 and /api/index.php?debug=1.
$rawQuery = array();
parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $rawQuery);
$diagnosticQuery = array_merge($rawQuery, is_array($_GET ?? null) ? $_GET : array());

if (($diagnosticQuery['__health'] ?? null) === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'diagnostic' => 'health',
        'query_string_seen' => (string) ($_SERVER['QUERY_STRING'] ?? ''),
        'php' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'mysqli' => extension_loaded('mysqli'),
        'curl' => extension_loaded('curl'),
        'system_dir' => is_dir(__DIR__ . '/../system'),
        'application_dir' => is_dir(__DIR__ . '/../application'),
        'controller_file' => is_file(__DIR__ . '/../application/controllers/Products.php')
    ), JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    exit;
}

if (($diagnosticQuery['__syntax'] ?? null) === '1') {
    header('Content-Type: application/json; charset=utf-8');
    $files = array(
        'front_controller' => __DIR__ . '/../index.php',
        'ci_core' => __DIR__ . '/../system/core/CodeIgniter.php',
        'ci_common' => __DIR__ . '/../system/core/Common.php',
        'app_constants' => __DIR__ . '/../application/config/constants.php'
    );
    $result = array(
        'diagnostic' => 'syntax',
        'query_string_seen' => (string) ($_SERVER['QUERY_STRING'] ?? ''),
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

$debugMode = ($diagnosticQuery['debug'] ?? null) === '1';
$diagnosticLog = array();
$diagnosticLog[] = array('stage' => 'api_entrypoint_reached', 'php' => PHP_VERSION, 'sapi' => PHP_SAPI);
$diagnosticLog[] = array('stage' => 'root_index_exists', 'result' => is_file(__DIR__ . '/../index.php'));
$diagnosticLog[] = array('stage' => 'system_core_exists', 'result' => is_file(__DIR__ . '/../system/core/CodeIgniter.php'));
$diagnosticLog[] = array('stage' => 'application_config_exists', 'result' => is_file(__DIR__ . '/../application/config/config.php'));

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
    'query_string' => (string) ($_SERVER['QUERY_STRING'] ?? ''),
    'front_controller_exists' => is_file(__DIR__ . '/../index.php')
), JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));

if ($debugMode) {
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>GamingPlace temporary diagnostics</title><body style="font:16px/1.5 system-ui,sans-serif;background:#10151d;color:#e8eef7;padding:24px">';
    echo '<h1>GamingPlace bootstrap diagnostics</h1><p>This temporary report is enabled by <code>?debug=1</code>.</p>';
    echo '<h2>Checkpoints</h2><pre style="white-space:pre-wrap;background:#1b2430;padding:16px;border-radius:8px">' .
        htmlspecialchars(json_encode($diagnosticLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
    echo '<h2>CodeIgniter bootstrap</h2><pre style="white-space:pre-wrap;background:#1b2430;padding:16px;border-radius:8px">';
    try {
        require __DIR__ . '/../index.php';
        echo "\nRoot index returned without throwing an exception.\n";
    } catch (Throwable $exception) {
        http_response_code(500);
        echo "BOOTSTRAP EXCEPTION\n";
        echo 'Type: ' . htmlspecialchars(get_class($exception), ENT_QUOTES, 'UTF-8') . "\n";
        echo 'Message: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
        echo 'File: ' . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') . "\n";
        echo 'Line: ' . (int) $exception->getLine() . "\n";
        error_log('[GamingPlace] debug_caught_exception ' . json_encode(array(
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ), JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
    }
    echo '</pre><p>Disable diagnostics by removing <code>?debug=1</code>.</p></body></html>';
    exit;
}

require __DIR__ . '/../index.php';
