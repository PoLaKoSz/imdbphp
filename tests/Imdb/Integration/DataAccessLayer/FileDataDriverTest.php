<?php

namespace Imdb\Tests\Integration\DataAccessLayer;

use Imdb\DataAccessLayer\FileDataDriver;
use Imdb\Services\Compressor;
use Imdb\Logger;

use Imdb\Tests\Fakes\FakeCompressor;
use PHPUnit\Framework\TestCase;

class FileDataDriverTest extends TestCase
{
    /**
     * @var FileDataDriverTest
     */
    private static $fileDataDriver;

    /**
     * @var string
     */
    private static $fileName;

    public static function setUpBeforeClass() : void
    {
        $compressor = new FakeCompressor();
        $logger     = new Logger(false);

        self::$fileDataDriver = new FileDataDriver($compressor, $logger);
        self::$fileName = 'naming.is.hard';
    }

    public function testLoadThrowsExceptionWhenFileDoesNotExists()
    {
        $this->expectException(\Exception::class);

        self::$fileDataDriver->load('path/to/file');
    }

    public function testLoadCanReadFile()
    {
        $fileData = 'file data';
        self::$fileDataDriver->save(self::$fileName, $fileData);

        $actualData = self::$fileDataDriver->load(self::$fileName);

        $this->assertEquals($fileData, $actualData);
    }

    public function testSaveCanSaveFile()
    {
        self::$fileDataDriver->save(self::$fileName, 'file data');

        $this->assertTrue(file_exists(self::$fileName));
    }

    protected function tearDown() : void
    {
        if (file_exists(self::$fileName))
        {
            unlink(self::$fileName);
        }
    }
}
