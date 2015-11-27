<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Drivers;


use Blivy\Exception\RedisException;
use Blivy\Support\Config;
use Predis\Client;

class RedisQueueDriver
{
    /**
     * @var Client
     */
    protected $connection;

    public function __construct()
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => Config::get('queue', 'connections.redis.host'),
            'port' => Config::get('queue', 'connections.redis.port'),
            'database' => Config::get('queue', 'connections.redis.database')
        ]);

        try {
            $client->connect();
        } catch (\Exception $e) {
            throw new RedisException('Connection to redis server failed. Please check your settings.');
        }

        $this->connection = $client;
    }

    public function addToQueue($value)
    {
        $prefix = Config::get('queue', 'key_prefix');
        $now = new \DateTime();
        $key = $prefix . $now->format('Y-m-d_H:i:s');
        $this->connection->set($key, $value);
        return true;
    }

    public function getNextJob()
    {
        $prefix = Config::get('queue', 'key_prefix');
        $all = $this->connection->keys($prefix . '*');
        if(count($all) < 1) {
            return null;
        }
        $first = $this->connection->get($all[0]);
        return [$all[0], $first];
    }

    public function getFailedJobs()
    {
        $prefix = Config::get('queue', 'failed_prefix');
        $all = $this->connection->keys($prefix . '*');
        return $all;
    }

    public function failJob($key)
    {
        $prefix = Config::get('queue', 'failed_prefix');
        $now = new \DateTime();
        $id = $prefix . $now->format('Y-m-d_H:i:s');

        $job = $this->connection->get($key);
        $this->connection->del($key);
        $this->connection->set($id, $job);
    }

    public function addFail($key, $job)
    {
        $job->tries++;
        $this->connection->set($key, serialize($job));
    }

    public function del($key)
    {
        $this->connection->del($key);
    }

}