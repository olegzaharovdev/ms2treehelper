<?php
/**
 * ms2TreeHelper plugin
 * Loads manager JS. The script itself activates only in miniShop2 option windows.
 */
if ($modx->event->name !== 'OnManagerPageBeforeRender') {
    return;
}

if (empty($modx->controller) || !method_exists($modx->controller, 'addJavascript')) {
    return;
}

$url = MODX_ASSETS_URL . 'components/ms2treehelper/js/mgr/ms2treehelper.js?v=1.1.0';
$modx->controller->addJavascript($url);
