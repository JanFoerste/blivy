<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue;


use Blivy\Queue\Drivers\Connector;

abstract class Queue
{
    /**
     * @var int
     */
    public $tries;
    /**
     * @var string
     */
    public $created_at;

    /**
     * Queue constructor.
     * ### Set basic parameters
     */
    public function __construct()
    {
        $now = new \DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->tries = 0;

        $this->storeJob();
    }

    /**
     * ### Store the job in the driver
     */
    private function storeJob()
    {
        $driver = Connector::get();
        $driver->addToQueue(serialize($this));
    }
}