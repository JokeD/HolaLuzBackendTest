<?php

declare(strict_types = 1);


namespace App\Domain;


class XmlParser implements Parseable
{

    const TYPE = 'xml';

    private array $content = [];


    private function __construct(Sourceable $source)
    {
        foreach (simplexml_load_string($source->fetch())->reading as $xmlRow) {
            $this->content[] = [
                'client'  => (string)$xmlRow['clientID'],
                'period'  => (string)$xmlRow['period'],
                'reading' => (int)$xmlRow
            ];
        }
    }

    public static function parse(Sourceable $source): self
    {
        return new self($source);
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