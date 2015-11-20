<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Database;

use Manager\Exception\Exception;

class DB
{
    /**
     * @var \PDO
     */
    private static $_instance = null;

    public function __construct()
    {
        self::$_instance = $this->connect();
        return self::$_instance;
    }

    /**
     * @return \PDO
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            new DB();
        }
        return self::$_instance;
    }

    public function connect()
    {
        $host = getenv('DB_HOST');
        $db = getenv('DB_NAME');
        $charset = getenv('DB_CHARSET');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        try {
            $conn = new \PDO('mysql:host=' . $host . ';dbname=' . $db . ';charset=' . $charset, $user, $password);
            $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return $conn;
    }
}