<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


class RedisException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}