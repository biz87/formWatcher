<?php
$form_id = $hook->getValue('fw_form_id');
$client_id = $hook->getValue('fw_client_id');
$formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'));
if(count($formWatcherCache[$client_id]) === 1){
    unset($formWatcherCache[$client_id]);
}else{
    unset($formWatcherCache[$client_id][$form_id]);
}

$options = array(
    xPDO::OPT_CACHE_KEY => 'formwatcher',
);
$modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);

return true;