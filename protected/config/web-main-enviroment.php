<?php

$configuration = include 'web-core.php';
$configuration['components']['db'] = [
    'connectionString' => 'mysql:host=localhost;dbname=research-tree-annotation-tools',
    'emulatePrepare' => true,
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'class' => 'DatabaseConnection'
];
return $configuration;
