<?php
error_reporting(E_ALL ^ E_NOTICE);
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Stark\CoreConfig;
use Stark\Registry;
use Stark\Mappers\StudentMapper;
use Stark\WebUser;
use Doctrine\Common\ClassLoader;

// APPLY GLOBAL SETTINGS FROM SETTINGS FILE
CoreConfig::applySettings(require_once('settings.php'));


$classLoader = new ClassLoader('Doctrine', $_SERVER['DOCUMENT_ROOT'] . '/vendor/doctrine/');
$classLoader->register();
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
    'url' => CoreConfig::settings()['db']['development'],
);

Registry::setConfig($connectionParams, $config);


// Setup AOP framework by GO!

$applicationAspectKernel = Stark\ApplicationAspectKernel::getInstance();
$applicationAspectKernel->init(array(
    'debug'    => TRUE, // use 'false' for production mode
    // Cache directory
    'cacheDir' => __DIR__ . '/aop_cache'
));


// SET USER INFORMATION
if (!empty($_SESSION) && isset($_SESSION['sid']))
{
    $StudentMapper = new \Stark\Mappers\UserMapper();

    echo $_SESSION['sid'];
    WebUser::setUser($StudentMapper->findByPk($_SESSION['sid']));
}


// SET TIMEZONE
date_default_timezone_set(CoreConfig::settings()['timezone']); // register timezone

?>