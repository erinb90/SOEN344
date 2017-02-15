<?php


/**
 * This class contains applications settings returned by the settings file:  settings.php
 * Turned it into a singleton so it can be globally accessed
 */
class CoreConfig
{

    /**
     * @var array
     */
    private static $confArray = [];

    /**
     * CoreConfig constructor.
     */
    private function __construct()
    {

    }

    /**
     * @param $settings array array of settings
     */
    public static function applySettings(array $settings)
    {
        self::$confArray = $settings;
    }

    /**
     * @return array returns an array of settings
     */
    public static function settings()
    {
        return self::$confArray;
    }

}


