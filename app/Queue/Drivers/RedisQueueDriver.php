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

    /**
     * RedisQueueDriver constructor.
     * ### Set up connection
     */
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

    /**
     * ### Pushes a new job to the queue
     *
     * @param $value
     * @return bool
     */
    public function addToQueue($value)
    {
        $prefix = Config::get('queue', 'key_prefix');
        $now = new \DateTime();
        $key = $prefix . $now->format('Y-m-d_H:i:s') . microtime();
        $this->connection->set($key, $value);
        return true;
    }

    /**
     * ### Gets the next queue entry
     *
     * @return array|null
     */
    public function getNextJob()
    {
        $prefix = Config::get('queue', 'key_prefix');
        $all = $this->connection->keys($prefix . '*');
        if (count($all) < 1) {
            return null;
        }
        $first = $this->connection->get($all[0]);
        return [$all[0], $first];
    }

    /**
     * ### Returns all failed jobs
     *
     * @return array
     */
    public function getFailedJobs()
    {
        $prefix = Config::get('queue', 'failed_prefix');
        $all = $this->connection->keys($prefix . '*');
        return $all;
    }

    /**
     * ### Pushes a job to the failed array
     *
     * @param $job
     */
    public function failJob($job)
    {
        $prefix = Config::get('queue', 'failed_prefix');
        $now = new \DateTime();
        $id = $prefix . $now->format('Y-m-d_H:i:s') . microtime();

        $this->connection->set($id, $job);
    }

    /**
     * ### Increments the fail iterator
     *
     * @param $key
     * @param $job
     */
    public function addFail($key, $job)
    {
        $job->tries++;
        $this->connection->set($key, serialize($job));
    }

    /**
     * ### Deletes a job
     *
     * @param $key
     */
    public function del($key)
    {
        $this->connection->del($key);
    }

    /**
     * ### Gets a job
     *
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }
}