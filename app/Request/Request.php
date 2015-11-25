<?php

namespace Blivy\Request;

use Blivy\Http\Router\Router;
use Blivy\Support\Config;

/**
 * @author Jan Foerste <me@janfoerste.de>
 */
class Request
{
    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->initSession();
        $_SESSION['router'] = new Router();
        $_SESSION['router']->route();
    }

    /**
     * ### Initialises a session, sets the cookie parameters
     * ### and generates a CSRF-Token
     */
    private function initSession()
    {
        $lifetime = 60 * Config::get('auth', 'session_lifetime');

        ini_set('session.cookie_lifetime', $lifetime);
        session_set_cookie_params($lifetime, '/', '.' . getenv('HOSTNAME'), false);
        session_start();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = hash("sha512", mt_rand(0, mt_getrandmax()));
        }
    }

}