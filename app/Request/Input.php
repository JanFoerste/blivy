<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Request;

class Input
{
    /**
     * ### Gets the requested POST or GET input
     *
     * @param string $name
     * @return string
     */
    public static function get($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } elseif (isset($_GET[$name])) {
            return $_GET[$name];
        } else {
            return '';
        }
    }

    /**
     * ### Checks if input is set
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {
        return isset($_POST[$name]) || isset($_GET[$name]) ? true : false;
    }

    /**
     * ### Returns all POST and GET inputs
     *
     * @return mixed
     */
    public static function all()
    {
        return array_merge($_POST, $_GET);
    }

    /**
     * ### Retrieves a route parameter value
     *
     * @param $name
     * @return mixed
     */
    public static function route($name)
    {
        return session_get('router')->getRouteParameter($name);
    }
}