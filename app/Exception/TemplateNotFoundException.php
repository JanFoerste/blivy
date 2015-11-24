<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Exception;


class TemplateNotFoundException extends Exception
{
    public function __construct($tpl)
    {
        $message = 'The template ' . $tpl . ' could not be found!';
        parent::__construct($message, 404, null, []);
    }
}