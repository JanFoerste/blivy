<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Auth;

/**
 * ### Base authentication functions
 *
 * Class Auth
 * @package Manager\Auth
 */
class Auth
{
    /**
     * ### Attempts to authenticate the given user
     * ### If requested, a remember token will be set
     *
     * @param string $user
     * @param string $password
     * @param bool|false $remember
     * @return bool
     */
    public static function attempt($user, $password, $remember = false)
    {
        $model = env('AUTH_MODEL', '\Models\User') . 'Query';
        $query = new $model();

        // ### Email and username can be used to authenticate
        if (strpos($user, '@') > -1) {
            $query->filterByEmail($user);
        } else {
            $query->filterByName($user);
        }

        // ### If no entry exists, return false
        if (!$query->exists()) return false;

        $user = $query->findOne();
        $hash = $user->getPassword();
        $verify = password_verify($password, $hash);

        if (!$verify) {
            self::stopSession();
            return false;
        };

        if ($remember) self::setRememberToken($user, $hash);
        self::startSession($user);

        return true;
    }

    /**
     * ### Attemts a login with the given remember token
     *
     * @param string $token
     * @return bool
     */
    public static function attemptByToken($token)
    {
        $explode = explode('.', $token);
        $model = env('AUTH_MODEL', '\Models\User') . 'Query';
        $query = new $model();
        $find = $query->findPk($explode[0]);

        if (!$find) return false;
        $stored = $find->getRemember();
        if (!$stored || $stored !== $explode[1]) return false;

        self::startSession($find);
        return true;
    }

    /**
     * ### Tries to auto-login with the remember_token cookie
     *
     * @return bool
     */
    public static function tryAutoLogin()
    {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }
        return self::attemptByToken($_COOKIE['remember_token']);
    }

    /**
     * ### Starts a session for the given user
     *
     * @param \Models\User $user
     */
    private static function startSession($user)
    {
        $_SESSION['logged_in'] = 1;
        $_SESSION['user'] = $user->getId();
    }

    /**
     * ### Stops a session, removes all tokens and destroys parameters
     *
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private static function stopSession()
    {
        self::get()->setRemember(null);
        self::get()->save();

        $cookie_name = 'remember_token';
        setcookie($cookie_name, null, -1);
        if (isset($_SESSION)) {
            $_SESSION['logged_in'] = 0;
            $_SESSION['user'] = null;
        }

        return true;
    }

    /**
     * ### Sets a remember token for the given user
     *
     * @param User $user
     * @param string $hash
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private static function setRememberToken(User $user, $hash)
    {
        $cookie_name = 'remember_token';
        $cookie_time = (3600 * 24 * 30);
        $sha = sha1($hash);
        $remember = $user->getId() . '.' . $sha;

        setcookie($cookie_name, $remember, time() + $cookie_time);

        $user->setRemember($sha);
        $user->save();
    }

    /**
     * ### Checks if the client is authenticated
     *
     * @return bool
     */
    public static function check()
    {
        if (!isset($_SESSION) || !isset($_SESSION['logged_in'])) {
            return self::tryAutoLogin();
        };;
        if ($_SESSION['logged_in'] == 1) return true;

        return self::tryAutoLogin();
    }

    /**
     * ### Kills the session and logs out the user
     *
     * @return bool
     */
    public static function logout()
    {
        if (self::check()) return self::stopSession();
        return true;
    }

    /**
     * ### Gets the user model for the current session
     *
     * @return mixed|\Models\User
     */
    public static function get()
    {
        if (!self::check()) {
            return false;
        }

        $model = env('AUTH_MODEL', '\Models\User') . 'Query';
        $query = new $model();
        $find = $query->findPk($_SESSION['user']);

        return $find;
    }
}