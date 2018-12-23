<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

define('MODX_API_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/index.php');

$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);

$fields = array();
foreach($_POST as $key => $value){
    $fields[$key] = trim( filter_input(INPUT_POST,$key,  FILTER_SANITIZE_STRING) );
}
$client_id = $fields['client_id'];
unset($fields['client_id']);
$fields['timestamp'] = time();

$formWatcherCache = array();
if(!$formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'))){
    $formWatcherCache = array();
}


$formWatcherCache[$client_id] = $fields;
$options = array(
    xPDO::OPT_CACHE_KEY => 'formwatcher',
);
$modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);


$data = array();
$data['post'] = ($_POST);
echo json_encode($data);
die();