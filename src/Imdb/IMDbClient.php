<?php

namespace Imdb;

use Imdb\DataAccessLayer\FileDataDriver;
use Imdb\DataAccessLayer\DataDriver;
use Imdb\DataAccessLayer\IWebClient;
use Imdb\DataAccessLayer\WebClient;
use Imdb\Security\ProxyService;
use Imdb\Services\FileCache;
use Imdb\Services\Compressor;
use Imdb\Services\DataService;
use Imdb\Services\Date;
use Imdb\Services\DateInterface;
use Imdb\Services\ICompressor;
use Imdb\Services\IDataService;
use Imdb\Logger;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class IMDbClient
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProxyService
     */
    private $proxy;

    /**
     * Class that will handle the HTTP requests.
     * 
     * @var IWebClient
     */
    private $webClient;

    private $dataDriver;
    private $cache;
    private $dataService;

    public function __construct(Config $config)
    {
        $this->logger = $this->initializeLogger();

        $this->proxy = $this->initializeProxy();

        $this->webClient = $this->initializeWebClient($config, $this->logger, $this->proxy);

        $compressor = $this->initializeCompressor();

        $this->dataDriver = $this->initializeDataDriver($compressor, $this->logger);

        $date = $this->initializeDateService();

        $this->cache = $this->initializeCache($this->dataDriver, $config, $this->logger, $date);

        $this->dataService = $this->initializeDataService($this->dataDriver, $config, $this->webClient, $this->logger);
    }

    protected function initializeLogger() : LoggerInterface
    {
        $isEnabled = false;
        return new Logger($isEnabled);
    }

    protected function initializeProxy() : ProxyService
    {
        // disabled proxy
        return new ProxyService("", -1, "", "");
    }

    protected function initializeWebClient(Config $config, LoggerInterface $logger, ProxyService $proxy) : IWebClient
    {
        return new WebClient($config, $logger, $proxy);
    }

    protected function initializeCompressor() : ICompressor
    {
        return new Compressor();
    }

    protected function initializeDataDriver(ICompressor $compressor, LoggerInterface $logger) : DataDriver
    {
        return new FileDataDriver($compressor, $logger);
    }

    protected function initializeDateService() : DateInterface
    {
        return new Date();
    }

    /**
     * @throws \Exception Occurs when the $dataDriver can not be initialized.
     */
    protected function initializeCache(DataDriver $dataDriver, Config $config, LoggerInterface $logger, DateInterface $date) : CacheInterface
    {
        $dataDriver->setup($config->cachedir);

        return new FileCache($dataDriver, $logger, $config->cache_expire, $date);
    }

    protected function initializeDataService(DataDriver $dataDriver, Config $config, IWebClient $webClient, LoggerInterface $logger) : IDataService
    {
        return new DataService($dataDriver, $config, $webClient, $logger);
    }
}
