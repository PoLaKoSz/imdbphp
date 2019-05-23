<?php

namespace Imdb\Models;

use Imdb\Models\Movie;

class Show extends Movie
{
    /**
     * @var int
     */
    private $endYear;

    public function __construct()
    {
        $this->endYear = -1;
    }
}
