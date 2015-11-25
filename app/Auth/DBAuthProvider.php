<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Auth;

use Blivy\Support\Config;
use Models\Base\User;

/**
 * ### Base authentication functions
 *
 * Class AuthProvider
 * @package Manager\Auth
 */
class DBAuthProvider implements AuthInterface
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
        $model = Config::get('auth', 'providers.db.auth_model');
        $query = new $model();

        // ### Email or username can be used to authenticate
        if (strpos($user, '@') > -1) {
            $query->filterByEmail($user);
        } else {
            $query->filterByName($user);
        }

        // ### If no entry exists, return false
        if (!$query->exists()) return false;

        $user = $query->findOne();
        $hash = $user->getPassword();
        $verify = Crypt::verify($password, $hash);

        if (!$verify) return false;

        if ($remember) self::setRememberToken($user, $hash);

        return $user;
    }

    /**
     * ### Clears session and cookie data
     */
    public static function clearSession()
    {
        if (isset($_SESSION['userdata'])) {
            $_SESSION['userdata']->setRemember(null);
            $_SESSION['userdata']->save();
        }

        setcookie('remember_token', null, -1);
    }

    /**
     * ### Checks if user is logged in
     *
     * @return bool
     */
    public static function check()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return self::attemptWithToken();
        }

        return true;
    }

    /**
     * ### Sets a remember token to stay logged in
     *
     * @param User $user
     * @param $hash
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
     * ### Checks if remmeber token can auth user
     *
     * @return bool
     */
    private static function attemptWithToken()
    {
        if (!isset($_COOKIE['remember_token'])) return false;

        $token = $_COOKIE['remember_token'];
        $explode = explode('.', $token);
        $model = Config::get('auth', 'providers.db.auth_model');
        $query = new $model();
        $find = $query->findPk($explode[0]);

        if (!$find) return false;
        $stored = $find->getRemember();
        if (!$stored || $stored !== $explode[1]) return false;

        $_SESSION['logged_in'] = true;
        $_SESSION['userdata'] = $find;
        return true;
    }
}