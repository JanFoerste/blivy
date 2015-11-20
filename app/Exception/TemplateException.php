<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


class TemplateException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 404, null, []);
    }
}