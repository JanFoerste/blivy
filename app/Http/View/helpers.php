<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Returns the full path for a template
 *
 * @param string $tpl
 * @return string
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