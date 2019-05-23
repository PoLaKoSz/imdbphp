<?php

namespace Imdb\Tests\Integration\DataAccessLayer;

use Imdb\DataAccessLayer\WebClient;
use Imdb\Exception\Http;
use Imdb\Models\WebResponse;
use Imdb\Security\DisabledProxyService;
use Imdb\Config;

use Imdb\Tests\MemoryLogger;
use PHPUnit\Framework\TestCase;

class WebCllientTest extends TestCase
{
    /**
     * @var IWebClient
     */
    private static $webClient;

    /**
     * @var MemoryLogger
     */
    private static $logger;

    /**
     * @var WebResponse
     */
    private static $successfulResponse;

    public static function setUpBeforeClass() : void
    {
        $config       = new Config();
        self::$logger = new MemoryLogger();
        $proxy        = new DisabledProxyService();

        self::$webClient          = new WebClient($config, self::$logger, $proxy);
        self::$successfulResponse = self::$webClient->get('https://www.google.com/');
    }

    public function testGetMethodReturnsCorrectObjectType()
    {
        $this->assertInstanceOf(WebResponse::class, self::$successfulResponse);
    }

    public function testGetLoadsGoogleWithHttp200Status()
    {
        $this->assertEquals(200, self::$successfulResponse->getStatus());
    }

    public function testGetLoadsGoogleWithContentTypeHtml()
    {
        $this->assertEquals('text/html; charset=UTF-8', self::$successfulResponse->getContentType());
    }

    public function testGetLoadsGoogleWithValidBody()
    {
        $bodyLength = strlen(self::$successfulResponse->getBody());
        
        $this->assertGreaterThan(100, $bodyLength);
    }

    public function testValidateGoogleRequestLogMessages()
    {
        $expected = [
            'info' => [
                ['[WebClient] Requesting https://www.google.com/', []],
            ],
        ];

        $this->assertEquals($expected, self::$logger->logs);
    }

    public function testInvalidUrlThrowsHttpException()
    {
        $url = 'http://polakosz.000webhostapp.com/asdfasdfasdf';

        $this->expectException(Http::class);
        $this->expectExceptionMessage("Failed to retrieve $url. Status code 404");

        self::$webClient->get($url);
    }

    public function testInvalidUrlLogsError()
    {
        $url = 'http://polakosz.000webhostapp.com/asdfasdfasdf';
        $logger = new MemoryLogger();
        $expected = [
            'info' => [
                [ "[WebClient] Failed to retrieve $url. Response headers: ", [] ],
            ],
        ];

        $this->expectException(Http::class);
        self::$webClient->get($url);

        $this->assertAreEquals($expected, $logger->logs);
    }
}
