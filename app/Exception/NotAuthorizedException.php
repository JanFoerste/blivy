<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


class NotAuthorizedException extends Exception
{
    public function __construct()
    {
        $message = 'Not authorized.';
        parent::__construct($message, 401, null, []);
    }
}