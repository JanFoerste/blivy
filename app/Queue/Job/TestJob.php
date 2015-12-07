<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Job;

use Blivy\Queue\Queue;

class TestJob extends Queue implements JobInterface
{
    protected $print;

    /**
     * TestJob constructor.
     * @param $print
     */
    public function __construct($print)
    {
        $this->print = $print;
        parent::__construct();
    }

    /**
     * ### Start the job execution
     */
    public function handle()
    {
        echo($this->print) . '<br>';
    }
}