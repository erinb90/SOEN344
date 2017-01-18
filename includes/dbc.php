<?php
error_reporting(E_ALL ^ E_NOTICE);
function my_autoloader($class)
{
    $f = $_SERVER['DOCUMENT_ROOT'] . "/includes/Class/" . $class . '.php';
    if (is_file($f))
    {
        include_once($f);
    }
}

// ACTIVATE AUTOLOADER
spl_autoload_register('my_autoloader');


/*
// APPLY GLOBAL SETTINGS FROM SETTINGS FILE
CoreConfig::applySettings(require_once('settings.php'));

*/

/*
// SET DOCTRINE LIBRARY SETTINGS FOR QUERYBUILDER
use Doctrine\Common\ClassLoader;
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

$classLoader = new ClassLoader('Doctrine', $_SERVER['DOCUMENT_ROOT'].'/vendor/doctrine/');
$classLoader->register();
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
    'url' => CoreConfig::settings()['db']['development'],
);
Registry::setConfig($connectionParams, $config);

// SET USER INFORMATION
if(!empty($_SESSION) && isset($_SESSION['uid']) )
{
    $UserMapper = new UserMapper();

    WebUser::setUser($UserMapper->findByPk($_SESSION['uid']));
}
*/

// SET TIMEZONE
//date_default_timezone_set(CoreConfig::settings()['timezone']); // register timezone

?>