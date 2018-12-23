<?php

$client_id = $hook->getValue('client_id');
$formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'));
unset($formWatcherCache[$client_id]);
$options = array(
    xPDO::OPT_CACHE_KEY => 'formwatcher',
);
$modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);

return true;
