<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


use Blivy\Http\Router\Router;

class HttpMethodNotAllowedException extends Exception
{
    public function __construct()
    {
        $message = 'The requested route method is not allowed!';
        parent::__construct($message, 404, null, [
            'URI' => Router::formatURI($_SERVER['REQUEST_URI']),
            'Method' => $_SERVER['REQUEST_METHOD']
        ]);
    }
}