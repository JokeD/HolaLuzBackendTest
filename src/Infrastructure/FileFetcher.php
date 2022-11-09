<?php


namespace App\Infrastructure;


use App\Domain\Sourceable;

class FileFetcher implements Sourceable
{
    private string $fileName;
    private string $path;

    const DEF_STORAGE_PATH = __DIR__ . '/../../storage/';

    public function __construct(string $fileName, string $path = self::DEF_STORAGE_PATH)
    {
        $this->fileName = $fileName;
        $this->path     = $path;
    }

    public function fetch(): string
    {
        return file_get_contents($this->path . $this->fileName);
    }

    public function type(): string
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }
}