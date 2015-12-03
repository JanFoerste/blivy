<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue;


use Blivy\Queue\Drivers\Connector;

class QueueWorker
{
    /**
     * @var int
     */
    protected $tries;

    /**
     * QueueWorker constructor.
     * @param int $tries
     */
    public function __construct($tries = 3)
    {
        $this->tries = $tries;
    }

    /**
     * ### Tries to run the job. Returns integer based on result
     *
     * ### -2 = No job found
     * ### -1 = Job failed too many times
     * ### 0 = Retry the job
     * ### 1 = Success
     *
     * @return int
     */
    public function tryJob()
    {
        $driver = Connector::get();
        $next = $driver->getNextJob();
        if (!$next) return 2;
        $driver->del($next[0]);
        $job = unserialize($next[1]);
        try {
            $job->handle();
        } catch (\Exception $e) {
            if ($job->tries >= $this->tries) {
                $this->fail($job);
                return -1;
            } else {
                $this->retry($next[0], $job);
                return 0;
            }
        }

        return 1;
    }

    /**
     * ### Pushes a failed job to the failed queue list
     *
     * @param $job
     */
    public function fail($job)
    {
        $driver = Connector::get();
        $driver->failJob(serialize($job));
    }

    /**
     * ### Retries a job
     *
     * @param $key
     * @param $job
     */
    public function retry($key, $job)
    {
        $driver = Connector::get();
        $driver->addFail($key, $job);
    }
}