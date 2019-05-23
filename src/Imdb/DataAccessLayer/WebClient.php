<?php

namespace Imdb\DataAccessLayer;

use Imdb\DataAccessLayer\IWebClient;
use Imdb\Exception\Http;
use Imdb\Models\WebResponse;
use Imdb\Security\ProxyService;
use Imdb\Config;
use Psr\Log\LoggerInterface;

/**
 * Class to pull content from the internet. Uses cURL and
 * supports redirecting.
 */
class WebClient implements IWebClient
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Unique ID for this class while logging.
     * 
     * @var string
     */
    private $tag;

    /**
     * @var resource
     */
    private $cUrlSession;

    private $requestHeaders = array();

    /**
     * @var WebResponse
     */
    private $response;

    public function __construct(Config $config, LoggerInterface $logger, ProxyService $proxyService)
    {
        $this->logger = $logger;
        $this->tag = "WebClient";

        $this->iniTcUrlWith($config, $proxyService);
    }

    /**
     * Returns the specified URL content.
     * 
     * @throws Exception\Http
     */
    public function get(string $url) : WebResponse
    {
        $this->logger->info("[$this->tag] Requesting $url");

        $this->sendRequestTo($url);

        if ($this->response->getStatus() == 200)
        {
            return $this->response;
        }

        if ($redirectURL = $this->response->getRedirectURL($url))
        {
            $this->logger->debug("[$this->tag] Following redirect from $url to $redirectURL");

            return $this->get($redirectURL);
        }
    }

    /**
     * Send a request to the specified site.
     * 
     * @throws Exception\Http Occurs when the request didn't
     *  executed successfully.
     */
    private function sendRequestTo(string $url) : WebResponse
    {
        $this->response = new WebResponse();
        curl_setopt($this->cUrlSession, CURLOPT_URL, $url);
        curl_setopt($this->cUrlSession, CURLOPT_HTTPHEADER, $this->requestHeaders);

        $this->response->setBody(curl_exec($this->cUrlSession));
        $this->response->setStatus(curl_getinfo($this->cUrlSession, CURLINFO_RESPONSE_CODE));

        if ($this->response->getStatus() < 400)
        {
            return $this->response;
        }

        $this->logger->error("[$this->tag] Failed to retrieve {url}. Response headers: {headers}", array('url' => $url, 'headers' => $this->response->getHeaders()));

        throw new Http("Failed to retrieve $url. Status code {$this->response->getStatus()}");
    }

    private function iniTcUrlWith(Config $config, ProxyService $proxyService) : void
    {
        $this->cUrlSession = curl_init();
        curl_setopt($this->cUrlSession, CURLOPT_ENCODING, "");
        curl_setopt($this->cUrlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cUrlSession, CURLOPT_HEADERFUNCTION, array(&$this, "callback_CURLOPT_HEADERFUNCTION"));

        $this->addHeader('Referer', 'https://' . $config->imdbsite . '/');

        if ($config->force_agent)
        {
            curl_setopt($this->cUrlSession, CURLOPT_USERAGENT, $config->force_agent);
        }
        else
        {
            curl_setopt($this->cUrlSession, CURLOPT_USERAGENT, $config->default_agent);
        }

        if ($config->language)
        {
            $this->addHeader('Accept-Language', $config->language);
        }

        if ($config->ip_address)
        {
            $this->addHeader('X-Forwarded-For', $config->ip_address);
        }

        $this->setup($proxyService);
    }

    /**
     * Enables content downloading through proxy if
     * the user enabled it.
     */
    private function setup(ProxyService $proxy) : void
    {
        if ($proxy->isActivated())
        {
            curl_setopt($this->cUrlSession, CURLOPT_PROXY,     $proxy->getHost());
            curl_setopt($this->cUrlSession, CURLOPT_PROXYPORT, $proxy->getPort());

            if ($proxy->shouldAuthenticate())
            {
                curl_setopt($this->cUrlSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($this->cUrlSession, CURLOPT_PROXYUSERPWD, $proxy->getUserName() . ':' . $proxy->getPassword());
            }
        }
    }

    private function callback_CURLOPT_HEADERFUNCTION($cUrlHandler, string $header) : int
    {
        $length = strlen($header);

        if ($length)
        {
            $this->response->add($header);
        }

        return $length;
    }

    private function addHeader(string $name, string $value) : void
    {
        $this->requestHeaders[] = "$name: $value";
    }
}
