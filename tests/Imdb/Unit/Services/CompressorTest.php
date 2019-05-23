<?php

namespace Imdb\Tests\Unit\Services;

use Imdb\Services\Compressor;

use PHPUnit\Framework\TestCase;

class CompressorTest extends TestCase
{
    private static $compressor;

    public static function setUpBeforeClass() : void
    {
        self::$compressor = new Compressor();
    }

    public function testCanCompress()
    {
        $input = 'test text';

        $compressedData = self::$compressor->compress($input);
        $deCompressedData = self::$compressor->deCompress($compressedData);

        $this->assertEquals($input, $deCompressedData);
    }

    public function testCanDeCompress()
    {
        $this->testCanCompress();
    }
}
