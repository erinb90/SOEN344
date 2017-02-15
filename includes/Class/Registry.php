<?php
namespace Stark;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

/**
 * Class Registry
 * This is a singleton pattern class which returns database connection
 */
class Registry
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private static $connection;

    /**
     * @var array connection parameters
     */
    private static $params;

    /**
     * @var Configuration
     */
    private static $config;

    /**
     * @param $connectionParams array
     * @param Configuration $config
     */
    public static function setConfig($connectionParams, Configuration $config)
    {
        self::$config = $config;
        self::$params = $connectionParams;
    }


    /**
     * @return Configuration
     */
    public static function getConfig()
    {
        return self::$config;
    }


    /**
     * @return \Doctrine\DBAL\Connection
     */
    public static function getConnection()
    {
        if (self::$connection === null)
        {
            if (self::$config === null)
            {
                throw new RuntimeException('No config set, cannot create connection');
            }
            self::$connection = null;
            try
            {
                self::$connection = DriverManager::getConnection(self::$params, self::$config);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
            }
        }
        return self::$connection;
    }
}

