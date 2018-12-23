<?php
switch ($modx->event->name) {
    case 'OnMODXInit':
        $formWatcherCache = $modx->cacheManager->get('formwatcher', array(xPDO::OPT_CACHE_KEY=>'formwatcher'));
        if(!empty($formWatcherCache)){
            $pdo = $modx->getService('pdoTools');

            $from = $modx->getOption('formwatcher_email_from', null, $modx->getOption('emailsender'));
            $to = $modx->getOption('formwatcher_email_to', null, $modx->getOption('emailsender'));
            $subject = $modx->getOption('formwatcher_subject', null, 'Отчет FormWatcher');
            $waiting_time = $modx->getOption('formwatcher_waiting_time', null, '1 day');
            $email_tpl = $modx->getOption('email_tpl', null, 'fw_email_report');

            $sendForms = array();

            foreach($formWatcherCache as $key => $form){
                if($form['timestamp']  < strtotime(' - '.$waiting_time) && !empty($key)){
                    unset($form['af_action']);
                    $sendForms[] = $form;
                    unset($formWatcherCache[$key]);
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
                }else{
                    $modx->mail->reset();
                    unset($formWatcherCache[$key]);
                    $options = array(
                        xPDO::OPT_CACHE_KEY => 'formwatcher',
                    );
                    $modx->cacheManager->set('formwatcher', $formWatcherCache, 0, $options);
                }
            }

        }
        break;
}