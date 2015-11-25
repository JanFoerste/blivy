<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Auth;

use Blivy\Support\Config;

class Auth
{
    private static $driver;

    /**
     * ### Gets the driver instance
     *
     * @return DBAuthProvider|mixed
     */
    public static function driver()
    {
        if (!isset(self::$driver)) {
            $driver = Config::get('auth', 'driver');
        } else {
            $driver = self::$driver;
        }

        $provider = Config::get('auth', 'providers.' . $driver . '.class');
        $provider = new $provider();

        return $provider;
    }

    /**
     * ### Sets a one-time driver
     *
     * @param $driver
     */
    public static function setDriver($driver)
    {
        self::$driver = $driver;
    }

    /**
     * ### Attempts a login
     *
     * @param string $user
     * @param string $password
     * @param bool|false $remember
     * @return bool
     */
    public static function attempt($user, $password, $remember = false)
    {
        if (self::check()) return true;
        $try = self::driver()->attempt($user, $password, $remember);
        if ($try != false) self::startSession($try);
        return $try;
    }

    /**
     * ### Stops the session and logs out the user
     *
     * @return bool
     */
    public static function logout()
    {
        if (!self::check()) return true;
        self::stopSession();
        return true;
    }

    /**
     * ### Checks if a user is logged in
     *
     * @return bool
     */
    public static function check()
    {
        return self::driver()->check();
    }

    /**
     * ### Gets the logged in user's data
     *
     * @return null
     */
    public static function user()
    {
        if (!self::check()) return null;
        if (!isset($_SESSION['logged_in'])) return null;
        return $_SESSION['userdata'];
    }

    /**
     * ### Sets all session data
     *
     * @param $user
     */
    private static function startSession($user)
    {
        $_SESSION['logged_in'] = true;
        $_SESSION['userdata'] = $user;
    }

    /**
     * ### Clears the session data
     */
    private static function stopSession()
    {
        $_SESSION['logged_in'] = false;
        self::driver()->clearSession();
        unset($_SESSION['userdata']);
    }
}