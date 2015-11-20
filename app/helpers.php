<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Gets the full path to the app directory
 *
 * @return string
 */
function appdir()
{
    return realpath(__DIR__ . '/').'/';
}

/**
 * ### Gets the full path to the app/Http directory
 *
 * @return string
 */
function httpdir()
{
    return realpath(appdir() . 'Http/').'/';
}

/**
 * ### Gets the full path to the template directory
 *
 * @return string
 */
function viewdir()
{
    return realpath(httpdir() . 'resources/views/').'/';
}

/**
 * ### Gets the full path to the cache directory
 *
 * @return string
 */
function cachedir()
{
    return realpath(appdir() . '../bootstrap/cache') . '/';
}

/**
 * ### Gets the full path to the filecache directory
 *
 * @return string
 */
function filecachedir()
{
    return realpath(cachedir() . 'file/').'/';
}

/**
 * ### Gets the full path to a config file
 *
 * @return string
 */
function conf($name)
{
    return realpath(appdir() . 'config/' . $name . '.php');
}

/**
 * ### Gets a value from the current session storage
 *
 * @param string $val
 * @return mixed
 */
function session_get($val)
{
    if (isset($_SESSION) && isset($_SESSION[$val])) {
        return $_SESSION[$val];
    } else {
        return null;
    }
}

/**
 * ### Gets an environment file and returns the default if it doesn't exist
 *
 * @param string $val
 * @param mixed $default
 * @return null|string
 */
function env($val, $default = null)
{
    $get = getenv($val);
    if (!$get) {
        return $default;
    }

    return $get;
}

/**
 * ### Gets the current CSRF-Token
 *
 * @return string
 */
function csrf_token()
{
    return session_get('csrf_token');
}

/**
 * ### Pretty prints a variable
 *
 * @param mixed $var
 */
function pr($var = null)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

/**
 * ### Returns a random string
 *
 * @param int $length
 * @return string
 */
function str_rand($length = 16)
{
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

/**
 * ### Gets a human readable filesize
 *
 * @param $file
 * @param int $decimals
 * @return string
 */
function h_filesize($file, $decimals = 2)
{
    $bytes = filesize($file);
    $sz = 'BKMGTP';
    $factor = intval(floor((strlen($bytes) - 1) / 3));
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/**
 * ### Adds a preceding 0 to a value lower than 10
 *
 * @param mixed $val
 * @return string
 */
function decimalConvert($val)
{
    if ($val < 10) return '0' . $val;
    return $val;
}

/**
 * ### Retrieves a route parameter
 *
 * @param string $name
 * @return mixed
 */
function input_r($name)
{
    return \Manager\Request\Input::route($name);
}