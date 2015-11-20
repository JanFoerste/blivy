<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Request;

class Input
{
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

    public static function has($name)
    {
        return isset($_POST[$name]) || isset($_GET[$name]) ? true : false;
    }

    public static function all()
    {
        return $_POST;
    }
}