<?php

declare(strict_types = 1);

namespace App\Domain;


interface Parseable
{
    public function type(): string;

    public function content(): array;

    public function headers(): array;
}