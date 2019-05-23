<?php

namespace Imdb\Models;

use Imdb\Exception\Http;

class WebResponse
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $headers;

    /**
     * HTTP status code
     * 
     * @var int
     */
    private $status;

    public function getContentType() : string
    {
        return $this->getHeader('Content-Type');
    }

    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * @throws \Exception When the body have been already set.
     */
    public function setBody(string $value) : void
    {
        if (!isset($this->body))
        {
            $this->body = $value;
        }
        else
        {
            throw new \Exception("Can not set response body twice!");
        }
    }

    /**
     * HTTP status code of the response
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * @throws \Exception When the HTTP status have been already set.
     */
    public function setStatus(int $value) : void
    {
        if (!isset($this->status))
        {
            $this->status = $value;
        }
        else
        {
            throw new \Exception("Can not set response HTTP status code twice!");
        }
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Gets the URL to redirect to if a HTTP 30* status code was returned.
     * 
     * @return string|null URL to redirect to, otherwise null
     */
    public function getRedirectURL(string $originalURL) : string
    {
        $status = $this->getStatus();

        if ($status == 301 || $status == 302 || $status == 303 || $status == 307)
        {
            foreach ($this->headers as $header)
            {
                if (strpos(trim(strtolower($header)), 'location') !== 0)
                {
                    continue;
                }

                $aLine = explode(': ', $header);
                $target = trim($aLine[1]);
                $urlParts = parse_url($target);

                if (!isset($urlParts['host']))
                {
                    $initialURL = parse_url($originalURL);
                    $target = $initialURL['scheme'] . "://" . $initialURL['host'] . $target;
                }

                return $target;
            }
        }
    }

    /**
     * Adds header to the response headers container.
     */
    public function add(string $header) : void
    {
        $this->headers[] = $header;
    }

    /**
     * Get the header value from the response
     * 
     * @param  string $header Name of the desired header.
     * 
     * @return string         Value of the header
     */
    private function getHeader(string $headerName) : string
    {
        foreach ($this->headers as $header)
        {
            if (is_integer(stripos($header, $headerName)))
            {
                $hstart = strpos($header, ": ");

                $header = trim(substr($header, $hstart + 2, 100));

                return $header;
            }
        }

        throw new Http("$headerName does not exists in the response header!");
    }
}
