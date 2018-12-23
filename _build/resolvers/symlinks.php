<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/formWatcher/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/formwatcher')) {
            $cache->deleteTree(
                $dev . 'assets/components/formwatcher/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/formwatcher/', $dev . 'assets/components/formwatcher');
        }
        if (!is_link($dev . 'core/components/formwatcher')) {
            $cache->deleteTree(
                $dev . 'core/components/formwatcher/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/formwatcher/', $dev . 'core/components/formwatcher');
        }
    }
}

return true;