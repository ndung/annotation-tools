<?php

/**
 * Basic Configurations
 */
return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '',
    'language' => 'id',
    'preload' => ['log'],
    'sourceLanguage' => 'id',
    'defaultController' => 'tree',
    /**
     * Auto load components
     */
    'import' => [
        /**
         * Base Platform Model Configurations
         */
        'application.models.base.*',
        'application.models.base.form.*',
        /**
         * Base Platform Model Configurations
         */
        'application.models.statistic.*',
        /**
         * General Configurations
         */
        'application.components.*',
        'application.components.base.*',
        'application.components.controllers.*',
        'application.components.core.*',
        'application.components.tools.*',
        'application.components.widget.*',
    /**
     * Specific Configurations
     */
    ],
    'modules' => [
        'administration' => [
            'defaultController' => 'core',
        ],
        'API' => [
            'defaultController' => 'core',
        ],
    ],
    /**
     * Application components
     */
    'components' => [
        'connectionStatisticDB' => [
            'class' => 'CDbConnection',
        ],
        'user' => [
            'allowAutoLogin' => true,
            'class' => 'UserWeb',
            // this is actually the default value
            'loginUrl' => array('authenticate/login'),
        ],
        'urlManager' => array(
            'urlFormat' => 'path',
            'appendParams' => false,
            'showScriptName' => false,
            'rules' => array(
                'gii' => 'gii/default',
                '/' => 'site/index',
                /**
                 * General Rules
                 */
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ],
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        /**
         * emails
         */
        'emails' => array(),
        'telephone' => '',
        'address' => '',
        'externalLink' => array(
            'bytemeup!' => array(
                'URL' => 'http://bmustudio.com/',
            ),
        ),
    ),
];
