<?php

declare(strict_types = 1);


namespace App\Domain;



class JsonParser implements Parseable
{

    const TYPE = 'json';

    private array $content;


    private function __construct(Sourceable $source)
    {
        $this->content = json_decode($source->fetch(), true);
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