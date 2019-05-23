<?php

namespace Imdb\Tests\Fakes;

use Imdb\Services\ICompressor;

class FakeCompressor implements ICompressor
{
    public function compress(string $data) : string
    {
        return $data;
    }

    public function deCompress(string $data) : string
    {
        return $data;
    }
}
