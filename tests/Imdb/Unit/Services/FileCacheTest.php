<?php

namespace Imdb\Tests\Unit\Services;

use Imdb\Services\FileCache;

use Imdb\Tests\MemoryLogger;
use Imdb\Tests\Fakes\FakeFileDataDriver;
use Imdb\Tests\Fakes\FakeDate;

use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    /**
     * @var MemoryLogger
     */
    private $logger;

    /**
     * @var FakeDate
     */
    private $dateService;

    /**
     * @var FileCache
     */
    private $cache;

    /**
     * @var string
     */
    private $notExistingKey;

    /**
     * @var string
     */
    private $existingKey;

    /**
     * @var string
     */
    private $cacheData;

    /**
     * @var int
     */
    private $expire;

    protected function setup() : void
    {
        $this->dateService = new FakeDate();
        $dataDriver        = new FakeFileDataDriver($this->dateService);
        $this->logger      = new MemoryLogger();
        $this->expire      = 10;

        $this->notExistingKey = 'not.existing.key';
        $this->existingKey    = 'existing.key';
        $this->cacheData      = 'test data for cache';

        $this->cache = new FileCache($dataDriver, $this->logger, $this->expire, $this->dateService);
    }

    public function testSetCanSaveCache()
    {
        $this->cache->set($this->existingKey, $this->cacheData);

        $actual = $this->cache->get($this->existingKey);
        $this->assertEquals($this->cacheData, $actual);
    }

    public function testSetLogsDebugInfo()
    {
        $expected = [
            'debug' => [
                ['[FileCache] Saving key: ' . $this->existingKey, []],
            ]
        ];

        $this->cache->set($this->existingKey, $this->cacheData);

        $this->assertEquals($expected, $this->logger->logs);
    }

    public function testSetUpdatesExistingCache()
    {
        $this->setupCache();

        $this->cache->set($this->existingKey, 'updated cache data');

        $actual = $this->cache->get($this->existingKey);
        $this->assertEquals('updated cache data', $actual);
    }

    public function testGetReturnsDefaultValueWhenKeyNotFound()
    {
        $data = $this->cache->get($this->notExistingKey);

        $this->assertNull($data);
    }

    public function testGetLogsDebugInfoWhenKeyNotFound()
    {
        $expected = [
            'debug' => [
                ['[FileCache] Return default value for key: ' . $this->notExistingKey, []],
            ]
        ];

        $this->cache->get($this->notExistingKey);

        $this->assertEquals($expected, $this->logger->logs);
    }

    public function testGetReturnsDataWhenCacheNotExpired()
    {
        $this->setupCache();

        $actual = $this->cache->get($this->existingKey);

        $this->assertEquals($this->cacheData, $actual);
    }

    public function testGetLogsDebugInfoWhenCacheNotExpired()
    {
        $this->setupCache();
        $expected = [
            'debug' => [
                ['[FileCache] Key found: ' . $this->existingKey, []],
            ]
        ];

        $this->cache->get($this->existingKey);

        $this->assertEquals($expected, $this->logger->logs);
    }

    public function testGetReturnsDefaultValueWhenCacheExpired()
    {
        $this->setupExpiredCache();

        $actual = $this->cache->get($this->existingKey);

        $this->assertNull($actual);
    }

    public function testGetDeletesCacheWhenCacheExpired()
    {
        $this->setupExpiredCache();

        $actual = $this->cache->get($this->existingKey);

        $this->assertNull($actual);
    }

    public function testGetLogsDebugInfoWhenCacheExpired()
    {
        $this->setupExpiredCache();
        $expected = [
            'debug' => [
                ['[FileCache] Expired key: ' . $this->existingKey, []],
                ['[FileCache] Delete key: ' . $this->existingKey, []],
                ['[FileCache] Return default value for key: ' . $this->existingKey, []],
            ]
        ];

        $this->cache->get($this->existingKey);

        $this->assertEquals($expected, $this->logger->logs);
    }

    public function testDeleteCanDeleteCache()
    {
        $this->setupCache();

        $this->cache->delete($this->existingKey);

        $actual = $this->cache->get($this->existingKey);
        $this->assertNull($actual);
    }

    public function testDeleteLogsDebugInfo()
    {
        $this->setupCache();
        $expected = [
            'debug' => [
                ['[FileCache] Delete key: ' . $this->existingKey, []],
            ]
        ];

        $this->cache->delete($this->existingKey);

        $this->assertEquals($expected, $this->logger->logs);
    }

    public function testClearCanPurgeTheWholeCache()
    {
        $this->cache->set('key1', $this->cacheData);
        $this->cache->set('key2', $this->cacheData);
        $this->cache->set('key3', $this->cacheData);

        $this->cache->clear();

        $this->assertNull($this->cache->get('key1'));
        $this->assertNull($this->cache->get('key2'));
        $this->assertNull($this->cache->get('key3'));
    }

    public function testClearLogsDebugInfo()
    {
        $this->cache->set('key1', $this->cacheData);
        $this->cache->set('key2', $this->cacheData);
        $this->cache->set('key3', $this->cacheData);
        $expected = [
            'debug' => [
                ['[FileCache] Saving key: key1', []],
                ['[FileCache] Saving key: key2', []],
                ['[FileCache] Saving key: key3', []],
                ['[FileCache] Delete key: key1', []],
                ['[FileCache] Delete key: key2', []],
                ['[FileCache] Delete key: key3', []],
            ]
        ];

        $this->cache->clear();

        $this->assertEquals($expected, $this->logger->logs);
    }

    private function setupCache() : void
    {
        $this->cache->set($this->existingKey, $this->cacheData);

        array_splice($this->logger->logs, 0);
    }

    public function setupExpiredCache() : void
    {
        $this->setupCache();

        $this->dateService->setNow($this->expire + 10);
    }
}
