<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


class CSRFTokenException extends Exception
{
    public function __construct()
    {
        $message = 'The CSRF-Token is invalid or has expired!';
        parent::__construct($message, 404, null, []);
    }
}