<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Cache;


use Blivy\Exception\Exception;
use Blivy\Support\Config;

/**
 * ### File caching driver
 *
 * Class FileCache
 * @package Manager\Cache
 */
class FileCache
{
    /**
     * ### Makes sure the map file exists and is writable
     *
     * FileCache constructor.
     */
    public function __construct()
    {
        $this->writable(filecachedir() . 'cachemap');
        if (!file_exists(filecachedir() . 'cachemap')) {
            file_put_contents(filecachedir() . 'cachemap', '{}');
        }
    }

    /**
     * ### Gets cache file id work with
     *
     * @return string
     */
    private function workId()
    {
        $prefix = Config::get('cache', 'key_prefix');
        $base = '00';
        while (file_exists(filecachedir() . $prefix . $base)) {
            if (filesize(filecachedir() . $prefix . $base) > 1e8) {
                $base = decimalConvert(intval($base) + 1);
            } else {
                break;
            }
        }
        return $base;
    }

    /**
     * ### Returns fill path for the current cache file
     *
     * @return string
     */
    private function workFile()
    {
        $prefix = Config::get('cache', 'key_prefix');
        return filecachedir() . $prefix . $this->workId();
    }

    /**
     * ### Searches the map for the given key
     *
     * @param string $key
     * @return null|string
     */
    private function searchInMap($key)
    {
        $map = json_decode(file_get_contents(filecachedir() . 'cachemap'));
        if (!isset($map->$key)) return null;
        return filecachedir() . Config::get('cache', 'key_prefix') . $map->$key;
    }

    /**
     * ### Checks map if key exists
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        $file = $this->searchInMap($key);
        if (!$file) return false;
        return true;
    }

    /**
     * ### Sets a new or replaces a set key/value pair
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        $file = $this->searchInMap($key);
        $value = serialize($value);
        if ($file) {
            return $this->replace($file, $key, $value);
        } else {
            return $this->write($key, $value);
        }
    }

    /**
     * ### Replaces the given key/value pair
     *
     * @param string $file
     * @param string $key
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    private function replace($file, $key, $value)
    {
        $arr = json_decode(file_get_contents($file));
        $arr->$key = $value;
        $this->writable($file);
        file_put_contents($file, json_encode($arr));
        return true;
    }


    /**
     * ### Writes a new key/value pair
     *
     * @param $key
     * @param $value
     * @return bool
     * @throws Exception
     */
    private function write($key, $value)
    {
        $file = $this->workFile();
        if (file_exists($file)) {
            $content = json_decode(file_get_contents($file), true);
        } else {
            $content = [];
        }

        $content[$key] = $value;
        $this->appendToMap($key, $this->workId());
        $this->writable($file);
        file_put_contents($file, json_encode($content));
        return true;
    }

    /**
     * ### Appends the key to the cache file id to locate the value quicker
     *
     * @param string $key
     * @param string $id
     * @throws Exception
     */
    private function appendToMap($key, $id)
    {
        $current = json_decode(file_get_contents(filecachedir() . 'cachemap'), true);
        $current[$key] = $id;
        $this->writable(filecachedir() . 'cachemap');
        file_put_contents(filecachedir() . 'cachemap', json_encode($current));
    }

    /**
     * ### Returns the value fitting to the key
     *
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        $file = $this->searchInMap($key);
        if (!$file) return null;
        $content = json_decode(file_get_contents($file));
        return unserialize($content->$key);
    }

    /**
     * ### Flushes the map and removes all cache files
     *
     * @return bool
     * @throws Exception
     */
    public function flush()
    {
        file_put_contents(filecachedir() . 'cachemap', '{}');
        $files = glob(filecachedir() . Config::get('cache', 'key_prefix') . '*', GLOB_BRACE);
        foreach ($files as $file) {
            $this->writable($file);
            unlink($file);
        }
        return true;
    }

    /**
     * ### Checks if file is writable
     *
     * @param string $file
     * @throws Exception
     */
    private function writable($file)
    {
        if (!is_writable($file) && file_exists($file)) throw new Exception('The cache directory is not writable!');
    }
}