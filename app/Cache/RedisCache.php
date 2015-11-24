<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Cache;

use Blivy\Exception\RedisException;
use Blivy\Support\Config;
use Predis\Client;

/**
 * ### The Redis cache driver
 *
 * Class RedisCache
 * @package Manager\Cache
 */
class RedisCache
{
    /**
     * @var Client
     */
    protected $conn;

    /**
     * ### Tries to connect to the configured redis database
     *
     * RedisCache constructor.
     */
    public function __construct()
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => Config::get('cache', 'redis_server.host'),
            'port' => Config::get('cache', 'redis_server.port'),
            'database' => Config::get('cache', 'redis_server.database')
        ]);

        try {
            $client->connect();
        } catch (\Exception $e) {
            throw new RedisException('Connection to redis server failed. Please check your settings.');
        }

        $this->conn = $client;
    }

    /**
     * ### Checks if the key/value pair exists
     *
     * @param string $key
     * @return int
     */
    public function exists($key)
    {
        return $this->conn->exists($key);
    }

    /**
     * ### Sets or replaces the given key/value pair
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 60)
    {
        $prefix = Config::get('cache', 'key_prefix');
        $this->conn->setex($prefix . $key, $expire * 60, serialize($value));
        return true;
    }

    /**
     * ### Returns the value belonging to the given key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $prefix = Config::get('cache', 'key_prefix');
        return unserialize($this->conn->get($prefix . $key));
    }

    /**
     * ### Deletes the redis cache
     *
     * @return bool
     */
    public function flush()
    {
        $this->conn->flushdb();
        return true;
    }
}