<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

function appdir()
{
    return realpath(__DIR__ . '/').'/';
}

function httpdir()
{
    return realpath(appdir() . 'Http/').'/';
}

function viewdir()
{
    return realpath(httpdir() . 'resources/views/').'/';
}

function cachedir()
{
    return realpath(appdir() . '../bootstrap/cache') . '/';
}

function filecachedir()
{
    return realpath(cachedir() . 'file/').'/';
}

function conf($name)
{
    return realpath(appdir() . 'config/' . $name . '.php');
}

function session_get($val)
{
    if (isset($_SESSION) && isset($_SESSION[$val])) {
        return $_SESSION[$val];
    } else {
        return null;
    }
}

function env($val, $default = null)
{
    $get = getenv($val);
    if (!$get) {
        return $default;
    }

    return $get;
}

function csrf_token()
{
    return session_get('csrf_token');
}

function pr($array = null)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function str_rand($length = 16)
{
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

function h_filesize($file, $decimals = 2)
{
    $bytes = filesize($file);
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function decimalConvert($val)
{
    if ($val < 10) return '0' . $val;
    return $val;
}

function input_r($name)
{
    return \Manager\Request\Input::route($name);
}