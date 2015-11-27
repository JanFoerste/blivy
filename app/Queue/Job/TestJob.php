<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Job;


use Blivy\Exception\TemplateException;
use Blivy\Queue\Queue;

class TestJob extends Queue
{
    protected $print;

    public function __construct($print)
    {
        $this->print = $print;
        parent::__construct();
    }

    public function handle()
    {
        //echo($this->print) . '<br>';
        throw new TemplateException('test exception');
    }
}