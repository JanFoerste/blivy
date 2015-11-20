<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;

use Manager\Http\Router\Router;

class HttpNotFoundException extends Exception
{
    public function __construct()
    {
        $message = 'The requested route does not exist!';
        parent::__construct($message, 404, null, ['URI' => Router::formatURI($_SERVER['REQUEST_URI'])]);
    }
}