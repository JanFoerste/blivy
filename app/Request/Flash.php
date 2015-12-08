<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Request;


class Flash
{
    /**
     * ### Retrieves the flash storage
     *
     * @return array
     */
    private static function init()
    {
        if (!isset($SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        return $_SESSION['flash'];
    }

    /**
     * ### Checks if key exists
     *
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        $arr = self::init();
        return isset($arr[$key]);
    }

    /**
     * ### Gets flash value
     *
     * @param $key
     * @return null
     */
    public static function get($key)
    {
        if (!self::has($key)) return null;
        $arr = self::init();
        $get = $arr[$key];
        unset($arr[$key]);
        return $get;
    }

    /**
     * ### Sets flash value
     *
     * @param $key
     * @param $message
     */
    public static function set($key, $message)
    {
        $arr = self::init();
        $arr[$key] = $message;
        $_SESSION['flash'] = $arr;
    }
}