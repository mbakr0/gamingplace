<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$databaseHost = getenv('DB_HOST') ?: 'localhost';
$databaseSslSetting = strtolower((string) getenv('DB_SSL'));
$databaseIsLocal = in_array(strtolower($databaseHost), array('localhost', '127.0.0.1', '::1'), TRUE);
$databaseUseSsl = ($databaseSslSetting === '1') || (!$databaseIsLocal && $databaseSslSetting !== '0');
$databaseEncrypt = FALSE;

if ($databaseUseSsl) {
    $databaseEncrypt = array('ssl_verify' => TRUE);
    $sslOptions = array(
        'DB_SSL_CA' => 'ssl_ca',
        'DB_SSL_CERT' => 'ssl_cert',
        'DB_SSL_KEY' => 'ssl_key'
    );

    foreach ($sslOptions as $environmentName => $configName) {
        $sslValue = getenv($environmentName);
        if ($sslValue !== FALSE && $sslValue !== '') {
            $databaseEncrypt[$configName] = $sslValue;
        }
    }
}

$db['default'] = array(
    'dsn'        => '',
    'hostname'   => $databaseHost,
    'port'       => (int) (getenv('DB_PORT') ?: ($databaseIsLocal ? 3306 : 4000)),
    'username'   => getenv('DB_USER') ?: 'root',
    'password'   => getenv('DB_PASS') ?: '',
    'database'   => getenv('DB_NAME') ?: 'gamingplace',
    'dbdriver'   => 'mysqli',
    'dbprefix'   => '',
    'pconnect'   => FALSE,
    'db_debug'   => (ENVIRONMENT !== 'production'),
    'cache_on'   => FALSE,
    'cachedir'   => '',
    'char_set'   => 'utf8mb4',
    'dbcollat'   => 'utf8mb4_unicode_ci',
    'swap_pre'   => '',
    'encrypt'    => $databaseEncrypt,
    'compress'   => FALSE,
    'stricton'   => TRUE,
    'failover'   => array(),
    'save_queries'=> FALSE
);
