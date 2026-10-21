<?php
/**
 * Serve public assets through the Vercel PHP runtime when the deployment's
 * filesystem routing does not return the original static file.
 *
 * Only files inside the repository's /assets directory are eligible.
 */
$assetRoot = realpath(__DIR__ . '/../assets');
$requestedPath = isset($_GET['path']) ? (string) $_GET['path'] : '';
$requestedPath = str_replace('\\\\', '/', $requestedPath);

if ($assetRoot === false || $requestedPath === '' || strpos($requestedPath, "\0") !== false) {
    http_response_code(404);
    exit('Asset not found');
}

// Reject traversal segments before resolving the path.
$segments = explode('/', $requestedPath);
foreach ($segments as $segment) {
    if ($segment === '.' || $segment === '..' || $segment === '') {
        http_response_code(404);
        exit('Asset not found');
    }
}

$filePath = realpath($assetRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $requestedPath));
$assetRootPrefix = rtrim($assetRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if ($filePath === false || strpos($filePath, $assetRootPrefix) !== 0 || !is_file($filePath) || !is_readable($filePath)) {
    http_response_code(404);
    exit('Asset not found');
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentTypes = array(
    'css' => 'text/css; charset=utf-8',
    'js' => 'application/javascript; charset=utf-8',
    'mjs' => 'application/javascript; charset=utf-8',
    'json' => 'application/json; charset=utf-8',
    'svg' => 'image/svg+xml',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'otf' => 'font/otf',
    'map' => 'application/json; charset=utf-8'
);

if (!isset($contentTypes[$extension])) {
    http_response_code(404);
    exit('Asset not found');
}

header('Content-Type: ' . $contentTypes[$extension]);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
