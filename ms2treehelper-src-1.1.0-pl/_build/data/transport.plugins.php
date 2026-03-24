<?php
/** @var modX $modx */
if (!function_exists('getSnippetContent')) {
    function getSnippetContent($filename)
    {
        if (!file_exists($filename)) {
            return '';
        }
        $o = file_get_contents($filename);
        $o = preg_replace('#^<\?php#', '', $o);
        return trim($o);
    }
}

$plugins = array();

$plugin = $modx->newObject('modPlugin');
$plugin->fromArray(array(
    'name' => 'ms2TreeHelper',
    'description' => 'Adds compact bulk category action icons to the miniShop2 option categories tree.',
    'plugincode' => getSnippetContent($sources['elements'] . 'plugins/plugin.ms2treehelper.php'),
    'static' => false,
    'source' => 1,
    'disabled' => false,
), '', true, true);

$plugins[] = $plugin;

return $plugins;
