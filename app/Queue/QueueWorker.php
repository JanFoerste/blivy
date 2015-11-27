<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue;


use Blivy\Queue\Drivers\Connector;

class QueueWorker
{
    protected $daemon;
    protected $tries;
    protected $sleep;

    public function __construct($daemon = false, $tries = 3, $sleep = 5)
    {
        $this->daemon = $daemon;
        $this->tries = $tries;
        $this->sleep = $sleep;
    }

    public function tryJob()
    {
        $driver = Connector::get();
        $next = $driver->getNextJob();
        if (!$next) return 'retry';
        $driver->del($next[0]);
        $job = unserialize($next[1]);
        try {
            $job->handle();
        } catch (\Exception $e) {
            if ($job->tries >= $this->tries) {
                $this->fail($next[0]);
                return 'fail';
            } else {
                $this->retry($next[0], $job);
                return 'retry';
            }
        }

        return true;
    }

    public function fail($key)
    {
        $driver = Connector::get();
        $driver->failJob($key);
    }

    public function retry($key, $job)
    {
        $driver = Connector::get();
        $driver->addFail($key, $job);
    }
}