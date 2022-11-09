<?php

declare(strict_types = 1);

namespace App\Tests;


use App\Domain\CalculateMedian;
use App\Domain\CsvParser;
use App\Domain\JsonParser;
use App\Domain\ParserFactory;
use App\Domain\SuspiciousReadingDetector;
use App\Domain\XmlParser;
use App\Infrastructure\FileFetcher;
use PHPUnit\Framework\TestCase;

class MainUnitTest extends TestCase
{

    private const READING_HEADERS = ['client', 'period', 'reading'];

    public function testCanCreateCsvParser()
    {
        $fileFetcherMock = $this->createMock(FileFetcher::class);

        $fileFetcherMock->expects(self::once())
            ->method('fetch')
            ->willReturn(
                "client,period,reading\n583ef6329d7b9,2016-01,42451"
            );

        $csvParser = CsvParser::parse($fileFetcherMock);
        $this->assertEquals(self::READING_HEADERS, $csvParser->headers());
        $this->assertNotEmpty($csvParser->content());
        $this->assertEquals(CsvParser::TYPE, $csvParser->type());
    }

    public function testCanCreateXmlParser()
    {
        $fileFetcherMock = $this->createMock(FileFetcher::class);

        $fileFetcherMock->expects(self::once())
            ->method('fetch')
            ->willReturn(
                '<readings><reading clientID="583ef6329df6b" period="2016-01">37232</reading></readings>'
            );

        $xmlParser = XmlParser::parse($fileFetcherMock);
        $this->assertEquals(self::READING_HEADERS, $xmlParser->headers());
        $this->assertNotEmpty($xmlParser->content());
        $this->assertEquals(XmlParser::TYPE, $xmlParser->type());
    }

    public function testCanCreateJsonParser()
    {
        $fileFetcherMock = $this->createMock(FileFetcher::class);

        $fileFetcherMock->expects(self::once())
            ->method('fetch')
            ->willReturn(
                '[{"client": "583ef6329d7b9","period": "2016-01","reading": 42451}]'
            );

        $jsonParser = JsonParser::parse($fileFetcherMock);
        $this->assertEquals(self::READING_HEADERS, $jsonParser->headers());
        $this->assertNotEmpty($jsonParser->content());
        $this->assertEquals(JsonParser::TYPE, $jsonParser->type());
    }

    public function testCanCreateFactoryParser()
    {
        $fileFetcherMock = $this->createMock(FileFetcher::class);

        $fileFetcherMock->expects(self::once())
            ->method('fetch')
            ->willReturn(
                '[{"client": "583ef6329d7b9","period": "2016-01","reading": 42451}]'
            );

        $fileFetcherMock->expects(self::once())->method('type')->willReturn('json');


        $parser = ParserFactory::parse($fileFetcherMock);
        $this->assertEquals(self::READING_HEADERS, $parser->headers());
        $this->assertNotEmpty($parser->content());
        $this->assertEquals(JsonParser::TYPE, $parser->type());
    }

    public function testCanCalculateMedian()
    {
        $readings = [
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-01",
                "reading" => 100,
            ],
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-02",
                "reading" => 50
            ],
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-03",
                "reading" => 20
            ]
        ];

        $this->assertEquals(50, CalculateMedian::fromArrayOfReadings($readings)->get());
    }

    public function testCanCalculateReadingDeviationOverOrLowerCustomPercentageValueAgainstMedian()
    {
        $readings = [
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-01",
                "reading" => 100,
            ],
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-02",
                "reading" => 50
            ],
            [
                "client"  => "583ef6329d7b9",
                "period"  => "2016-03",
                "reading" => 20
            ]
        ];

        $suspiciousReadingDetector = new SuspiciousReadingDetector($readings);
        $this->assertNull($suspiciousReadingDetector->calculateMedianDeviationToPeriodsByCustomer(200));
        $this->assertNotNull($suspiciousReadingDetector->calculateMedianDeviationToPeriodsByCustomer(-30));
        $this->assertNotNull($suspiciousReadingDetector->calculateMedianDeviationToPeriodsByCustomer());
    }
}