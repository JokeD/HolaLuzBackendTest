<?php

declare(strict_types = 1);

namespace App\Domain;


class ParserFactory implements Parseable
{
    private static Parseable $parser;

    public static function parse(Sourceable $source): Parseable
    {
        self::$parser = match ($source->type()) {
            XmlParser::TYPE => XmlParser::parse($source),
            CsvParser::TYPE => CsvParser::parse($source),
            JsonParser::TYPE => JsonParser::parse($source),
            default => throw new \Exception('Not available parser for type: ' . $source->type()),
        };
        return self::$parser;
    }

    public function type(): string
    {
        return self::$parser->type();
    }

    public function content(): array
    {
        return self::$parser->content();
    }

    public function headers(): array
    {
        return self::$parser->headers();
    }
}