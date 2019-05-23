<?php

namespace Imdb\Models;

class Gross
{
    /**
     * In the USA.
     * 
     * @var int
     */
    private $opening;

    /**
     * @var int Unix timestamp
     */
    private $openingDate;

    /**
     * @var int
     */
    private $totalUSA;

    /**
     * @var int
     */
    private $worldWide;
}
