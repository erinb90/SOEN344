<?php
use Doctrine\DBAL\DriverManager;

abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    // Instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;
    static private $dbal = null;

    //  Instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return null|PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null)
        {
            if (self::$pdo == null)
            {
                self::$pdo = new PDO(
                    $GLOBALS['DB_DSN'],
                    $GLOBALS['DB_USER'],
                    $GLOBALS['DB_PSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    /**
     * @return PDO
     */
    protected function getPdo()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDbal()
    {
        if (self::$dbal === null)
        {
            self::$dbal = DriverManager::getConnection(['pdo' => $this->getPdo()]);
        }

        return self::$dbal;
    }
}