<?php
/**
 * ms2treehelper transport package builder
 */
$mtime = microtime(true);
set_time_limit(0);

if (!function_exists('rrmdir')) {
    function rrmdir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}



if (!function_exists('ms2th_normalize_transport_zip')) {
    function ms2th_normalize_transport_zip($zipPath, $signature)
    {
        if (!class_exists('ZipArchive') || !file_exists($zipPath)) {
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (empty($stat['name']) || substr($stat['name'], -1) === '/') {
                continue;
            }

            $name = $stat['name'];
            if (substr($name, -8) !== '.vehicle' && substr($name, -12) !== 'manifest.php') {
                continue;
            }

            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }

            $content = preg_replace(
                '#ms2treehelper-\d+\.\d+\.\d+-pl/modCategory/#',
                $signature . '/modCategory/',
                $content
            );
            $content = preg_replace(
                '#ms2treehelper-\d+\.\d+\.\d+-pl/modNamespace/#',
                $signature . '/modNamespace/',
                $content
            );

            $zip->deleteIndex($i);
            $zip->addFromString($name, $content);
        }

        $zip->close();
    }
}

/* package info */
define('PKG_NAME', 'ms2TreeHelper');
define('PKG_NAME_LOWER', 'ms2treehelper');
define('PKG_VERSION', '1.1.0');
define('PKG_RELEASE', 'pl');
define('PKG_SIGNATURE', PKG_NAME_LOWER . '-' . PKG_VERSION . '-' . PKG_RELEASE);

$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'elements' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/',
);
unset($root);

require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

$builder = new modPackageBuilder($modx);

/* Clean previous workspaces and zips for this package to avoid stale vehicle paths */
$packagePattern = MODX_CORE_PATH . 'packages/' . PKG_NAME_LOWER . '-*';
foreach (glob($packagePattern) as $oldPackageItem) {
    if (is_dir($oldPackageItem)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Removing old package workspace: ' . $oldPackageItem);
        rrmdir($oldPackageItem);
    } elseif (is_file($oldPackageItem)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Removing old package file: ' . $oldPackageItem);
        @unlink($oldPackageItem);
    }
}

$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');

$category = $modx->newObject('modCategory');
$category->set('category', PKG_NAME);
$category->set('rank', 0);

$plugins = require $sources['data'] . 'transport.plugins.php';
if (is_array($plugins) && !empty($plugins)) {
    $category->addMany($plugins, 'Plugins');
}

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Plugins' => array(
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
    ),
);

$vehicle = $builder->createVehicle($category, $attr);

$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'setup.resolver.php',
));

$builder->putVehicle($vehicle);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_exists($sources['docs'] . 'license.txt') ? file_get_contents($sources['docs'] . 'license.txt') : '',
    'readme' => file_exists($sources['docs'] . 'readme.txt') ? file_get_contents($sources['docs'] . 'readme.txt') : '',
    'changelog' => file_exists($sources['docs'] . 'changelog.txt') ? file_get_contents($sources['docs'] . 'changelog.txt') : '',
    'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php',
    ),
));

$modx->log(modX::LOG_LEVEL_INFO, 'Packing transport package...');
$builder->pack();


$packageZip = MODX_CORE_PATH . 'packages/' . PKG_SIGNATURE . '.transport.zip';
ms2th_normalize_transport_zip($packageZip, PKG_SIGNATURE);
$modx->log(modX::LOG_LEVEL_INFO, 'Transport zip normalized: ' . $packageZip);


$totalTime = sprintf('%2.4f s', microtime(true) - $mtime);
$modx->log(modX::LOG_LEVEL_INFO, "\nPackage built. Execution time: {$totalTime}\n");

session_write_close();
exit();
