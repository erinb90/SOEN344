<?php

// APPLY TEST SETTINGS
use Doctrine\Common\ClassLoader;
use Stark\CoreConfig;
use Stark\Registry;

$settings = [
    'appname' => 'ForceAwakens',
    'timezone' => 'America/Montreal',
    'db' => [
        'development' => 'mysql://root:@localhost/soen344',
        'production' => 'mysql://root:@localhost/soen344',
    ],
    'reservations' => [
        'max_per_week' => 3,    // hours
        'max_repeats' => 3,    // recurrences
        'max_per_reservation' => 180,  // mins
        'lock' => 120   // seconds
    ]
];

CoreConfig::applySettings($settings);

$_SERVER['DOCUMENT_ROOT'] = "../";

$classLoader = new ClassLoader('Doctrine', $_SERVER['DOCUMENT_ROOT'] . '/vendor/doctrine/');
$classLoader->register();
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = [
    'url' => $settings['db']['development'],
];

Registry::setConfig($connectionParams, $config);