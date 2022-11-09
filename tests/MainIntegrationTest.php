<?php

declare(strict_types = 1);


namespace App\Tests;


use App\Application\GetReadingsFromStreamUseCase;
use App\Application\GetSuspiciousReadingsUseCase;
use App\Domain\CsvParser;
use App\Domain\JsonParser;
use App\Domain\ParserFactory;
use App\Domain\XmlParser;
use App\Infrastructure\FileFetcher;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainIntegrationTest extends WebTestCase
{
    private string $testStorageFilesPath;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug'       => false,
        ]);

        $this->testStorageFilesPath = __DIR__ . '/storage/';
    }

    public function testCanReadCsvFromFile()
    {
        $data = new FileFetcher('2016-readings.csv', $this->testStorageFilesPath);
        $this->assertNotEmpty($data->fetch());
    }

    public function testCanParseCsvFromFile()
    {
        $expectedHeaders = ['client', 'period', 'reading'];

        $data = new FileFetcher('2016-readings.csv', $this->testStorageFilesPath);

        $csvParser = CsvParser::parse($data, ',');
        $this->assertEquals('csv', $csvParser->type());
        $this->assertNotEmpty($csvParser->content());
        $this->assertIsArray($csvParser->content());
        $this->assertNotEmpty($csvParser->headers());
        $this->assertIsArray($csvParser->headers());
        $this->assertEquals($csvParser->headers(), $expectedHeaders);
    }

    public function testCanReadXmlFromFile()
    {
        $data = new FileFetcher('2016-readings.xml', $this->testStorageFilesPath);
        $this->assertNotEmpty($data->fetch());
    }

    public function testCanParseXmlFromFile()
    {
        $expectedHeaders = ['client', 'period', 'reading'];

        $data = new FileFetcher('2016-readings.xml', $this->testStorageFilesPath);

        $xmlParser = XmlParser::parse($data);
        $this->assertEquals('xml', $xmlParser->type());
        $this->assertNotEmpty($xmlParser->content());
        $this->assertIsArray($xmlParser->content());
        $this->assertNotEmpty($xmlParser->headers());
        $this->assertIsArray($xmlParser->headers());
        $this->assertEquals($xmlParser->headers(), $expectedHeaders);
    }

    public function testCanReadJsonFromFile()
    {
        $data = new FileFetcher('2016-readings.json', $this->testStorageFilesPath);
        $this->assertNotEmpty($data->fetch());
    }

    public function testCanParseJsonFromFile()
    {
        $expectedHeaders = ['client', 'period', 'reading'];

        $data = new FileFetcher('2016-readings.json', $this->testStorageFilesPath);

        $jsonParser = JsonParser::parse($data);
        $this->assertEquals('json', $jsonParser->type());
        $this->assertNotEmpty($jsonParser->content());
        $this->assertIsArray($jsonParser->content());
        $this->assertNotEmpty($jsonParser->headers());
        $this->assertIsArray($jsonParser->headers());
        $this->assertEquals($jsonParser->headers(), $expectedHeaders);
    }

    public function testGetReadingsFromStreamUseCaseWithCsvParser()
    {

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readings = $getReadingsFromStreamUseCase->execute(
            CsvParser::parse(new FileFetcher('2016-readings.csv', $this->testStorageFilesPath), ','),
        );

        foreach ($readings as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }
    }

    public function testGetReadingsFromStreamUseCaseWithXmlParser()
    {
        $source = new FileFetcher('2016-readings.xml', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readings = $getReadingsFromStreamUseCase->execute(XmlParser::parse($source));

        foreach ($readings as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }
    }

    public function testGetReadingsFromStreamUseCaseWithJsonParser()
    {
        $source = new FileFetcher('2016-readings.json', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readings = $getReadingsFromStreamUseCase->execute(JsonParser::parse($source));

        foreach ($readings as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }
    }

    public function testGetReadingsFromStreamUseCaseWithFactoryParser()
    {
        $xmlSource = new FileFetcher('2016-readings.xml', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readingsFromXml = $getReadingsFromStreamUseCase->execute(ParserFactory::parse($xmlSource));

        foreach ($readingsFromXml as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }

        $csvSource = new FileFetcher('2016-readings.csv', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readingsFromCsv = $getReadingsFromStreamUseCase->execute(ParserFactory::parse($csvSource));

        foreach ($readingsFromCsv as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }

        $jsonSource = new FileFetcher('2016-readings.json', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readingsFromJson = $getReadingsFromStreamUseCase->execute(ParserFactory::parse($jsonSource));

        foreach ($readingsFromJson as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
        }
    }

    public function testGetSuspiciousReadingsUseCase()
    {
        $source = new FileFetcher('2016-readings.xml', $this->testStorageFilesPath);

        $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

        $readings = $getReadingsFromStreamUseCase->execute(ParserFactory::parse($source));

        $getSuspiciousReadingsUseCase = new GetSuspiciousReadingsUseCase();

        $suspiciousReadings = $getSuspiciousReadingsUseCase->execute($readings);

        foreach ($suspiciousReadings as $reading) {
            $this->assertArrayHasKey('client', $reading);
            $this->assertArrayHasKey('period', $reading);
            $this->assertArrayHasKey('reading', $reading);
            $this->assertArrayHasKey('deviationFromMedianPercentage', $reading);
        }
    }
}