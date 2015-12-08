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
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [
                'error' => [],
                'warning' => [],
                'info' => [],
                'success' => [],
                'formset' => []
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
     * @return mixed
     */
    public static function getFormValue($key)
    {
        $arr = self::init();
        $array = $arr['formset'];
        if (isset($array[$key])) {
            return $array[$key];
        } else {
            return null;
        }
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
     * @param $key
     * @return bool
     */
    public static function hasError($key)
    {
        $arr = self::init();
        return isset($arr['error'][$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function hasInfo($key)
    {
        $arr = self::init();
        return isset($arr['info'][$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function hasWarning($key)
    {
        $arr = self::init();
        return isset($arr['warning'][$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function hasSuccess($key)
    {
        $arr = self::init();
        return isset($arr['success'][$key]);
    }

    /**
     * ### Sets flash value
     *
     * @param $type
     * @param $key
     * @param $message
     */
    public static function set($type = 'info', $message = '', $key = '')
    {
        $arr = self::init();
        if ($key !== '') {
            $arr[$type][$key] = $message;
        } else {
            array_push($arr[$type], $message);
        }
        $_SESSION['flash'] = $arr;
    }
}