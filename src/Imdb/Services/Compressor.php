<?php

namespace Imdb\Services;

class Compressor implements ICompressor
{
    /**
     * The level of compression. Can be given as 0 for no compression up to 9 for maximum compression.
     * If -1 is used, the default compression of the zlib library is used which is 6.
     * @var int
     */
    private $compressLevel;

    public function __construct()
    {
        $this->compressLevel = -1;
    }

    public function compress(string $data) : string
    {
        return gzcompress($data, $this->compressLevel);
    }

    public function deCompress(string $data) : string
    {
        return gzuncompress($data);
    }
}
