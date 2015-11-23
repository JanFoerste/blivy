<?php

namespace Manager\Request;
use Manager\Http\Router\Router;

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
        ini_set('session.cookie_lifetime', 60 * getenv('SESSION_LIFETIME'));
        session_set_cookie_params(60 * getenv('SESSION_LIFETIME'), '/', '.' . getenv('HOSTNAME'), false);
        session_start();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = hash("sha512",mt_rand(0,mt_getrandmax()));
        }
    }

}