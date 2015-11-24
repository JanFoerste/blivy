<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;

use Blivy\Http\Router\Router;

class HttpNotFoundException extends Exception
{
    public function __construct()
    {
        $message = 'The requested route does not exist!';
        parent::__construct($message, 404, null, ['URI' => Router::formatURI($_SESSION['REQUEST_URI'])]);
    }
}