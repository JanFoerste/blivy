<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


class MailException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}