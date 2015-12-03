<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Console;


use Blivy\Queue\Drivers\Connector;
use Blivy\Queue\QueueWorker;

class Queue extends Command
{
    /**
     * @var bool
     */
    protected $daemon;

    /**
     * @var int
     */
    protected $sleep;

    /**
     * @var int
     */
    protected $tries;

    /**
     * ### Handles the queue command
     */
    public function handle()
    {
        $this->daemon = boolval($this->getParameter('daemon'));
        $this->sleep = $this->getParameter('sleep') ? intval($this->getParameter('sleep')) : 3;
        $this->tries = $this->getParameter('tries') ? intval($this->getParameter('tries')) : 3;

        switch ($this->getParameter('failed')) {
            case 'retry':
                $this->retryFailed();
                break;
            case 'flush':
                $this->flushFailed();
                break;
            default:
                $this->work();
        }
    }

    /**
     * ### Works on the next queue job
     */
    private function work()
    {
        $worker = new QueueWorker($this->tries);
        $try = $worker->tryJob();

        switch ($try) {
            case -1:
                echo 'Error in queue job. Check the error log for more info.' . "\n";
                $this->daemon();
                break;
            case 0:
                $this->wait();
                break;
            case 1:
                echo 'Processed job!' . "\n";
                $this->daemon();
                break;
            case 2:
                $this->daemon();
                break;
        }
    }

    /**
     * ### Pushes all failed jobs back to the queue
     */
    private function retryFailed()
    {
        $driver = Connector::get();
        $failed = $driver->getFailedJobs();
        foreach ($failed as $item) {
            $val = $driver->get($item);
            $driver->addToQueue($val);
            $driver->del($item);
        }

        echo 'Pushed failed jobs back to queue.';
    }

    /**
     * ### Removes all failed jobs
     */
    private function flushFailed()
    {
        $driver = Connector::get();
        $failed = $driver->getFailedJobs();
        foreach ($failed as $item) {
            $driver->del($item);
        }

        echo 'Cleared failed jobs.';
    }

    /**
     * ### Waits for the given amount of time
     */
    private function wait()
    {
        sleep($this->sleep);
        $this->work();
    }

    /**
     * ### Waits if daemon parameter is set, else stops the execution
     */
    private function daemon()
    {
        if ($this->daemon) {
            $this->wait();
        } else {
            die();
        }
    }
}