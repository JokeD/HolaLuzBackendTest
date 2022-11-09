<?php

declare(strict_types = 1);

namespace App\Domain;


class CsvParser implements Parseable
{

    const TYPE = 'csv';

    private Sourceable $source;
    private string $delimiter;
    private array $content = [];


    private function __construct(Sourceable $source, string $delimiter)
    {
        $this->source    = $source;
        $this->delimiter = $delimiter;

        $csvRows = explode(PHP_EOL, $this->source->fetch());

        foreach ($csvRows as $csvIndex => $csvRow) {
            $this->content[$csvIndex]            = array_combine(
                explode($this->delimiter, $csvRows[0]),
                str_getcsv($csvRow, $this->delimiter)
            );
            $this->content[$csvIndex]['reading'] = (int)$this->content[$csvIndex]['reading'];
        }
        array_splice($this->content, 0, 1);
    }

    public static function parse(Sourceable $source, string $delimiter = ','): self
    {
        return new self($source, $delimiter);
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function content(): array
    {
        return $this->content;
    }

    public function headers(): array
    {
        return array_keys($this->content[0]);
    }
}