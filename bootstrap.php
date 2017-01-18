<?php
function my_autoloader($class)
{
    $f = "includes/Class/" . $class . '.php';
    if (is_file($f))
    {
        include_once($f);
    }
}
// ACTIVATE AUTOLOADER
spl_autoload_register('my_autoloader');
?>