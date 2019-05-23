<?php

namespace Imdb\Tests\Integration\Parsers;

use Imdb\Config;
use Imdb\DataAccessLayer\FileDataDriver;
use Imdb\DataAccessLayer\WebClient;
use Imdb\Models\Movie;
use Imdb\Models\Rating;
use Imdb\Parsers\TitleParser;
use Imdb\Security\DisabledProxyService;
use Imdb\Services\Compressor;

use Imdb\Tests\Integration\Models\MatrixTitle;
use Imdb\Tests\Fakes\FakeCompressor;
use Imdb\Tests\MemoryLogger;
use PHPUnit\Framework\TestCase;

class TitleParserTest extends TestCase
{
    /**
     * This field will help to load the files
     * only once and not every time the
     * titleProvider() method called.
     * 
     * @var array
     */
    private static $titles;

    public static function setUpBeforeClass() : void
    {
        self::$titles = [
            'matrix' => [new MatrixTitle(), self::loadActual('title.tt0133093')],
        ];
    }

    public function DownloadSampleTitle()
    {
        $config = new Config();
        $logger = new MemoryLogger();
        $proxy  = new DisabledProxyService();

        $webClient          = new WebClient($config, $logger, $proxy);
        $successfulResponse = $webClient->get('https://www.imdb.com/title/tt0133093/');

        $compressor     = new FakeCompressor();
        $fileDataDriver = new FileDataDriver($compressor, $logger);
        $fileDataDriver->save('matrix.html', $successfulResponse->getBody());
    }

    /**
     * @dataProvider titleProvider
     */
    public function CanParseTitle(Movie $expected, Movie $actual)
    {
        $this->assertEquals($expected->getTitle(), $actual->getTitle());
    }

    /**
     * @dataProvider titleProvider
     */
    public function CanParseYear(Movie $expected, Movie $actual)
    {
        $this->assertEquals($expected->getYear(), $actual->getYear());
    }

    /**
     * @dataProvider titleProvider
     */
    public function CanParseCertificate(Movie $expected, Movie $actual)
    {
        $this->assertEquals($expected->getCertificate(), $actual->getCertificate());
    }

    /**
     * @dataProvider titleProvider
     */
    public function CanParseOriginalTitle(Movie $expected, Movie $actual)
    {
        $this->assertEquals($expected->getOriginalTitle(), $actual->getOriginalTitle());
    }

    /**
     * @dataProvider titleProvider
     */
    public function CanParseRatings(Movie $expected, Movie $actual)
    {
        $expected = $expected->getRatings();
        $actual = $actual->getRatings();

        $this->assertEquals($expected->getCount(), $actual->getCount(), 'count');
        $this->assertEquals($expected->getBest(), $actual->getBest(), 'best');
        $this->assertEquals($expected->getWorst(), $actual->getWorst(), 'worst');
        $this->assertEquals($expected->getCurrent(), $actual->getCurrent(), 'current');
    }

    /**
     * @dataProvider titleProvider
     */
    public function testCanParseRecommendations(Movie $expected, Movie $actual)
    {
        /*$expected = $expected->getRatings();
        $actual = $actual->getRatings();

        $this->assertEquals($expected->getCount(), $actual->getCount(), 'count');
        $this->assertEquals($expected->getBest(), $actual->getBest(), 'best');
        $this->assertEquals($expected->getWorst(), $actual->getWorst(), 'worst');
        $this->assertEquals($expected->getCurrent(), $actual->getCurrent(), 'current');*/
    }

    public static function titleProvider()
    {
        return self::$titles;
    }

    private static function loadActual(string $fileName) : Movie
    {
        $compressor = new FakeCompressor();
        $logger     = new MemoryLogger();

        $dataDriver = new FileDataDriver($compressor, $logger);
        $html       = $dataDriver->load('tests/Imdb/Integration/SavedResources/Titles/' . $fileName);

        $parser         = new TitleParser();
        return $parser->parse($html);
    }
}
