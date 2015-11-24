<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Database;

use Blivy\Exception\Exception;

class DB
{
    /**
     * @var \PDO
     */
    private static $_instance = null;

    /**
     * ### Creates a new DB instance
     *
     * DB constructor.
     */
    public function __construct()
    {
        self::$_instance = $this->connect();
        return self::$_instance;
    }

    /**
     * ### Creates new instance if not already set
     *
     * @return \PDO
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            new DB();
        }
        return self::$_instance;
    }

    /**
     * ### Connects to a database
     *
     * @return \PDO
     * @throws Exception
     */
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