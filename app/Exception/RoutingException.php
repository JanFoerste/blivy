<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


class RoutingException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}