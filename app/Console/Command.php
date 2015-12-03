<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Console;

use Blivy\Support\Config;

class Command
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Command constructor.
     * ### Sets input parameters
     */
    public function __construct()
    {
        global $argv;
        $this->setParameters($argv);
    }

    /**
     * ### Handles the command
     */
    public function handle()
    {
        $this->routeCommand();
    }

    /**
     * ### Runs the requested command
     *
     * @return mixed
     */
    private function routeCommand()
    {
        if (!isset($this->parameters['commands'][0])) {
            $help = new Help();
            $help->handle();
            die();
        }

        $cmd = $this->parameters['commands'][0];
        $commands = Config::get('console', 'commands');

        if (!array_key_exists($cmd, $commands)) {
            die('Command "' . $cmd . '" does not exist or is not configured properly.');
        }
        $class = $commands[$cmd]['class'];

        $run = new $class();
        return $run->handle();
    }

    /**
     * ### Sets up the parameter array
     *
     * @param $argv
     */
    private function setParameters($argv)
    {
        array_shift($argv);
        $commands = [];
        $options = [];
        foreach ($argv as $argument) {
            if (substr($argument, 0, 2) == '--') {
                $argument = substr($argument, 2);
                if (strpos($argument, '=')) {
                    $pairs = explode('=', $argument);
                } else {
                    $pairs[0] = $argument;
                    $pairs[1] = true;
                }

                $options[$pairs[0]] = $pairs[1];
            } else {
                array_push($commands, $argument);
            }
        }
        $this->parameters['commands'] = $commands;
        $this->parameters['options'] = $options;
    }

    /**
     * ### Gets an input parameter
     *
     * @param $str
     * @return null
     */
    protected function getParameter($str)
    {
        if (isset($this->parameters['options']) && isset($this->parameters['options'][$str])) {
            return $this->parameters['options'][$str];
        } else {
            return null;
        }
    }
}

class Help
{
    /**
     * ### Prints the help
     */
    public function handle()
    {
        echo "Welcome to the Blivy command line interface.\n";
        echo "This tool can handle pre-installed and custom commands.\n";
        echo "To find out how to customize it, check out our wiki:\n";
        echo "https://github.com/JanFoerste/blivy/wiki\n\n";
        echo "The following commands are available:\n\n";

        $padding = 15;
        $commands = Config::get('console', 'commands');
        foreach ($commands as $command => $conf) {
            $sp = $padding - strlen($command);
            $pad = $sp > 0 ? str_repeat(' ', $sp) : ' ';

            echo $command . $pad . '=>   ' . $conf['description'] . "\n";
        }
    }
}