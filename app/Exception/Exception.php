<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Exception extends \Exception
{
    public function __construct($message, $code = 500, Exception $previous = null, $traits = [])
    {
        parent::__construct($message, $code, $previous);
        $this->setErrorTraits($traits);
    }

    public function setErrorTraits($traits)
    {
        $run = new Run();
        $handler = new PrettyPageHandler();

        $handler->addDataTable('Traits', $traits);
        $run->pushHandler($handler);
        $run->register();
    }
}