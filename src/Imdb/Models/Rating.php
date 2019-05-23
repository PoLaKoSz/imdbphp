<?php

namespace Imdb\Models;

class Rating
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var float
     */
    private $best;

    /**
     * @var float
     */
    private $worst;

    /**
     * @var float
     */
    private $current;

    public function __construct(int $count, float $current)
    {
        $this->count = $count;
        $this->best = 10.0;
        $this->worst = 1.0;
        $this->current = $current;
    }

    /**
     * @return  int
     */ 
    public function getCount() : int
    {
        return $this->count;
    }

    /**
     * @return  float
     */ 
    public function getBest() : float
    {
        return $this->best;
    }

    /**
     * @return  float
     */ 
    public function getWorst() : float
    {
        return $this->worst;
    }

    /**
     * @return  float
     */ 
    public function getCurrent() : float
    {
        return $this->current;
    }
}
