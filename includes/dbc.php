<?php
error_reporting(E_ALL ^ E_NOTICE);

/*
 * recursive autoloader
function autoload( $class, $dir = null ) {

    if ( is_null( $dir ) )
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/includes/";

    foreach ( scandir( $dir ) as $file ) {

        // directory?
        if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
            autoload( $class, $dir.$file.'/' );

        // php file?
        if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {

           // echo $class;
            // filename matches class?
            if ( str_replace( '.php', '', $file ) == $class
                || str_replace('\\', '/', $file) . '.php' == $class
                || str_replace( '.class.php', '', $file ) == $class )
            {

                include $dir . $file;
            }
        }
    }
}
*/

require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

// ACTIVATE AUTOLOADER
//spl_autoload_register('autoload');

use Stark\CoreConfig;
use Stark\Registry;
use Stark\Mappers\StudentMapper;
use Stark\WebUser;

// SET DOCTRINE LIBRARY SETTINGS FOR QUERYBUILDER
use Doctrine\Common\ClassLoader;


// APPLY GLOBAL SETTINGS FROM SETTINGS FILE
CoreConfig::applySettings(require_once('settings.php'));






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