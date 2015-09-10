<?php

$coreConfiguration = include 'web-base.php';
$coreConfiguration['name'] = 'Research';
$coreConfiguration['modules']['parser'] = [
    'defaultController' => 'tree',
];
$coreConfiguration['import'] = array_merge($coreConfiguration['import'], [
    'application.models.*',
    'application.models.form.*',
    'application.models.baseentity.*',
    'application.models.baseentity.account.*',
    'application.models.baseentity.parsetree.*',
    'application.models.baseentity.session.*',
    'application.models.entity.*',
    'application.models.entity.account.*',
    'application.models.entity.parsetree.*',
    'application.models.entity.session.*',
    'application.components.*',
        ]);
$coreConfiguration['components']['urlManager']['rules'] = array_merge([
        ], $coreConfiguration['components']['urlManager']['rules']);

$coreConfiguration['components']['mail'] = [
    'class' => 'ext.yii-mail.YiiMail',
    'transportType' => 'smtp',
    'transportOptions' => [
        'host' => '',
        'username' => '',
        'password' => '',
        'port' => 587
    ],
    'logging' => false,
    'dryRun' => false
];

$coreConfiguration['params']['emails'] = array_merge($coreConfiguration['params']['emails'], [
    'noReply' => '',
    'sysadmin' => '',
    'developerList' => [
    ]
        ]);

$coreConfiguration['params']['recaptcha'] = [
    'privateKey' => '',
    'publicKey' => '',
];
return $coreConfiguration;
