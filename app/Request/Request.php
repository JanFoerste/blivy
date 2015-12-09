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
        $hostname = Config::get('app', 'hostname');
        if (filter_var($hostname, FILTER_VALIDATE_IP)) {
            $cookie_host = $hostname;
        } else {
            $cookie_host = '.' . $hostname;
        }

        ini_set('session.cookie_lifetime', $lifetime);
        session_set_cookie_params($lifetime, '/', $cookie_host, false);
        session_start();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = hash("sha512", mt_rand(0, mt_getrandmax()));
        }

        if (!isset($_SESSION['blivy_redirect']) || $_SESSION['blivy_redirect'] !== true) {
            if (isset($_SESSION['flash'])) unset($_SESSION['flash']);
        } else {
            unset($_SESSION['blivy_redirect']);
        }
    }

}