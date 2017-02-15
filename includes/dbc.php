<?php
error_reporting(E_ALL ^ E_NOTICE);
/*
function my_autoloader($class)
{
    $f = $_SERVER['DOCUMENT_ROOT'] . "/includes/Class/" . $class . '.php';
    if (is_file($f))
    {
        include_once($f);
    }
}
*/

function autoload( $class, $dir = null ) {

    if ( is_null( $dir ) )
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/includes/";

    foreach ( scandir( $dir ) as $file ) {

        // directory?
        if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
            autoload( $class, $dir.$file.'/' );

        // php file?
        if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {

            // filename matches class?
            if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class ) {

                include $dir . $file;
            }
        }
    }
}



// ACTIVATE AUTOLOADER
spl_autoload_register('autoload');




// APPLY GLOBAL SETTINGS FROM SETTINGS FILE
CoreConfig::applySettings(require_once('settings.php'));




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


// Setup AOP framework by GO!

$applicationAspectKernel = Stark\ApplicationAspectKernel::getInstance();
$applicationAspectKernel->init(array(
    'debug' => true, // use 'false' for production mode
    // Cache directory
    'cacheDir'  => __DIR__ . '/aop_cache'
));



// SET USER INFORMATION
if(!empty($_SESSION) && isset($_SESSION['sid']) )
{
    $StudentMapper = new StudentMapper();

    WebUser::setUser($StudentMapper->findByPk($_SESSION['sid']));
}


// SET TIMEZONE
date_default_timezone_set(CoreConfig::settings()['timezone']); // register timezone

?>