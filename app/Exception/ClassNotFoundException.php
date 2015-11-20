<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


class ClassNotFoundException extends Exception
{
    public function __construct($class)
    {
        $message = 'The requested class ' . $class . ' could not be found!';
        parent::__construct($message, 404, null, []);
    }
}