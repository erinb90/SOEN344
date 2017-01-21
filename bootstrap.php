<?php

require_once ("vendor/autoload.php");

/**
 * @param $class
 */
function class_loader($class)
{
    $f = "includes/Class/" . $class . '.php';
    if (is_file($f))
    {
        include_once($f);
    }
}

/**
 * @param $class
 */
function test_class_loader($class)
{
    $f = "Tests/" . $class . '.php';
    if (is_file($f))
    {
        include_once($f);
    }
}

// ACTIVATE AUTOLOADER
spl_autoload_register('class_loader');
spl_autoload_register('test_class_loader');
?>