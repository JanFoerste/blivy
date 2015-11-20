<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


class MethodNotFoundException extends Exception
{
    public function __construct($method)
    {
        $message = 'The requested method ' . $method . ' could not be found!';
        parent::__construct($message, 404, null, []);
    }
}