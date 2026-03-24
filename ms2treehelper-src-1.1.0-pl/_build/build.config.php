<?php
/**
 * Build config for ms2treehelper.
 *
 * 1) Try to find and include the site's config.core.php.
 * 2) If not found, fall back to MODX core autodetection.
 * 3) Define missing path/url constants needed for package build.
 */

$buildDir = dirname(__FILE__);
$configCore = null;

$candidates = array(
    dirname($buildDir, 2) . '/config.core.php',
    dirname($buildDir, 3) . '/config.core.php',
    dirname($buildDir, 4) . '/config.core.php',
    dirname($buildDir, 5) . '/config.core.php',
);

foreach ($candidates as $candidate) {
    if (file_exists($candidate)) {
        $configCore = realpath($candidate);
        break;
    }
}

if ($configCore) {
    require_once $configCore;
}

if (!defined('MODX_CORE_PATH')) {
    $coreCandidates = array(
        dirname($buildDir, 2) . '/core/',
        dirname($buildDir, 3) . '/core/',
        dirname($buildDir, 4) . '/core/',
        dirname($buildDir, 5) . '/core/',
    );

    foreach ($coreCandidates as $candidate) {
        if (is_dir($candidate) && file_exists($candidate . 'model/modx/modx.class.php')) {
            define('MODX_CORE_PATH', realpath($candidate) . '/');
            break;
        }
    }
}

if (!defined('MODX_CORE_PATH')) {
    die('Could not detect MODX_CORE_PATH. Edit _build/build.config.php manually.');
}

if (!defined('MODX_CONFIG_KEY')) {
    define('MODX_CONFIG_KEY', 'config');
}

$basePath = null;
if ($configCore) {
    $basePath = dirname($configCore) . '/';
} elseif (!empty($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
    $basePath = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/\\') . '/';
} else {
    $basePath = dirname(rtrim(MODX_CORE_PATH, '/\\')) . '/';
}

if (!defined('MODX_BASE_PATH')) {
    define('MODX_BASE_PATH', $basePath);
}
if (!defined('MODX_CONNECTORS_PATH')) {
    define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
}
if (!defined('MODX_MANAGER_PATH')) {
    define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
}
if (!defined('MODX_ASSETS_PATH')) {
    define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');
}

if (!defined('MODX_BASE_URL')) {
    $baseUrl = '/';
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $documentRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']), '/');
        $normalizedBasePath = rtrim(str_replace('\\', '/', realpath(MODX_BASE_PATH) ?: MODX_BASE_PATH), '/');
        if ($documentRoot && strpos($normalizedBasePath, $documentRoot) === 0) {
            $relative = substr($normalizedBasePath, strlen($documentRoot));
            $relative = trim($relative, '/');
            $baseUrl = '/' . ($relative !== '' ? $relative . '/' : '');
        }
    }
    define('MODX_BASE_URL', $baseUrl);
}
if (!defined('MODX_MANAGER_URL')) {
    define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
}
if (!defined('MODX_ASSETS_URL')) {
    define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');
}
if (!defined('MODX_CONNECTORS_URL')) {
    define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
}
