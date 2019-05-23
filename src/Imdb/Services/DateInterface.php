<?php

namespace Imdb\Services;

interface DateInterface
{
    /**
     * Gets the current time.
     * 
     * @return int Unix timestamp.
     */
    public function now() : int;
}
