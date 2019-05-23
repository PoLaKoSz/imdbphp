<?php

namespace Imdb\Tests\Fakes;

use Imdb\DataAccessLayer\DataDriver;
use Imdb\Services\DateInterface;

class FakeFileDataDriver implements DataDriver
{
    /**
     * 2D string array containing the file details.
     * 
     *      key   =>     value
     *   fileName | [ data , editedAt ]
     * 
     * @var array
     */
    public $files;

    private $date;

    public function __construct(DateInterface $dateService)
    {
        $this->date = $dateService;
        $this->setup('');
    }

    public function setup(string $folderPath) : void
    {
        $this->files = array();
    }

    public function save(string $fileName, string $value) : void
    {
        $this->files[$fileName] = [$value, $this->date->now()];
    }

    public function load(string $fileName) : string
    {
        return $this->files[$fileName][0];
    }

    public function editedAt(string $fileName) : int
    {
        return $this->files[$fileName][1];
    }

    public function setEditedAt(string $fileName, int $time) : void
    {
        $this->files[$fileName][1] = $time;
    }

    public function list() : array
    {
        return array_keys($this->files);
    }

    public function delete(string $fileName) : void
    {
        unset($this->files, $fileName);
    }
}

