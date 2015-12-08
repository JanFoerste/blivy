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
            $_SESSION['flash'] = [
                'error' => [],
                'warning' => [],
                'info' => [],
                'success' => []
            ];
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
     * @return array
     */
    public static function getErrorMessages()
    {
        $arr = self::init();
        return $arr['error'];
    }

    /**
     * @return array
     */
    public static function getWarningMessages()
    {
        $arr = self::init();
        return $arr['warning'];
    }

    /**
     * @return array
     */
    public static function getInfoMessages()
    {
        $arr = self::init();
        return $arr['info'];
    }

    /**
     * @return $array
     */
    public static function getSuccessMessages()
    {
        $arr = self::init();
        return $arr['success'];
    }

    /**
     * ### Sets flash value
     *
     * @param $type
     * @param $key
     * @param $message
     */
    public static function set($type = 'info', $key = '', $message = '')
    {
        $arr = self::init();
        $arr = $arr[$type];
        if ($key !== '') {
            $arr[$key] = $message;
        } else {
            array_push($arr, $message);
        }
        $_SESSION['flash'] = $arr;
    }
}