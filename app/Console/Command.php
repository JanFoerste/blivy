<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Console;


class Command
{
    protected $parameters = [];

    public function __construct()
    {
        global $argv;
        $this->setParameters($argv);
        pr($this->parameters);
    }

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

    protected function getParameters()
    {
        return $this->parameters;
    }
}