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
            $email_tpl = $modx->getOption('email_tpl', null, 'formwatcher.email.tpl');


            foreach($formWatcherCache as $key => $form){
                if($form['timestamp']  < strtotime(' - '.$waiting_time) ){

                    unset($form['af_action']);
                    unset($form['timestamp']);

                    $message = $pdo->getChunk($email_tpl, array('form' => $form));
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