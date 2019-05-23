<?php

namespace Imdb\Services;

class Date implements DateInterface
{
    /**
     * Gets the current time.
     * 
     * @return int Unix timestamp.
     */
    public function now() : int
    {
        return time();
    }
}
