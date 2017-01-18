<?php

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
     * @var \Doctrine\DBAL\Configuration
     */
    private static $config;

    /**
     * @param $connectionParams array
     * @param \Doctrine\DBAL\Configuration $config
     */
    public static function setConfig($connectionParams, \Doctrine\DBAL\Configuration $config)
    {
        self::$config = $config;
        self::$params = $connectionParams;
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
                self::$connection = \Doctrine\DBAL\DriverManager::getConnection(self::$params, self::$config);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
            }
        }
        return self::$connection;
    }
}

