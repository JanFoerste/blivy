<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

function ext_config($extension)
{
    $path = $extension . '/config.php';
    $conf = include($path);
    return $conf;
}