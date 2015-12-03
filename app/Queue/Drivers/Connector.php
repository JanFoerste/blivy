<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Drivers;


use Blivy\Support\Config;

class Connector
{
    public static function get()
    {
        $driver = Config::get('queue', 'driver');

        $provider = Config::get('queue', 'connections.' . $driver . '.class');
        $provider = new $provider();

        return $provider;
    }
}