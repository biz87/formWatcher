<?php

return [
    'email_from' => [
        'xtype' => 'textfield',
        'area' => 'formwatcher_main',
    ],
    'email_to' => [
        'xtype' => 'textfield',
        'area' => 'formwatcher_main',
    ],
    'email_subject' => [
        'xtype' => 'textfield',
        'value' => 'Отчет FormWatcher',
        'area' => 'formwatcher_main',
    ],
    'email_tpl' => [
        'xtype' => 'textfield',
        'value' => 'fw_email_report',
        'area' => 'formwatcher_main',
    ],
    'waiting_time' => [
        'xtype' => 'textfield',
        'value' => '1 day',
        'area' => 'formwatcher_main',
    ],
];