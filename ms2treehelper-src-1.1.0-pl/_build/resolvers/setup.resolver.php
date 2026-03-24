<?php
/**
 * Resolver for ms2TreeHelper.
 * Ensures namespace and plugin event binding exist on install/upgrade.
 *
 * @var xPDOTransport $object
 * @var array $options
 */
if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;
} else {
    return false;
}

$action = isset($options[xPDOTransport::PACKAGE_ACTION]) ? $options[xPDOTransport::PACKAGE_ACTION] : '';

switch ($action) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $namespace = $modx->getObject('modNamespace', array('name' => 'ms2treehelper'));
        if (!$namespace) {
            $namespace = $modx->newObject('modNamespace');
            $namespace->set('name', 'ms2treehelper');
            $namespace->set('path', '{core_path}components/ms2treehelper/');
            $namespace->set('assets_path', '{assets_path}components/ms2treehelper/');
            $namespace->save();
        }

        /** @var modPlugin $plugin */
        $plugin = $modx->getObject('modPlugin', array('name' => 'ms2TreeHelper'));
        if ($plugin) {
            $plugin->set('disabled', 0);
            $plugin->save();

            /** @var modEvent $event */
            $event = $modx->getObject('modEvent', array('name' => 'OnManagerPageBeforeRender'));
            if ($event) {
                $pluginEvent = $modx->getObject('modPluginEvent', array(
                    'pluginid' => $plugin->get('id'),
                    'event' => $event->get('name'),
                ));

                if (!$pluginEvent) {
                    $pluginEvent = $modx->newObject('modPluginEvent');
                    $pluginEvent->fromArray(array(
                        'pluginid' => $plugin->get('id'),
                        'event' => $event->get('name'),
                        'priority' => 0,
                        'propertyset' => 0,
                    ), '', true, true);
                    $pluginEvent->save();
                }
            }
        }
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        $plugin = $modx->getObject('modPlugin', array('name' => 'ms2TreeHelper'));
        if ($plugin) {
            $pluginEvent = $modx->getObject('modPluginEvent', array(
                'pluginid' => $plugin->get('id'),
                'event' => 'OnManagerPageBeforeRender',
            ));
            if ($pluginEvent) {
                $pluginEvent->remove();
            }
        }

        $namespace = $modx->getObject('modNamespace', array('name' => 'ms2treehelper'));
        if ($namespace) {
            $namespace->remove();
        }
        break;
}

return true;
