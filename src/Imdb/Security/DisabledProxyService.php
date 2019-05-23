<?php

namespace Imdb\Security;

use Imdb\Security\ProxyService;

class DisabledProxyService extends ProxyService
{
    public function __construct()
    {
        parent::__construct("", -1, "", "");
    }
}
