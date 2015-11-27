<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue;


use Blivy\Queue\Drivers\Connector;

abstract class Queue
{
    public $store;
    public $tries;
    public $created_at;

    public function __construct()
    {
        $now = new \DateTime();
        $this->created_at = $now->format('Y-m-d H:i:s');
        $this->tries = 0;

        $this->store = $this->serialize();
        $this->storeJob();
    }

    public function serialize()
    {
        return serialize($this);
    }

    private function storeJob()
    {
        $driver = Connector::get();
        $driver->addToQueue($this->store);
    }
}