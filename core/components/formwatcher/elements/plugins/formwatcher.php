<?php
switch ($modx->event->name) {
    case 'OnMODXInit':
        $formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'));
        if(!empty($formWatcherCache)){
            $pdo = $modx->getService('pdoTools');

            $from = $modx->getOption('formwatcher_email_from', null, $modx->getOption('emailsender'));
            $to = $modx->getOption('formwatcher_email_to', null, $modx->getOption('emailsender'));
            $subject = $modx->getOption('formwatcher_email_subject', null, 'Отчет FormWatcher');
            $waiting_time = $modx->getOption('formwatcher_waiting_time', null, '1 day');
            $email_tpl = $modx->getOption('email_tpl', null, 'fw_email_report');

            $sendForms = array();

            foreach($formWatcherCache as $user_id => $forms){
                if(count($forms) > 0){
                    foreach($forms as $form_id => $form){
                        if(isset($form['timestamp'])){
                            if($form['timestamp']  < strtotime(' - '.$waiting_time) && !empty($user_id)){
                                unset($form['af_action']);
                                $sendForms[] = $form;
                                if(count($formWatcherCache[$user_id]) === 1){
                                    unset($formWatcherCache[$user_id]);
                                }else{
                                    unset($formWatcherCache[$user_id][$form_id]);
                                }
                            }
                        }
                    }
                }else{
                    unset($formWatcherCache[$user_id]);
                }

            }

            if(count($sendForms) > 0){
                $message = $pdo->getChunk($email_tpl, array('forms' => $sendForms));
                $modx->getService('mail', 'mail.modPHPMailer');
                $modx->mail->set(modMail::MAIL_BODY,$message);
                $modx->mail->set(modMail::MAIL_FROM,$from);
                $modx->mail->set(modMail::MAIL_FROM_NAME,$modx->getOption('site_name'));
                $modx->mail->set(modMail::MAIL_SUBJECT,$subject);
                $to = array_map('trim', explode(',', $to));
                foreach($to as $email){
                    $modx->mail->address('to',$email);
                }

                $modx->mail->setHTML(true);
                if (!$modx->mail->send()) {
                    $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                }
                $modx->mail->reset();
            }

            $options = array(
                xPDO::OPT_CACHE_KEY => 'formwatcher',
            );
            $modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);

        }
        break;
    case 'OnHandleRequest':
        // Handle ajax requests
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (empty($_REQUEST['fw_client_id']) || empty($_REQUEST['fw_form_id']) || !$isAjax) {
            return;
        }

        $fields = array();
        foreach($_POST as $key => $value){
            $value = trim( filter_input(INPUT_POST,$key,  FILTER_SANITIZE_STRING) );
            if(!empty($value)){
                $fields[$key] = $value;
            }

        }

        //Проверка на обязательные поля
        $required_fields_option = $modx->getOption('formwatcher_required_fields');
        if(!empty($required_fields_option)){
            $required_fields_arr = explode(';', $required_fields_option);
            if(count($required_fields_arr) > 0){
                foreach($required_fields_arr as $required_fields_item){
                    if(!empty($required_fields_item)){
                        $required_fields_item_form = explode(':', $required_fields_item);
                        if(count($required_fields_item_form) == 2){
                            $form_id = trim($required_fields_item_form[0]);
                            if($form_id == $_REQUEST['fw_form_id']){
                                //email,phone
                                $required_fields = explode(',', $required_fields_item_form[1]);
                                if(count($required_fields) > 0){
                                    foreach($required_fields as $field){
                                        if(!array_key_exists($field, $fields)){return;}
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }



        $client_id = $fields['fw_client_id'];
        unset($fields['fw_client_id']);

        if(count($fields) > 1){
            $fields['timestamp'] = time();

            $formWatcherCache = array();
            if(!$formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'))){
                $formWatcherCache = array();
            }

            $formWatcherCache[$client_id][$fields['fw_form_id']] = $fields;

            $options = array(
                xPDO::OPT_CACHE_KEY => 'formwatcher',
            );
            $modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);
        }

        die();
        break;
}