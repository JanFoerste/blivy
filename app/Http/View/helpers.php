<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

function templatePath($tpl)
{
    if (!strpos($tpl, ':')) {
        $path = viewdir() . $tpl . '.tpl.php';
    } else {
        $explode = explode(':', $tpl);
        $path = viewdir() . $explode[0] . '/' . $explode[1] . '.tpl.php';
    }

    return $path;
}