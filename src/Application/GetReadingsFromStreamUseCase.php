<?php

declare(strict_types = 1);

namespace App\Application;

use App\Domain\Parseable;

class GetReadingsFromStreamUseCase
{

    public function execute(Parseable $parseable): array
    {
        return $parseable->content();
    }
}