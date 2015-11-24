<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Cache;

/**
 * ### Cache handler. Can currently choose between redis and file caching
 *
 * Class Cache
 * @package Manager\Cache
 */
class Cache
{
    /**
     * @var string
     */
    private static $driver;

    /**
     * ### Creates a new driver instance
     *
     * @return FileCache|RedisCache|null
     */
    private static function driver()
    {
        if (!isset(self::$driver)) {
            $driver = env('CACHE_DRIVER', 'file');
        } else {
            $driver = self::$driver;
        }

        switch ($driver) {
            case 'file':
                $class = new FileCache();
                break;
            case 'redis':
                $class = new RedisCache();
                break;
            default:
                $class = null;
        }

        self::$driver = null;
        return $class;
    }

    /**
     * ### Sets a driver to be used once
     *
     * @param string $driver
     */
    public static function setDriver($driver = 'file')
    {
        self::$driver = $driver;
    }

    /**
     * ### Sets a new value to be cached.
     * ### Expiration only supported by redis
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    public static function set($key, $value, $expire = 60)
    {
        return self::driver()->set($key, $value, $expire);
    }

    /**
     * ### Retrieves the requested cache value
     *
     * @param string $key
     * @return null|string
     */
    public static function get($key)
    {
        return self::driver()->get($key);
    }

    /**
     * ### Checks if the value exists
     *
     * @param $key
     * @return bool|int
     */
    public static function exists($key)
    {
        return self::driver()->exists($key);
    }

    public static function remove($key)
    {
        if (!self::exists($key)) return true;
        return self::driver()->remove($key);
    }

    /**
     * ### Flushes the current cache driver (Removes all key/value pairs)
     *
     * @return bool
     */
    public static function flush()
    {
        return self::driver()->flush();
    }
}