<?php

namespace Imdb\Models;

class BoxOffice
{
    /**
     * @var int
     */
    private $budget;

    /**
     * https://www.imdb.com/title/tt0087544/ does not have this section!!
     * 
     * @var Imdb\Models\Gross
     */
    private $gross;
}
