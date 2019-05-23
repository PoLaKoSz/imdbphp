<?php

namespace Imdb\Services;

use Imdb\DataAccessLayer\IWebClient;
use Imdb\Services\IDataService;
use Imdb\Config;
use Imdb\Title;
use Psr\Log\LoggerInterface;

class DataService implements IDataService
{
    /**
     * @var FileCache
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Config
     */
    private $config;

    public function __construct(FileCache $FileCache, Config $config, IWebClient $webclient, LoggerInterface $logger)
    {
        $this->cache  = $FileCache;
        $this->log    = $logger;
        $this->config = $config;
    }

    /**
     * 
     */
    public function getTitleWith(int $id)
    {
        //$id : unique ID for every data

        $title = $this->cache->get($id, null);

        if ($title == null)
        {
            $title = new Title($id, $this->config);
            $this->cache->set($id, $title, $this->config->cache_expire);
        }

        return $title;
    }
}
