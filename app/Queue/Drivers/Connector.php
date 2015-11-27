<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Drivers;


class Connector
{
    public static function get()
    {
        return new RedisQueueDriver();
    }
}