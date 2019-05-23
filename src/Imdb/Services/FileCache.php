<?php

namespace Imdb\Services;

use Imdb\Config;
use Imdb\DataAccessLayer\DataDriver;
use Imdb\Services\DateInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Cache layer implementation which uses the file system.
 */
class FileCache implements CacheInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataDriver
     */
    private $dataDriver;

    /**
     * @var int
     */
    private $expire;

    /**
     * @var DateInterface
     */
    private $date;

    public function __construct(DataDriver $dataDriver, LoggerInterface $logger, int $expire, DateInterface $date)
    {
        $this->dataDriver = $dataDriver;
        $this->logger     = $logger;
        $this->expire     = $expire;
        $this->date       = $date;
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $cleanKey = $this->sanitiseKey($key);

        try
        {
            $lastEdit = $this->dataDriver->editedAt($cleanKey);

            if ($this->expire < ($this->date->now() - $lastEdit))
            {
                $this->logger->debug("[FileCache] Expired key: $key");
                $this->delete($key);
            }
            else
            {
                $this->logger->debug("[FileCache] Key found: $key");
                return $this->dataDriver->load($cleanKey);
            }
        }
        catch (\Exception $ex) { }

        $this->logger->debug("[FileCache] Return default value for key: $key");
        return $default;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null) : void
    {
        $this->logger->debug("[FileCache] Saving key: $key");

        $cleanKey = $this->sanitiseKey($key);

        $this->dataDriver->save($cleanKey, $value, $ttl);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->logger->debug("[FileCache] Delete key: $key");

        $cleanKey = $this->sanitiseKey($key);

        $this->dataDriver->delete($cleanKey);
    }

    /**
     * @inheritdoc
     */
    public function clear() : void
    {
        $items = $this->dataDriver->list();

        foreach ($items as $item)
        {
            $this->delete($item);
        }
    }

    /**
     * @inheritdoc
     */
    public function getMultiple($keys, $default = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function setMultiple($values, $ttl = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function deleteMultiple($keys)
    {
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
    }

    /**
     * Replace characters the OS won't like using with the filesystem
     */
    private function sanitiseKey($key) : string
    {
        return str_replace(array('/', '\\', '?', '%', '*', ':', '|', '"', '<', '>'), '.', $key);
    }
}
