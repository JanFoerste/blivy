<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Request;


class Response
{
    public static function json($array)
    {
        echo json_encode($array, JSON_PRETTY_PRINT);
        return true;
    }
}