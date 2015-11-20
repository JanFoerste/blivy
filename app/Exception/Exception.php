<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Exception extends \Exception
{
    /**
     * @var Logger
     */
    protected $log;

    public function __construct($message, $code = 500, Exception $previous = null, $traits = [])
    {
        parent::__construct($message, $code, $previous);
        $this->registerLogging();
        $this->log($message, parent::getFile(), parent::getLine());
        if (conf('app.debug')) {
            $this->setErrorTraits($traits);
        } else {
            die('Whoops, looks like we encountered an error!');
        }
    }

    public function setErrorTraits($traits)
    {
        $run = new Run();
        $handler = new PrettyPageHandler();

        $handler->addDataTable('Traits', $traits);
        $run->pushHandler($handler);
        $run->register();
    }

    public function registerLogging()
    {
        $this->log = new Logger('default');
        $this->log->pushHandler(new StreamHandler(logdir() . 'error.log', Logger::ERROR));
    }

    public function log($msg, $file, $line)
    {
        $this->log->addError($msg, [$file => $line]);
    }
}