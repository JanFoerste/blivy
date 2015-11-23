<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Exception;

use Monolog\Formatter\LineFormatter;
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
        $txt = $message . ' in ' . parent::getFile() . ':' . parent::getLine() . "\n" . $this->generateBacktrace();
        $this->exceptionLog($txt);

        if (conf('app.debug') == 1) {
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
        $formatter = new LineFormatter(null, null, true, true);
        $stream = new StreamHandler(logdir() . 'error.log', Logger::ERROR);
        $stream->setFormatter($formatter);

        $this->log = new Logger('default');
        $this->log->pushHandler($stream);
    }

    public function exceptionLog($msg)
    {
        $this->log->addError($msg);
    }

    public function generateBacktrace()
    {
        $trace = debug_backtrace(0);
        $str = "Backtrace: \n";
        foreach ($trace as $key => $item) {
            $append = '#' . $key . ': ';
            $file = $item['file'] . '(' . $item['line'] . ')';
            $class = isset($item['class']) ? $item['class'] : '';
            $args = count($item['args']) > 0 ? '' : implode(', ', $item['args']);
            $func = '->' . $item['function'] . '(' . $args . ')';

            $str = $str . $append . $file . $class . $args . $func . "\n";
        }

        return $str;
    }
}