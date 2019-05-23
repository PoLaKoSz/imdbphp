<?php

namespace Imdb\DataAccessLayer;

use Imdb\Models\WebResponse;

interface IWebClient
{
    public function get(string $url) : WebResponse;
}
