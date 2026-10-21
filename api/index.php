<?php
// Temporary diagnostics. Remove these branches after the deployment issue is resolved.
// Some serverless PHP adapters do not populate $_GET consistently, so parse the
// raw query string as a fallback. No query value is used in a shell command.
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

require __DIR__ . '/../index.php';
