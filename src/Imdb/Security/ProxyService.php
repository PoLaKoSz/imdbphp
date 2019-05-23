<?php

namespace Imdb\Security;

class ProxyService
{
    private $host;

    private $port;

    private $userName;

    private $password;

    public function __construct(string $host, int $port, string $userName, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->userName = $userName;
        $this->password = $password;
    }

    public function getHost() : string
    {
        return $this->host;
    }

    public function getPort() : int
    {
        return $this->port;
    }

    public function getUserName() : string
    {
        return $this->userName;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * Determinates if the User wants to use proxy.
     */
    public function isActivated() : bool
    {
        return (!empty($this->host) && $this->port != -1);
    }

    /**
     * @return true if the proxy require authentication
     * before the user actually can use it.
     */
    public function shouldAuthenticate() : bool
    {
        return (!empty($this->userName) && !empty($this->password));
    }
}
