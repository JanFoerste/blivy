<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Auth;

interface AuthInterface
{
    public static function attempt($user, $password, $remember = false);

    public static function clearSession();

    public static function check();
}