<?php

namespace Imdb\Services;

interface ICompressor
{
    public function compress(string $data) : string;

    public function deCompress(string $data) : string;
}
