<?php

namespace Imdb\Tests\Fakes;

use Imdb\Services\DateInterface;

class FakeDate implements DateInterface
{
    /**
     * @var int
     */
    private $now;

    public function __construct()
    {
        $this->now = 0;
    }

    public function now() : int
    {
        return $this->now;
    }

    public function setNow(int $date) : void
    {
        $this->now = $date;
    }
}
