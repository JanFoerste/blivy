<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Support;


class Config
{
    /**
     * ### Tries to find a configuration item
     *
     * @param string $from
     * @param string $item
     * @return mixed
     */
    public static function get($from, $item)
    {
        $conf = include(conf($from));
        if (!isset($conf[$item])) {
            return self::tryDot($conf, $item);
        }
        return $conf[$item];
    }

    /**
     * ### Tries to find a three-dimensional array value
     *
     * @param string $conf
     * @param string $item
     * @return null
     */
    private static function tryDot($conf, $item)
    {
        if (strpos($item, '.') < 0) return null;

        $parts = explode('.', $item);
        if (!isset($conf[$parts[0]])) return null;
        if (!isset($conf[$parts[0]][$parts[1]])) return null;

        return $conf[$parts[0]][$parts[1]];
    }
}